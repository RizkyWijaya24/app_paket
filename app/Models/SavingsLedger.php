<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_packet_id',
        'jumlah_setoran',
        'tanggal_setor',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_setoran' => 'decimal:2',
        'tanggal_setor'  => 'date',
    ];

    /**
     * Setiap setoran merujuk ke satu CustomerPacket.
     */
    public function customerPacket()
    {
        return $this->belongsTo(CustomerPacket::class);
    }
}
