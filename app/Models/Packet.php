<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'setoran_wajib',
        'total_periode',
        'keterangan',
    ];

    protected $casts = [
        'setoran_wajib' => 'decimal:2',
        'total_periode' => 'integer',
    ];

    /**
     * Sebuah paket bisa dimiliki oleh banyak nasabah (via customer_packets).
     */
    public function customerPackets()
    {
        return $this->hasMany(CustomerPacket::class);
    }

    /**
     * Total nilai paket (setoran_wajib × total_periode).
     */
    public function getTotalNilaiAttribute(): float
    {
        return $this->setoran_wajib * $this->total_periode;
    }
}
