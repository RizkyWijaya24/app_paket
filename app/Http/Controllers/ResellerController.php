<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::withCount('customers')->latest()->paginate(15);
        return view('resellers.index', compact('resellers'));
    }

    public function create()
    {
        return view('resellers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_reseller' => 'required|string|max:100',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:255',
        ]);

        Reseller::create($validated);

        return redirect()->route('resellers.index')
            ->with('success', 'Reseller berhasil ditambahkan!');
    }

    public function edit(Reseller $reseller)
    {
        return view('resellers.edit', compact('reseller'));
    }

    public function update(Request $request, Reseller $reseller)
    {
        $validated = $request->validate([
            'nama_reseller' => 'required|string|max:100',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:255',
        ]);

        $reseller->update($validated);

        return redirect()->route('resellers.index')
            ->with('success', 'Data reseller berhasil diperbarui!');
    }

    public function destroy(Reseller $reseller)
    {
        if ($reseller->customers()->exists()) {
            return back()->with('error', 'Reseller tidak bisa dihapus karena masih memiliki nasabah!');
        }

        $reseller->delete();

        return redirect()->route('resellers.index')
            ->with('success', 'Reseller berhasil dihapus.');
    }

    /**
     * Ekspor data tabungan reseller ke Excel (.xls)
     */
    public function export(Reseller $reseller)
    {
        $reseller->load([
            'customers.customerPackets.packet',
            'customers.customerPackets.savingsLedgers'
        ]);

        // Bersihkan nama reseller dari karakter non-alphanumeric untuk filename
        $cleanResellerName = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $reseller->nama_reseller));
        $fileName = 'Laporan_Tabungan_' . $cleanResellerName . '_' . date('Ymd_His') . '.xls';

        // Render view ke string HTML XLS
        $html = view('resellers.export_excel', compact('reseller'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'max-age=0');
    }
}
