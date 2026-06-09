<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPacket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'packet_id',
        'kuantitas',
        'status',
        'catatan',
    ];

    protected $casts = [
        'kuantitas'     => 'integer',
        'setoran_wajib' => 'decimal:2',
    ];

    /**
     * CustomerPacket milik satu nasabah.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * CustomerPacket merujuk ke satu paket.
     */
    public function packet()
    {
        return $this->belongsTo(Packet::class);
    }

    /**
     * CustomerPacket memiliki banyak catatan setoran.
     */
    public function savingsLedgers()
    {
        return $this->hasMany(SavingsLedger::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES (Accessor)
    // =========================================================

    /**
     * Total yang sudah dibayarkan ke paket ini.
     */
    public function getTotalSetoranAttribute(): float
    {
        return (float) $this->savingsLedgers()->sum('jumlah_setoran');
    }

    /**
     * Target total yang harus dibayar (setoran × periode × kuantitas).
     */
    public function getTargetTotalAttribute(): float
    {
        return (float) ($this->packet->setoran_wajib * $this->packet->total_periode * $this->kuantitas);
    }

    /**
     * Sisa setoran yang belum dibayar.
     */
    public function getSisaSetoranAttribute(): float
    {
        return max(0, $this->target_total - $this->total_setoran);
    }

    /**
     * Persentase progress setoran (0-100).
     */
    public function getProgressPersenAttribute(): float
    {
        if ($this->target_total == 0) return 100;
        return min(100, round(($this->total_setoran / $this->target_total) * 100, 1));
    }

    /**
     * Jumlah periode yang sudah terbayar.
     */
    public function getPeriodeTerbayarAttribute(): int
    {
        if ($this->packet->setoran_wajib == 0) return 0;
        return (int) floor($this->total_setoran / ($this->packet->setoran_wajib * $this->kuantitas));
    }
}
