<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPacket;
use App\Models\Packet;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['reseller', 'customerPackets.packet'])
            ->latest()
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $resellers = Reseller::orderBy('nama_reseller')->get();
        $packets   = Packet::orderBy('nama_paket')->get();

        return view('customers.create', compact('resellers', 'packets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reseller_id'          => 'required|exists:resellers,id',
            'nama_customer'        => 'required|string|max:100',
            'no_wa'                => 'nullable|string|max:20',
            'alamat'               => 'nullable|string|max:255',
            'packets'              => 'required|array|min:1',
            'packets.*.packet_id'  => 'required|exists:packets,id',
            'packets.*.kuantitas'  => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $customer = Customer::create([
                'reseller_id'   => $validated['reseller_id'],
                'nama_customer' => $validated['nama_customer'],
                'no_wa'         => $validated['no_wa'] ?? null,
                'alamat'        => $validated['alamat'] ?? null,
            ]);

            // Simpan setiap paket yang dipilih ke customer_packets
            foreach ($validated['packets'] as $packetData) {
                CustomerPacket::create([
                    'customer_id' => $customer->id,
                    'packet_id'   => $packetData['packet_id'],
                    'kuantitas'   => $packetData['kuantitas'],
                    'status'      => 'aktif',
                ]);
            }
        });

        return redirect()->route('customers.index')
            ->with('success', 'Nasabah berhasil didaftarkan dengan ' . count($validated['packets']) . ' paket!');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'reseller',
            'customerPackets.packet',
            'customerPackets.savingsLedgers' => fn($q) => $q->latest('tanggal_setor'),
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $resellers = Reseller::orderBy('nama_reseller')->get();
        $customer->load('customerPackets');

        return view('customers.edit', compact('customer', 'resellers'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'reseller_id'    => 'required|exists:resellers,id',
            'nama_customer'  => 'required|string|max:100',
            'no_wa'          => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Data nasabah berhasil diperbarui!');
    }

    /**
     * Tambah paket baru ke nasabah yang sudah ada.
     */
    public function addPacket(Request $request, Customer $customer)
    {
        $request->validate([
            'packet_id'  => 'required|exists:packets,id',
            'kuantitas'  => 'required|integer|min:1',
        ]);

        CustomerPacket::create([
            'customer_id' => $customer->id,
            'packet_id'   => $request->packet_id,
            'kuantitas'   => $request->kuantitas,
            'status'      => 'aktif',
        ]);

        return back()->with('success', 'Paket berhasil ditambahkan ke nasabah!');
    }

    /**
     * Update status customer_packet (aktif/lunas/batal).
     */
    public function updatePacketStatus(Request $request, CustomerPacket $customerPacket)
    {
        $request->validate([
            'status' => 'required|in:aktif,lunas,batal',
        ]);

        $customerPacket->update(['status' => $request->status]);

        return back()->with('success', 'Status paket berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Data nasabah berhasil dihapus.');
    }
}
