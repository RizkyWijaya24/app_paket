@extends('layouts.app')
@section('title', 'Paket Tabungan')
@section('page-title', 'Paket Tabungan')
@section('page-subtitle', 'Kelola jenis-jenis paket tabungan Lebaran')

@section('header-actions')
    <a href="{{ route('packets.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Paket
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($packets as $packet)
    <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-br from-primary-400 to-primary-600 px-6 py-5 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-primary-100 text-xs font-medium uppercase tracking-wider">Paket</p>
                    <h3 class="text-xl font-bold mt-1">{{ $packet->nama_paket }}</h3>
                </div>
                <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $packet->customer_packets_count }} nasabah
                </span>
            </div>
        </div>
        <div class="px-6 py-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-500 text-sm">Setoran / Periode</span>
                <span class="font-bold text-gray-900">Rp {{ number_format($packet->setoran_wajib, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-500 text-sm">Total Periode</span>
                <span class="font-semibold text-gray-700">{{ $packet->total_periode }}× setoran</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                <span class="text-gray-500 text-sm font-medium">Total Nilai Paket</span>
                <span class="font-bold text-primary-600 text-lg">
                    Rp {{ number_format($packet->setoran_wajib * $packet->total_periode, 0, ',', '.') }}
                </span>
            </div>
            @if($packet->keterangan)
            <p class="text-gray-400 text-xs bg-gray-50 rounded-lg px-3 py-2">{{ $packet->keterangan }}</p>
            @endif
        </div>
        <div class="px-6 pb-4 flex gap-2">
            <a href="{{ route('packets.edit', $packet) }}"
               class="flex-1 text-center text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-lg font-medium transition-colors">
                ✏️ Edit
            </a>
            <form action="{{ route('packets.destroy', $packet) }}" method="POST"
                  onsubmit="return confirm('Hapus paket {{ $packet->nama_paket }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-lg font-medium transition-colors">
                    🗑️ Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <p class="text-5xl mb-3">📦</p>
        <p class="font-medium text-gray-700">Belum ada paket</p>
        <a href="{{ route('packets.create') }}" class="inline-block mt-3 text-primary-600 hover:underline text-sm">Buat paket pertama →</a>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $packets->links() }}</div>
@endsection
