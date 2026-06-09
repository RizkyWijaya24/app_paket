<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'nama_customer',
        'no_wa',
        'alamat',
    ];

    /**
     * Nasabah terdaftar di bawah satu reseller.
     */
    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Nasabah bisa memiliki BANYAK paket (via customer_packets).
     * Ini adalah relasi INTI dari aplikasi ini.
     */
    public function customerPackets()
    {
        return $this->hasMany(CustomerPacket::class);
    }

    /**
     * Many-to-Many ke Packet melalui customer_packets.
     */
    public function packets()
    {
        return $this->belongsToMany(Packet::class, 'customer_packets')
                    ->withPivot('id', 'kuantitas', 'status', 'catatan')
                    ->withTimestamps();
    }
}
