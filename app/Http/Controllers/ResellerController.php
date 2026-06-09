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
}
