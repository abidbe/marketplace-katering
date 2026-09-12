<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS = [
        'pending' => 'Menunggu',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'merchant_id',
        'delivery_date',
        'note',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function nextInvoiceNumber(\DateTimeInterface|string $date): string
    {
        $period = Carbon::parse($date)->format('Ym');

        return DB::transaction(function () use ($period) {
            $last = static::query()
                ->where('invoice_number', 'like', 'INV-'.$period.'-%')
                ->lockForUpdate()
                ->orderByDesc('invoice_number')
                ->value('invoice_number');

            $next = ((int) substr((string) $last, -4)) + 1;

            return sprintf('INV-%s-%04d', $period, $next);
        });
    }

    public function itemsTotal(): int
    {
        return (int) $this->items->sum('subtotal');
    }

    public function isEditableByCustomer(): bool
    {
        return $this->status === 'pending';
    }

    public function getStatusValAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getTotalRpAttribute()
    {
        return 'Rp'.number_format($this->total_price, 0, ',', '.');
    }
}
