@extends('layouts.app')
@section('title', 'Daftarkan Nasabah Baru')
@section('page-title', 'Daftarkan Nasabah Baru')
@section('page-subtitle', 'Isi data nasabah dan pilih paket yang akan diikuti')

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('customers.store') }}" id="customerForm">
    @csrf

    <div class="space-y-5">
        {{-- ===== DATA NASABAH ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary-500 text-white text-xs rounded-full flex items-center justify-center font-bold">1</span>
                Data Nasabah
            </h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Nasabah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_customer" required
                           value="{{ old('nama_customer') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Nama lengkap nasabah">
                    @error('nama_customer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reseller <span class="text-red-500">*</span></label>
                        <select name="reseller_id" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                            <option value="">— Pilih Reseller —</option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}" {{ old('reseller_id') == $reseller->id ? 'selected' : '' }}>
                                    {{ $reseller->nama_reseller }}
                                </option>
                            @endforeach
                        </select>
                        @error('reseller_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No. WhatsApp</label>
                        <input type="text" name="no_wa"
                               value="{{ old('no_wa') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                               placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                              placeholder="Alamat nasabah (opsional)">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===== PILIH PAKET ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-primary-500 text-white text-xs rounded-full flex items-center justify-center font-bold">2</span>
                    Pilih Paket <span class="text-red-500">*</span>
                </h2>
                <button type="button" id="addPacketBtn"
                    class="text-sm text-primary-600 border border-primary-300 hover:bg-primary-50 px-3 py-1.5 rounded-lg font-medium transition-colors">
                    + Tambah Paket
                </button>
            </div>

            <div id="packetRows" class="space-y-3">
                {{-- Row paket pertama (default) --}}
                <div class="packet-row flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                    <div class="flex-1">
                        <select name="packets[0][packet_id]" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                            <option value="">— Pilih Paket —</option>
                            @foreach($packets as $packet)
                                <option value="{{ $packet->id }}"
                                    data-setoran="{{ $packet->setoran_wajib }}"
                                    data-periode="{{ $packet->total_periode }}">
                                    {{ $packet->nama_paket }} (Rp {{ number_format($packet->setoran_wajib,0,',','.') }} × {{ $packet->total_periode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-28">
                        <input type="number" name="packets[0][kuantitas]" value="1" min="1" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-400"
                               placeholder="Unit">
                    </div>
                    <div class="text-xs text-gray-400 w-16 text-right">unit</div>
                    <button type="button" class="remove-packet text-red-400 hover:text-red-600 transition-colors hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3">💡 Nasabah bisa mengambil lebih dari 1 paket secara bersamaan.</p>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="btn-primary flex-1 py-3.5 text-white font-bold rounded-xl text-sm">
                ✅ Daftarkan Nasabah
            </button>
            <a href="{{ route('customers.index') }}"
               class="px-6 py-3.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-semibold text-center">
                Batal
            </a>
        </div>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
    // Template untuk row paket tambahan
    const packetOptions = `
        @foreach($packets as $packet)
            <option value="{{ $packet->id }}" data-setoran="{{ $packet->setoran_wajib }}" data-periode="{{ $packet->total_periode }}">
                {{ $packet->nama_paket }} (Rp {{ number_format($packet->setoran_wajib,0,',','.') }} × {{ $packet->total_periode }})
            </option>
        @endforeach
    `;

    let rowIndex = 1;

    document.getElementById('addPacketBtn').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'packet-row flex items-center gap-3 bg-gray-50 rounded-xl p-3';
        row.innerHTML = `
            <div class="flex-1">
                <select name="packets[${rowIndex}][packet_id]" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                    <option value="">— Pilih Paket —</option>
                    ${packetOptions}
                </select>
            </div>
            <div class="w-28">
                <input type="number" name="packets[${rowIndex}][kuantitas]" value="1" min="1" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="Unit">
            </div>
            <div class="text-xs text-gray-400 w-16 text-right">unit</div>
            <button type="button" class="remove-packet text-red-400 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        document.getElementById('packetRows').appendChild(row);
        row.querySelector('.remove-packet').addEventListener('click', () => row.remove());
        rowIndex++;
    });
</script>
@endpush
