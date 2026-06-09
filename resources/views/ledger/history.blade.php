@extends('layouts.app')
@section('title', 'Riwayat Setoran — ' . $customerPacket->customer->nama_customer)
@section('page-title', 'Riwayat Setoran')
@section('page-subtitle', $customerPacket->customer->nama_customer . ' · Paket: ' . $customerPacket->packet->nama_paket)

@section('header-actions')
    <a href="{{ route('customers.show', $customerPacket->customer) }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition-all">
        ⬅️ Kembali ke Detail Nasabah
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Detail Paket & Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Card 1: Paket Tabungan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Paket Tabungan</p>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                    {{ $customerPacket->packet->nama_paket }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Rp {{ number_format($customerPacket->packet->setoran_wajib, 0, ',', '.') }} × {{ $customerPacket->packet->total_periode }} Periode
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center">
                <span class="text-xs text-gray-500">Jumlah Unit:</span>
                <span class="text-sm font-bold text-primary-600 bg-primary-50 px-2.5 py-0.5 rounded-lg">
                    {{ $customerPacket->kuantitas }} Unit
                </span>
            </div>
        </div>

        {{-- Card 2: Status & Progress --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Status & Kemajuan</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                        {{ $customerPacket->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $customerPacket->status === 'lunas' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $customerPacket->status === 'batal' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ strtoupper($customerPacket->status) }}
                    </span>
                    <span class="text-sm font-bold text-gray-700">
                        {{ $customerPacket->progress_persen }}%
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <div class="bg-gray-100 rounded-full h-2 w-full">
                    <div class="h-2 rounded-full {{ $customerPacket->progress_persen >= 100 ? 'bg-emerald-500' : 'bg-primary-500' }}"
                         style="width: {{ $customerPacket->progress_persen }}%"></div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Terkumpul --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Total Terkumpul</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">
                    Rp {{ number_format($customerPacket->total_setoran, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between text-xs text-gray-500">
                <span>Target:</span>
                <span class="font-semibold text-gray-700">Rp {{ number_format($customerPacket->target_total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Card 4: Sisa Setoran --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Sisa Kekurangan</p>
                <h3 class="text-2xl font-bold text-rose-500 mt-1">
                    Rp {{ number_format($customerPacket->sisa_setoran, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between text-xs text-gray-500">
                <span>Periode Terbayar:</span>
                <span class="font-semibold text-gray-700">{{ $customerPacket->periode_terbayar }} / {{ $customerPacket->packet->total_periode }}</span>
            </div>
        </div>
    </div>

    {{-- Detail Nasabah --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Informasi Nasabah</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <span class="text-gray-400 block mb-1">Nama Lengkap</span>
                <span class="font-semibold text-gray-800">{{ $customerPacket->customer->nama_customer }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Reseller</span>
                <span class="font-semibold text-gray-800">
                    <span class="px-2 py-0.5 bg-gray-100 rounded text-xs text-gray-600 font-medium mr-1.5">Reseller</span>
                    {{ $customerPacket->customer->reseller->nama_reseller }}
                </span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">No. WhatsApp</span>
                @if($customerPacket->customer->no_wa)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customerPacket->customer->no_wa) }}" 
                       target="_blank" 
                       class="font-semibold text-primary-600 hover:underline flex items-center gap-1">
                        {{ $customerPacket->customer->no_wa }} ↗️
                    </a>
                @else
                    <span class="text-gray-400 italic">Tidak ada WhatsApp</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat Setoran --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900">Seluruh Riwayat Setoran</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar setoran nasabah untuk paket ini (diurutkan dari yang terbaru)</p>
            </div>
            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">
                {{ $customerPacket->savingsLedgers->count() }} Setoran
            </span>
        </div>

        @if($customerPacket->savingsLedgers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase text-xs">
                        <th class="py-3.5 px-6 text-left font-semibold">No</th>
                        <th class="py-3.5 px-6 text-left font-semibold">Tanggal Setor</th>
                        <th class="py-3.5 px-6 text-right font-semibold">Jumlah Setoran</th>
                        <th class="py-3.5 px-6 text-left font-semibold pl-12">Keterangan</th>
                        <th class="py-3.5 px-6 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customerPacket->savingsLedgers as $index => $ledger)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6 text-gray-500">
                            {{ $customerPacket->savingsLedgers->count() - $index }}
                        </td>
                        <td class="py-4 px-6 text-gray-700 font-medium">
                            {{ $ledger->tanggal_setor->format('d F Y') }}
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-emerald-600">
                            Rp {{ number_format($ledger->jumlah_setoran, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 pl-12 text-gray-500 text-xs">
                            {{ $ledger->keterangan ?? '—' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <form action="{{ route('ledger.destroy', $ledger) }}" method="POST"
                                  onsubmit="return confirm('Hapus catatan setoran senilai Rp {{ number_format($ledger->jumlah_setoran, 0, ',', '.') }} ini?')"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                        title="Hapus setoran">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="text-4xl mb-3">📭</div>
            <h4 class="font-bold text-gray-900 text-sm">Belum ada riwayat setoran</h4>
            <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Setoran dapat dimasukkan melalui menu Input Setoran Massal.</p>
            <div class="mt-4">
                <a href="{{ route('ledger.bulk-input', ['reseller_id' => $customerPacket->customer->reseller_id]) }}"
                   class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl inline-block">
                    ✍️ Input Setoran Sekarang
                </a>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
