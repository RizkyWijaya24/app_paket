@extends('layouts.app')
@section('title', isset($packet) ? 'Edit Paket' : 'Tambah Paket')
@section('page-title', isset($packet) ? 'Edit Paket' : 'Tambah Paket Tabungan')
@section('page-subtitle', isset($packet) ? 'Perbarui data paket' : 'Buat paket tabungan baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ isset($packet) ? route('packets.update', $packet) : route('packets.store') }}">
            @csrf
            @if(isset($packet)) @method('PUT') @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_paket" required autofocus
                           value="{{ old('nama_paket', $packet->nama_paket ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Contoh: Paket A - Gold">
                    @error('nama_paket') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Setoran per Periode (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="setoran_wajib" required min="0" step="500"
                               value="{{ old('setoran_wajib', $packet->setoran_wajib ?? '') }}"
                               id="setoran_wajib"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                               placeholder="150000">
                        @error('setoran_wajib') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Periode <span class="text-red-500">*</span></label>
                        <input type="number" name="total_periode" required min="1"
                               value="{{ old('total_periode', $packet->total_periode ?? '') }}"
                               id="total_periode"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                               placeholder="10">
                        @error('total_periode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Preview Total --}}
                <div class="bg-primary-50 border border-primary-200 rounded-xl px-4 py-3">
                    <p class="text-xs text-primary-600 font-medium">Total nilai paket:</p>
                    <p class="text-xl font-bold text-primary-700" id="totalPreview">Rp 0</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                              placeholder="Deskripsi paket...">{{ old('keterangan', $packet->keterangan ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1 py-3 text-white font-bold rounded-xl text-sm">
                    {{ isset($packet) ? '💾 Perbarui Paket' : '✅ Simpan Paket' }}
                </button>
                <a href="{{ route('packets.index') }}"
                   class="px-5 py-3 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-semibold text-center transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePreview() {
    const s = parseFloat(document.getElementById('setoran_wajib').value) || 0;
    const p = parseInt(document.getElementById('total_periode').value) || 0;
    document.getElementById('totalPreview').textContent = 'Rp ' + (s * p).toLocaleString('id-ID');
}
document.getElementById('setoran_wajib').addEventListener('input', updatePreview);
document.getElementById('total_periode').addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
