@extends('layouts.app')

@section('title', 'Input Setoran Massal')
@section('page-title', 'Input Setoran Massal')
@section('page-subtitle', 'Input pembayaran banyak nasabah sekaligus dalam satu klik')

@section('content')
<div class="space-y-6">

    {{-- ===== FORM PILIH RESELLER ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('ledger.bulk-input') }}" id="filterForm">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🏪 Pilih Reseller
                    </label>
                    <select name="reseller_id" id="reseller_id" onchange="this.form.submit()"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-gray-50">
                        <option value="">— Pilih Reseller —</option>
                        @foreach($resellers as $reseller)
                            <option value="{{ $reseller->id }}" {{ (isset($selectedReseller) && $selectedReseller->id == $reseller->id) ? 'selected' : '' }}>
                                {{ $reseller->nama_reseller }} ({{ $reseller->customers_count ?? $reseller->customers->count() }} nasabah)
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($selectedReseller)
                <div class="flex items-center gap-3 bg-primary-50 border border-primary-200 rounded-xl px-4 py-2.5 text-sm text-primary-800">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-semibold">{{ $selectedReseller->nama_reseller }}</span>
                    <span class="text-primary-600">— {{ $customerPackets->count() }} paket aktif</span>
                </div>
                <a href="{{ route('resellers.export', $selectedReseller) }}"
                   download="Laporan_Tabungan_{{ preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $selectedReseller->nama_reseller)) }}_{{ date('Ymd_His') }}.xls"
                   class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-800 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all inline-flex items-center gap-1.5 shadow-sm">
                    📊 Ekspor Excel
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== FORM SETORAN MASSAL ===== --}}
    @if($selectedReseller && $customerPackets->count() > 0)

    <form method="POST" action="{{ route('ledger.bulk-store') }}" id="bulkForm">
        @csrf
        <input type="hidden" name="reseller_id" value="{{ $selectedReseller->id }}">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header Form --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-gray-900">
                        Daftar Nasabah: <span class="text-primary-600">{{ $selectedReseller->nama_reseller }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Isi nominal setoran lalu klik <strong>Simpan Semua</strong>. Biarkan kosong untuk skip.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">📅 Tanggal Setor</label>
                        <input type="date" name="tanggal_setor" id="tanggal_setor"
                            value="{{ date('Y-m-d') }}"
                            required
                            class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                    <div class="pt-5">
                        <button type="button" id="fillAllBtn"
                            class="border border-gray-200 text-gray-600 hover:border-primary-400 hover:text-primary-600 px-3 py-2 rounded-xl text-xs font-semibold transition-all">
                            Isi Semua Nominal
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabel Setoran --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left w-8">#</th>
                            <th class="px-6 py-3 text-left">Nasabah</th>
                            <th class="px-6 py-3 text-left">Paket</th>
                            <th class="px-6 py-3 text-center">Progress</th>
                            <th class="px-6 py-3 text-right">Setoran Wajib</th>
                            <th class="px-6 py-3 text-right">Total Bayar</th>
                            <th class="px-6 py-3 text-right min-w-48">Jumlah Setoran Kali Ini ✍️</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="setoranTable">
                        @foreach($customerPackets as $index => $cp)
                        @php
                            $totalSetoran  = $cp->savingsLedgers->sum('jumlah_setoran');
                            $targetTotal   = $cp->is_bebas ? 0 : ($cp->packet->setoran_wajib * $cp->packet->total_periode * $cp->kuantitas);
                            $sisaSetoran   = $cp->is_bebas ? 0 : max(0, $targetTotal - $totalSetoran);
                            $progressPct   = ($targetTotal > 0) ? min(100, round(($totalSetoran / $targetTotal) * 100)) : 0;
                            $isLunasReady  = !$cp->is_bebas && ($sisaSetoran == 0);
                        @endphp
                        <tr class="hover:bg-amber-50/30 transition-colors group {{ $isLunasReady ? 'bg-emerald-50/40' : '' }}"
                            data-setoran-wajib="{{ $cp->packet->setoran_wajib }}">

                            {{-- No --}}
                            <td class="px-6 py-4 text-gray-400 text-sm">{{ $index + 1 }}</td>

                            {{-- Nasabah --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 text-sm">{{ $cp->customer->nama_customer }}</p>
                                <p class="text-gray-400 text-xs">{{ $cp->customer->no_wa ?? '—' }}</p>
                            </td>

                            {{-- Paket --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 bg-primary-50 text-primary-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    {{ $cp->is_bebas ? '💵' : '📦' }} {{ $cp->packet->nama_paket }}
                                    @if($cp->kuantitas > 1)
                                        <span class="bg-primary-200 text-primary-800 px-1.5 rounded-full">×{{ $cp->kuantitas }}</span>
                                    @endif
                                </span>
                            </td>

                            {{-- Progress --}}
                            <td class="px-6 py-4">
                                @if($cp->is_bebas)
                                    <span class="text-xs bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded-full border border-amber-200">
                                        💰 Bebas/Fleksibel
                                    </span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-2 min-w-16">
                                            <div class="h-2 rounded-full transition-all {{ $isLunasReady ? 'bg-emerald-500' : 'bg-primary-400' }}"
                                                 style="width: {{ $progressPct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold {{ $isLunasReady ? 'text-emerald-600' : 'text-gray-600' }} w-10 text-right">
                                            {{ $progressPct }}%
                                        </span>
                                    </div>
                                    @if($isLunasReady)
                                        <span class="text-xs text-emerald-600 font-semibold">✅ Lunas</span>
                                    @else
                                        <span class="text-xs text-gray-400">Sisa: Rp {{ number_format($sisaSetoran, 0, ',', '.') }}</span>
                                    @endif
                                @endif
                            </td>

                            {{-- Setoran Wajib --}}
                            <td class="px-6 py-4 text-right">
                                @if($cp->is_bebas)
                                    <span class="text-xs text-gray-500 font-medium italic">Bebas</span>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($cp->packet->setoran_wajib, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-400">per periode</p>
                                @endif
                            </td>

                            {{-- Total Terkumpul --}}
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-semibold {{ $totalSetoran > 0 ? 'text-emerald-600' : 'text-gray-900' }}">
                                    Rp {{ number_format($totalSetoran, 0, ',', '.') }}
                                </p>
                                @if(!$cp->is_bebas)
                                    <p class="text-xs text-gray-400">dari Rp {{ number_format($targetTotal, 0, ',', '.') }}</p>
                                @endif
                            </td>

                            {{-- INPUT JUMLAH SETORAN --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <span class="bg-gray-100 text-gray-500 text-xs px-2 py-2.5 rounded-l-xl border border-r-0 border-gray-200 font-medium">Rp</span>
                                    <input
                                        type="number"
                                        name="setoran[{{ $cp->id }}][jumlah]"
                                        id="setoran_{{ $cp->id }}"
                                        min="0"
                                        step="500"
                                        placeholder="{{ $isLunasReady ? 'Sudah lunas' : ($cp->is_bebas ? 'Bebas...' : number_format($cp->packet->setoran_wajib, 0, '.', '')) }}"
                                        {{ $isLunasReady ? 'disabled' : '' }}
                                        class="setoran-input flex-1 border border-gray-200 rounded-r-xl px-3 py-2.5 text-sm text-right
                                               focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent
                                               {{ $isLunasReady ? 'bg-emerald-50 text-emerald-600 cursor-not-allowed' : 'bg-white' }}
                                               transition-all"
                                        data-wajib="{{ $cp->packet->setoran_wajib }}"
                                        oninput="highlightRow(this)">
                                </div>
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-4 py-3">
                                <input
                                    type="text"
                                    name="setoran[{{ $cp->id }}][keterangan]"
                                    placeholder="Opsional..."
                                    {{ $isLunasReady ? 'disabled' : '' }}
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-600
                                           focus:outline-none focus:ring-2 focus:ring-primary-400
                                           {{ $isLunasReady ? 'bg-emerald-50 cursor-not-allowed' : 'bg-white' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer: Summary & Submit --}}
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-sm">
                        <span class="text-gray-500">Total akan disimpan:</span>
                        <span class="font-bold text-primary-600 text-lg ml-2" id="grandTotal">Rp 0</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span id="countFilled">0</span> dari {{ $customerPackets->count() }} baris diisi
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="clearAll()"
                        class="px-4 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-semibold transition-all">
                        🗑️ Reset Semua
                    </button>
                    <button type="button" onclick="openConfirmModal()" id="submitBtn"
                        class="btn-primary px-6 py-2.5 text-white font-bold rounded-xl shadow-sm text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Semua Setoran
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== CUSTOM CONFIRMATION MODAL ===== --}}
    <div id="confirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop/Overlay -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeConfirmModal()"></div>
        <!-- Modal Content -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl z-10 max-w-sm w-full p-6 transform transition-all duration-200 scale-95 opacity-0" id="modalBox">
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mx-auto mb-4">
                    💾
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Konfirmasi Setoran</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Anda akan menyimpan <span id="modalCount" class="font-bold text-primary-600">0</span> setoran dengan total nominal <span id="modalTotal" class="font-bold text-primary-600">Rp 0</span>.
                </p>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="submitBulkForm()" class="btn-primary flex-1 py-2.5 text-white font-semibold rounded-xl text-xs">
                        Ya, Simpan
                    </button>
                    <button type="button" onclick="closeConfirmModal()" class="flex-1 py-2.5 border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-xl text-xs font-medium">
                        Batal
                      </button>
                </div>
            </div>
        </div>
    </div>

    @elseif($selectedReseller && $customerPackets->count() === 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <p class="text-5xl mb-4">📭</p>
        <p class="text-gray-700 font-semibold text-lg">Tidak ada paket aktif</p>
        <p class="text-gray-400 mt-1">Semua nasabah reseller <strong>{{ $selectedReseller->nama_reseller }}</strong> belum memiliki paket aktif.</p>
        <a href="{{ route('customers.create') }}" class="inline-block mt-4 btn-primary text-white px-5 py-2 rounded-xl text-sm font-semibold">
            + Tambah Nasabah
        </a>
    </div>

    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <p class="text-6xl mb-4">👆</p>
        <p class="text-gray-700 font-semibold text-xl">Pilih Reseller dulu</p>
        <p class="text-gray-400 mt-2">Pilih reseller di atas untuk menampilkan daftar nasabah dan form input setoran.</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // ===== Hitung total & counter real-time =====
    const inputs = document.querySelectorAll('.setoran-input');

    function updateSummary() {
        let total = 0;
        let count = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            if (val > 0) { total += val; count++; }
        });
        document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('countFilled').textContent = count;
    }

    function highlightRow(input) {
        const row = input.closest('tr');
        const val = parseFloat(input.value) || 0;
        if (val > 0) {
            row.classList.add('bg-amber-50');
        } else {
            row.classList.remove('bg-amber-50');
        }
        updateSummary();
    }

    // ===== Isi semua dengan setoran wajib =====
    document.getElementById('fillAllBtn')?.addEventListener('click', function() {
        inputs.forEach(input => {
            if (!input.disabled && !input.value && parseFloat(input.dataset.wajib) > 0) {
                input.value = input.dataset.wajib;
                highlightRow(input);
            }
        });
        updateSummary();
    });

    // ===== Reset semua =====
    function clearAll() {
        inputs.forEach(input => {
            input.value = '';
            const tr = input.closest('tr');
            if (tr) tr.classList.remove('bg-amber-50');
        });
        updateSummary();
    }

    // ===== Modal controls =====
    function openConfirmModal() {
        const count = parseInt(document.getElementById('countFilled').textContent);
        const total = document.getElementById('grandTotal').textContent;
        if (count === 0) {
            alert('⚠️ Tidak ada nominal yang diisi! Harap isi minimal 1 setoran.');
            return;
        }
        document.getElementById('modalCount').textContent = count;
        document.getElementById('modalTotal').textContent = total;

        const modal = document.getElementById('confirmModal');
        const box = document.getElementById('modalBox');
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        const box = document.getElementById('modalBox');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function submitBulkForm() {
        document.getElementById('bulkForm').submit();
    }

    // Inisialisasi
    updateSummary();
</script>
@endpush
