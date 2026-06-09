@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan kondisi tabungan paket Lebaran')

@section('header-actions')
    <a href="{{ route('ledger.bulk-input') }}"
       class="btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Input Setoran
    </a>
@endsection

@section('content')

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- Total Dana --}}
        <div class="card-hover bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-primary-100 text-sm font-medium">Total Dana Terkumpul</p>
                    <p class="text-3xl font-bold mt-1">{{ 'Rp ' . number_format($totalDanaGlobal, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">💰</div>
            </div>
            <p class="text-primary-200 text-xs mt-3">Semua reseller gabungan</p>
        </div>

        {{-- Total Nasabah --}}
        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Nasabah</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalNasabah) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-2xl">👥</div>
            </div>
            <a href="{{ route('customers.create') }}" class="text-blue-600 text-xs font-medium hover:underline mt-3 inline-block">+ Tambah nasabah →</a>
        </div>

        {{-- Paket Aktif --}}
        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Paket Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalPaketAktif) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-2xl">📦</div>
            </div>
            <p class="text-gray-400 text-xs mt-3">Sedang berjalan</p>
        </div>

        {{-- Paket Lunas --}}
        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Paket Lunas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalPaketLunas) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-2xl">✅</div>
            </div>
            <p class="text-gray-400 text-xs mt-3">Sudah selesai</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- ===== TABEL RESELLER ===== --}}
        <div class="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Dana Per Reseller</h2>
                <a href="{{ route('resellers.index') }}" class="text-primary-600 text-sm hover:underline font-medium">Kelola →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Reseller</th>
                            <th class="px-6 py-3 text-center">Nasabah</th>
                            <th class="px-6 py-3 text-right">Total Dana</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($resellerStats as $reseller)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-sm">
                                        {{ strtoupper(substr($reseller->nama_reseller, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $reseller->nama_reseller }}</p>
                                        <p class="text-gray-400 text-xs">{{ $reseller->no_wa ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    {{ $reseller->customers_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 text-sm">
                                Rp {{ number_format($reseller->total_dana, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ledger.bulk-input', ['reseller_id' => $reseller->id]) }}"
                                   class="text-xs bg-primary-50 text-primary-700 px-3 py-1 rounded-full font-medium hover:bg-primary-100 transition-colors">
                                    Input Setoran
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-3xl mb-2">📋</p>
                                <p>Belum ada reseller. <a href="{{ route('resellers.create') }}" class="text-primary-600 hover:underline">Tambah sekarang</a></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== SETORAN TERBARU ===== --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Setoran Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($setoranTerbaru as $setoran)
                <div class="px-6 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $setoran->customerPacket->customer->nama_customer }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ $setoran->customerPacket->packet->nama_paket }}
                                · {{ $setoran->tanggal_setor->format('d M Y') }}
                            </p>
                        </div>
                        <p class="text-sm font-bold text-emerald-600 whitespace-nowrap flex-shrink-0">
                            +Rp {{ number_format($setoran->jumlah_setoran, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-400">
                    <p class="text-3xl mb-2">📭</p>
                    <p class="text-sm">Belum ada setoran</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
