@extends('layouts.app')
@section('title', 'Data Nasabah')
@section('page-title', 'Data Nasabah')
@section('page-subtitle', 'Daftar semua nasabah yang terdaftar')

@section('header-actions')
    <a href="{{ route('customers.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Daftar Nasabah Baru
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Nasabah</th>
                    <th class="px-6 py-3 text-left">Reseller</th>
                    <th class="px-6 py-3 text-left">Paket yang Diambil</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($customers as $i => $customer)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $customers->firstItem() + $i }}</td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $customer->nama_customer }}</p>
                        <p class="text-xs text-gray-400">{{ $customer->no_wa ?? '—' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                            {{ $customer->reseller->nama_reseller }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($customer->customerPackets as $cp)
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $cp->status === 'aktif' ? 'bg-primary-50 text-primary-700' : '' }}
                                {{ $cp->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $cp->status === 'batal' ? 'bg-gray-100 text-gray-500 line-through' : '' }}">
                                {{ $cp->packet->nama_paket }}
                                @if($cp->kuantitas > 1) ×{{ $cp->kuantitas }} @endif
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('customers.show', $customer) }}"
                               class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-medium hover:bg-blue-100 transition-colors">
                                Detail
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}"
                               class="text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                  onsubmit="return confirm('Hapus nasabah {{ $customer->nama_customer }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-medium hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                        <p class="text-4xl mb-3">👤</p>
                        <p class="font-medium">Belum ada nasabah terdaftar</p>
                        <a href="{{ route('customers.create') }}" class="text-primary-600 hover:underline text-sm mt-1 inline-block">Daftarkan nasabah pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
</div>
@endsection
