@extends('layouts.app')
@section('title', 'Detail Nasabah — '.$customer->nama_customer)
@section('page-title', $customer->nama_customer)
@section('page-subtitle', 'Reseller: '.$customer->reseller->nama_reseller.' · Terdaftar '.($customer->created_at->diffForHumans()))

@section('header-actions')
    <a href="{{ route('customers.edit', $customer) }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition-all">
        ✏️ Edit Data
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Info Dasar --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Reseller</p>
            <p class="font-bold text-gray-900">{{ $customer->reseller->nama_reseller }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">WhatsApp</p>
            <p class="font-bold text-gray-900">{{ $customer->no_wa ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Alamat</p>
            <p class="font-bold text-gray-900">{{ $customer->alamat ?? '—' }}</p>
        </div>
    </div>

    {{-- Tambah Paket Baru --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-gray-900">Tambah Paket ke Nasabah Ini</h2>
        </div>
        <form action="{{ route('customers.add-packet', $customer) }}" method="POST" class="flex items-center gap-3 flex-wrap">
            @csrf
            <select name="packet_id" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                <option value="">— Pilih Paket —</option>
                @foreach(\App\Models\Packet::all() as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_paket }}</option>
                @endforeach
            </select>
            <input type="number" name="kuantitas" value="1" min="1"
                   class="w-24 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-400"
                   placeholder="Unit">
            <button type="submit" class="btn-primary text-white text-sm font-semibold px-4 py-2.5 rounded-xl">
                + Tambah Paket
            </button>
        </form>
    </div>

    {{-- Daftar Paket & Riwayat Setoran --}}
    @foreach($customer->customerPackets as $cp)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header Paket --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-lg">
                    {{ $cp->is_bebas ? '💵' : '📦' }}
                </div>
                <div>
                    <p class="font-bold text-gray-900">{{ $cp->packet->nama_paket }}
                        @if($cp->kuantitas > 1) <span class="text-primary-500">×{{ $cp->kuantitas }}</span> @endif
                    </p>
                    <p class="text-xs text-gray-500">
                        @if($cp->isBebas)
                            Tabungan Uang · Setoran Bebas/Fleksibel
                        @else
                            Rp {{ number_format($cp->packet->setoran_wajib, 0, ',', '.') }} / periode ·
                            {{ $cp->packet->total_periode }} periode
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status Badge --}}
                <span class="text-xs font-semibold px-3 py-1 rounded-full
                    {{ $cp->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $cp->status === 'lunas' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $cp->status === 'batal' ? 'bg-gray-100 text-gray-600' : '' }}">
                    {{ strtoupper($cp->status) }}
                </span>

                {{-- Update Status --}}
                <form action="{{ route('customer-packets.update-status', $cp) }}" method="POST" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-primary-400">
                        <option {{ $cp->status === 'aktif' ? 'selected' : '' }} value="aktif">Aktif</option>
                        <option {{ $cp->status === 'lunas' ? 'selected' : '' }} value="lunas">Lunas</option>
                        <option {{ $cp->status === 'batal' ? 'selected' : '' }} value="batal">Batal</option>
                    </select>
                </form>

                <a href="{{ route('ledger.history', $cp) }}"
                   class="text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-medium transition-colors">
                    Riwayat Lengkap
                </a>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="px-6 py-4 bg-gray-50">
            @if($cp->isBebas)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700">Total Terkumpul: <strong class="text-emerald-600 text-base">Rp {{ number_format($cp->total_setoran, 0, ',', '.') }}</strong></span>
                    <span class="text-xs bg-amber-100 text-amber-800 font-semibold px-3 py-1.5 rounded-xl border border-amber-200">
                        💰 Tabungan Uang Bebas (Setoran Fleksibel)
                    </span>
                </div>
            @else
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Total Terkumpul: <strong>Rp {{ number_format($cp->total_setoran, 0, ',', '.') }}</strong></span>
                    <span class="text-gray-600">Target: <strong>Rp {{ number_format($cp->target_total, 0, ',', '.') }}</strong></span>
                    <span class="{{ $cp->progress_persen >= 100 ? 'text-emerald-600' : 'text-primary-600' }} font-bold">{{ $cp->progress_persen }}%</span>
                </div>
                <div class="bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $cp->progress_persen >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-primary-400 to-primary-600' }}"
                         style="width: {{ $cp->progress_persen }}%"></div>
                </div>
            @endif
        </div>

        {{-- 5 Setoran Terakhir --}}
        @if($cp->savingsLedgers->count() > 0)
        <div class="px-6 pb-4">
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-400 uppercase">
                    <tr>
                        <th class="py-2 text-left">Tanggal</th>
                        <th class="py-2 text-right">Jumlah</th>
                        <th class="py-2 text-left pl-4">Keterangan</th>
                        <th class="py-2 text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cp->savingsLedgers->take(5) as $ledger)
                    <tr>
                        <td class="py-2 text-gray-600">{{ $ledger->tanggal_setor->format('d M Y') }}</td>
                        <td class="py-2 text-right font-semibold text-emerald-600">
                            +Rp {{ number_format($ledger->jumlah_setoran, 0, ',', '.') }}
                        </td>
                        <td class="py-2 pl-4 text-gray-400 text-xs">{{ $ledger->keterangan ?? '—' }}</td>
                        <td class="py-2 text-center">
                            <form action="{{ route('ledger.destroy', $ledger) }}" method="POST"
                                  onsubmit="return confirm('Hapus catatan setoran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs transition-colors">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($cp->savingsLedgers->count() > 5)
            <a href="{{ route('ledger.history', $cp) }}" class="text-xs text-primary-600 hover:underline mt-2 inline-block">
                Lihat semua {{ $cp->savingsLedgers->count() }} setoran →
            </a>
            @endif
        </div>
        @endif
    </div>
    @endforeach

</div>
@endsection
