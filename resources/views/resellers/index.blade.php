@extends('layouts.app')
@section('title', 'Data Reseller')
@section('page-title', 'Data Reseller')
@section('page-subtitle', 'Kelola daftar reseller tabungan paket Lebaran')

@section('header-actions')
    <a href="{{ route('resellers.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Reseller
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Nama Reseller</th>
                    <th class="px-6 py-3 text-left">No. WhatsApp</th>
                    <th class="px-6 py-3 text-left">Alamat</th>
                    <th class="px-6 py-3 text-center">Nasabah</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($resellers as $i => $reseller)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $resellers->firstItem() + $i }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                                {{ strtoupper(substr($reseller->nama_reseller, 0, 1)) }}
                            </div>
                            <p class="font-semibold text-gray-900">{{ $reseller->nama_reseller }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($reseller->no_wa)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reseller->no_wa) }}" target="_blank"
                               class="text-emerald-600 hover:underline flex items-center gap-1">
                                📱 {{ $reseller->no_wa }}
                            </a>
                        @else — @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $reseller->alamat ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                            {{ $reseller->customers_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('ledger.bulk-input', ['reseller_id' => $reseller->id]) }}"
                               class="text-xs bg-primary-50 text-primary-700 px-3 py-1.5 rounded-lg font-medium hover:bg-primary-100 transition-colors">
                                Input Setoran
                            </a>
                            <a href="{{ route('resellers.edit', $reseller) }}"
                               class="text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('resellers.destroy', $reseller) }}" method="POST"
                                   onsubmit="return confirm('Hapus reseller {{ $reseller->nama_reseller }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <p class="text-4xl mb-3">👥</p>
                        <p class="font-medium">Belum ada reseller</p>
                        <a href="{{ route('resellers.create') }}" class="text-primary-600 hover:underline text-sm mt-1 inline-block">Tambah reseller pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $resellers->links() }}
    </div>
</div>
@endsection
