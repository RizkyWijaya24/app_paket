<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if gte mso 9]>
<xml>
  {!! '<x' . ':ExcelWorkbook>' !!}
    {!! '<x' . ':ExcelWorksheets>' !!}
      {!! '<x' . ':ExcelWorksheet>' !!}
        {!! '<x' . ':Name>Laporan Tabungan</x' . ':Name>' !!}
        {!! '<x' . ':WorksheetOptions>' !!}
          {!! '<x' . ':DisplayGridlines/>' !!}
        {!! '</x' . ':WorksheetOptions>' !!}
      {!! '</x' . ':ExcelWorksheet>' !!}
    {!! '</x' . ':ExcelWorksheets>' !!}
  {!! '</x' . ':ExcelWorkbook>' !!}
</xml>
<![endif]-->
<style>
  body { font-family: Arial, sans-serif; }
  .title { font-size: 14pt; font-weight: bold; text-align: center; }
  .subtitle { font-size: 10pt; text-align: center; color: #555555; }
  table { border-collapse: collapse; }
  th { background-color: #d97706; color: white; font-weight: bold; border: 1px solid #000000; padding: 6px; text-align: center; }
  td { border: 1px solid #000000; padding: 5px; vertical-align: middle; }
  .number { text-align: right; }
  .center { text-align: center; }
  .bold { font-weight: bold; }
  .total-row { background-color: #e2e8f0; font-weight: bold; }
</style>
</head>
<body>

  <table>
    <tr>
      <td colspan="10" class="title">LAPORAN TABUNGAN PAKET LEBARAN 2026</td>
    </tr>
    <tr>
      <td colspan="10" class="title">RESELLER: {{ strtoupper($reseller->nama_reseller) }}</td>
    </tr>
    <tr>
      <td colspan="10" class="subtitle">Tanggal Ekspor: {{ date('d M Y H:i') }} · Total Nasabah: {{ $reseller->customers->count() }}</td>
    </tr>
    <tr>
      <td colspan="10"></td>
    </tr>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Nasabah</th>
        <th>No. WhatsApp</th>
        <th>Paket Tabungan</th>
        <th>Unit</th>
        <th>Setoran Wajib</th>
        <th>Target Total</th>
        <th>Total Terkumpul</th>
        <th>Sisa Kekurangan</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @php
        $no = 1;
        $totalUnitGlobal = 0;
        $totalTargetGlobal = 0;
        $totalTerkumpulGlobal = 0;
        $totalSisaGlobal = 0;
      @endphp
      @foreach($reseller->customers as $customer)
        @php $rowspan = max(1, $customer->customerPackets->count()); @endphp
        @foreach($customer->customerPackets as $index => $cp)
          @php
            $totalUnitGlobal += $cp->kuantitas;
            $totalTargetGlobal += $cp->target_total;
            $totalTerkumpulGlobal += $cp->total_setoran;
            $totalSisaGlobal += $cp->sisa_setoran;
          @endphp
          <tr>
            @if($index === 0)
              <td rowspan="{{ $rowspan }}" class="center">{{ $no++ }}</td>
              <td rowspan="{{ $rowspan }}">{{ $customer->nama_customer }}</td>
              <td rowspan="{{ $rowspan }}" class="center">'{{ $customer->no_wa ?? '—' }}</td>
            @endif
            <td>{{ $cp->packet->nama_paket }}</td>
            <td class="center">{{ $cp->kuantitas }}</td>
            <td class="number">
              @if($cp->is_bebas)
                Bebas
              @else
                {{ number_format($cp->packet->setoran_wajib, 0, ',', '.') }}
              @endif
            </td>
            <td class="number">
              @if($cp->is_bebas)
                Fleksibel
              @else
                {{ number_format($cp->target_total, 0, ',', '.') }}
              @endif
            </td>
            <td class="number" style="color: #16a34a; font-weight: bold;">
              {{ number_format($cp->total_setoran, 0, ',', '.') }}
            </td>
            <td class="number" style="color: {{ $cp->is_bebas ? '#475569' : ($cp->sisa_setoran > 0 ? '#dc2626' : '#1d4ed8') }}">
              @if($cp->is_bebas)
                —
              @else
                {{ number_format($cp->sisa_setoran, 0, ',', '.') }}
              @endif
            </td>
            <td class="center font-bold">
              @if($cp->status === 'aktif')
                AKTIF
              @elseif($cp->status === 'lunas')
                LUNAS
              @else
                BATAL
              @endif
            </td>
          </tr>
        @endforeach
        @if($customer->customerPackets->isEmpty())
          <tr>
            <td class="center">{{ $no++ }}</td>
            <td>{{ $customer->nama_customer }}</td>
            <td class="center">'{{ $customer->no_wa ?? '—' }}</td>
            <td colspan="7" class="center" style="color: #94a3b8; font-style: italic;">Belum mengambil paket</td>
          </tr>
        @endif
      @endforeach
      
      {{-- Baris Total --}}
      <tr class="total-row">
        <td colspan="4" class="right bold">TOTAL</td>
        <td class="center">{{ $totalUnitGlobal }}</td>
        <td class="number">—</td>
        <td class="number">{{ number_format($totalTargetGlobal, 0, ',', '.') }}</td>
        <td class="number">{{ number_format($totalTerkumpulGlobal, 0, ',', '.') }}</td>
        <td class="number">{{ number_format($totalSisaGlobal, 0, ',', '.') }}</td>
        <td class="center">—</td>
      </tr>
    </tbody>
  </table>

</body>
</html>
