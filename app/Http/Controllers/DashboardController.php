<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPacket;
use App\Models\Reseller;
use App\Models\SavingsLedger;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total dana global yang sudah terkumpul
        $totalDanaGlobal = SavingsLedger::sum('jumlah_setoran');

        // Total nasabah aktif
        $totalNasabah = Customer::count();

        // Total paket aktif (customer_packets)
        $totalPaketAktif = CustomerPacket::where('status', 'aktif')->count();

        // Total paket lunas
        $totalPaketLunas = CustomerPacket::where('status', 'lunas')->count();

        // Breakdown dana per reseller
        $resellerStats = Reseller::withCount('customers')
            ->get()
            ->map(function ($reseller) {
                $totalDana = DB::table('savings_ledgers')
                    ->join('customer_packets', 'savings_ledgers.customer_packet_id', '=', 'customer_packets.id')
                    ->join('customers', 'customer_packets.customer_id', '=', 'customers.id')
                    ->where('customers.reseller_id', $reseller->id)
                    ->sum('savings_ledgers.jumlah_setoran');

                $reseller->total_dana    = $totalDana;
                return $reseller;
            });

        // Setoran terbaru (10 terakhir)
        $setoranTerbaru = SavingsLedger::with([
                'customerPacket.customer.reseller',
                'customerPacket.packet',
            ])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'totalDanaGlobal',
            'totalNasabah',
            'totalPaketAktif',
            'totalPaketLunas',
            'resellerStats',
            'setoranTerbaru',
        ));
    }
}
