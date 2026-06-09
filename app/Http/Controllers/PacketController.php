<?php

namespace App\Http\Controllers;

use App\Models\Packet;
use Illuminate\Http\Request;

class PacketController extends Controller
{
    public function index()
    {
        $packets = Packet::withCount('customerPackets')->latest()->paginate(15);
        return view('packets.index', compact('packets'));
    }

    public function create()
    {
        return view('packets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket'     => 'required|string|max:100',
            'setoran_wajib'  => 'required|numeric|min:0',
            'total_periode'  => 'required|integer|min:1',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        Packet::create($validated);

        return redirect()->route('packets.create')
            ->with('success', 'Paket berhasil ditambahkan!');
    }

    public function edit(Packet $packet)
    {
        return view('packets.edit', compact('packet'));
    }

    public function update(Request $request, Packet $packet)
    {
        $validated = $request->validate([
            'nama_paket'     => 'required|string|max:100',
            'setoran_wajib'  => 'required|numeric|min:0',
            'total_periode'  => 'required|integer|min:1',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        $packet->update($validated);

        return redirect()->route('packets.index')
            ->with('success', 'Data paket berhasil diperbarui!');
    }

    public function destroy(Packet $packet)
    {
        if ($packet->customerPackets()->exists()) {
            return back()->with('error', 'Paket tidak bisa dihapus karena sudah ada nasabah yang terdaftar!');
        }

        $packet->delete();

        return redirect()->route('packets.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
