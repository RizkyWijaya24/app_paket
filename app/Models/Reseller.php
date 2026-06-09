<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_reseller',
        'no_wa',
        'alamat',
    ];

    /**
     * Seorang reseller memiliki banyak nasabah.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Akses semua customer_packets milik nasabah reseller ini.
     */
    public function customerPackets()
    {
        return $this->hasManyThrough(CustomerPacket::class, Customer::class);
    }

    /**
     * Total dana yang berhasil dikumpulkan oleh reseller ini.
     */
    public function getTotalDanaAttribute(): float
    {
        return $this->customerPackets()
            ->join('savings_ledgers', 'customer_packets.id', '=', 'savings_ledgers.customer_packet_id')
            ->sum('savings_ledgers.jumlah_setoran');
    }
}
