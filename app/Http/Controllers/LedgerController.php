<?php

namespace App\Http\Controllers;

use App\Models\CustomerPacket;
use App\Models\Reseller;
use App\Models\SavingsLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    /**
     * Tampilkan halaman Setoran Massal.
     * Owner memilih Reseller → tampil semua customer_packets nasabahnya.
     */
    public function bulkInput(Request $request)
    {
        $resellers      = Reseller::orderBy('nama_reseller')->get();
        $selectedReseller = null;
        $customerPackets  = collect();

        if ($request->filled('reseller_id')) {
            $selectedReseller = Reseller::findOrFail($request->reseller_id);

            // Ambil semua customer_packets dari nasabah reseller ini
            // Hanya yang berstatus AKTIF
            $customerPackets = CustomerPacket::with(['customer', 'packet', 'savingsLedgers'])
                ->whereHas('customer', fn($q) => $q->where('reseller_id', $request->reseller_id))
                ->where('status', 'aktif')
                ->orderBy('customer_id')
                ->get();
        }

        return view('ledger.bulk-input', compact(
            'resellers',
            'selectedReseller',
            'customerPackets',
        ));
    }

    /**
     * PROSES SETORAN MASSAL — Inti dari fitur ini.
     *
     * Menerima array input:
     *   setoran[{customer_packet_id}][jumlah] = 150000
     *   setoran[{customer_packet_id}][keterangan] = "..."
     *
     * Hanya menyimpan baris yang jumlahnya > 0.
     * Menggunakan DB transaction untuk atomicity.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'reseller_id'           => 'required|exists:resellers,id',
            'tanggal_setor'         => 'required|date',
            'setoran'               => 'required|array',
            'setoran.*.jumlah'      => 'nullable|numeric|min:0',
            'setoran.*.keterangan'  => 'nullable|string|max:255',
        ]);

        $setoranData    = $request->input('setoran', []);
        $tanggalSetor   = $request->input('tanggal_setor');
        $savedCount     = 0;
        $skippedCount   = 0;

        // Validasi bahwa customer_packet_id milik reseller yang dipilih
        $validIds = CustomerPacket::whereHas(
            'customer',
            fn($q) => $q->where('reseller_id', $request->reseller_id)
        )->where('status', 'aktif')->pluck('id')->toArray();

        DB::transaction(function () use ($setoranData, $tanggalSetor, $validIds, &$savedCount, &$skippedCount) {
            $insertData = [];

            foreach ($setoranData as $customerPacketId => $data) {
                $jumlah = (float) ($data['jumlah'] ?? 0);

                // Skip jika jumlah 0 atau id tidak valid (security check)
                if ($jumlah <= 0 || !in_array((int) $customerPacketId, $validIds)) {
                    $skippedCount++;
                    continue;
                }

                $insertData[] = [
                    'customer_packet_id' => (int) $customerPacketId,
                    'jumlah_setoran'     => $jumlah,
                    'tanggal_setor'      => $tanggalSetor,
                    'keterangan'         => $data['keterangan'] ?? null,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
                $savedCount++;
            }

            // Bulk insert semua setoran sekaligus (efisien, 1 query)
            if (!empty($insertData)) {
                SavingsLedger::insert($insertData);
            }
        });

        if ($savedCount === 0) {
            return back()
                ->with('warning', 'Tidak ada setoran yang disimpan. Pastikan ada nominal yang diisi.')
                ->withInput();
        }

        return redirect()->route('ledger.bulk-input', ['reseller_id' => $request->reseller_id])
            ->with('success', "✅ Berhasil menyimpan {$savedCount} setoran! ({$skippedCount} baris dilewati karena kosong)");
    }

    /**
     * Riwayat setoran untuk satu customer_packet.
     */
    public function history(CustomerPacket $customerPacket)
    {
        $customerPacket->load(['customer.reseller', 'packet', 'savingsLedgers' => fn($q) => $q->latest('tanggal_setor')]);

        return view('ledger.history', compact('customerPacket'));
    }

    /**
     * Hapus satu record setoran.
     */
    public function destroy(SavingsLedger $savingsLedger)
    {
        $resellerId = $savingsLedger->customerPacket->customer->reseller_id;
        $savingsLedger->delete();

        return redirect()->route('ledger.bulk-input', ['reseller_id' => $resellerId])
            ->with('success', 'Catatan setoran berhasil dihapus.');
    }
}
