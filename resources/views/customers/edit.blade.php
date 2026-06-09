@extends('layouts.app')
@section('title', 'Edit Nasabah')
@section('page-title', 'Edit Data Nasabah')

@section('content')
<div class="max-w-lg">
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Nasabah <span class="text-red-500">*</span></label>
                <input type="text" name="nama_customer" required
                       value="{{ old('nama_customer', $customer->nama_customer) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reseller <span class="text-red-500">*</span></label>
                <select name="reseller_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                    @foreach($resellers as $reseller)
                        <option value="{{ $reseller->id }}" {{ $customer->reseller_id == $reseller->id ? 'selected' : '' }}>
                            {{ $reseller->nama_reseller }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">No. WhatsApp</label>
                <input type="text" name="no_wa"
                       value="{{ old('no_wa', $customer->no_wa) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                <textarea name="alamat" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">{{ old('alamat', $customer->alamat) }}</textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-8">
            <button type="submit" class="btn-primary flex-1 py-3 text-white font-bold rounded-xl text-sm">💾 Perbarui</button>
            <a href="{{ route('customers.show', $customer) }}"
               class="px-5 py-3 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold text-center">Batal</a>
        </div>
    </form>
</div>
</div>
@endsection
