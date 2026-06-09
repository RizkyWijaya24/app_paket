@extends('layouts.app')
@section('title', isset($reseller) ? 'Edit Reseller' : 'Tambah Reseller')
@section('page-title', isset($reseller) ? 'Edit Reseller' : 'Tambah Reseller')
@section('page-subtitle', isset($reseller) ? 'Perbarui data reseller' : 'Daftarkan reseller baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ isset($reseller) ? route('resellers.update', $reseller) : route('resellers.store') }}">
            @csrf
            @if(isset($reseller)) @method('PUT') @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Reseller <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_reseller" required
                           value="{{ old('nama_reseller', $reseller->nama_reseller ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Contoh: Bunda Sari">
                    @error('nama_reseller') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. WhatsApp</label>
                    <input type="text" name="no_wa"
                           value="{{ old('no_wa', $reseller->no_wa ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="08xx-xxxx-xxxx">
                    @error('no_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                              placeholder="Alamat reseller...">{{ old('alamat', $reseller->alamat ?? '') }}</textarea>
                    @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1 py-3 text-white font-bold rounded-xl text-sm">
                    {{ isset($reseller) ? '💾 Perbarui Data' : '✅ Simpan Reseller' }}
                </button>
                <a href="{{ route('resellers.index') }}"
                   class="px-5 py-3 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-semibold text-center transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
