<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;
use Rupadana\ApiService\Contracts\HasAllowedSorts;

class Order extends Model
{
use HasFactory;

protected $fillable = [
    'name',
    'number',
    'alamat',
    'priority',
    'time',
    'status',
    'down_payment',
    'sum_price',
    'total_product',
    'finish',
    'antrian',
    'keterangan',
];

protected $casts = [
    'finish' => 'boolean',
    'priority' => 'boolean',
];

    protected static function boot()
    {
        parent::boot();

        // Ketika order baru dibuat
        static::created(function ($order) {
            static::updatePendingOrders();
        });

        static::deleted(function ($order) {
            static::updatePendingOrders();
        });

        // Ketika order diperbarui
        static::updated(function ($order) {
            // Jika perubahan pada kolom finish, priority, atau time
            if ($order->isDirty('finish') || $order->isDirty('time') || $order->isDirty('priority')) {
                static::updatePendingOrders();
            }
        });
    }

// Update semua antrian yang belum selesai
    protected static function updatePendingOrders()
    {


        DB::transaction(function () {
            $pendingOrders = Order::where('finish', false)
                ->orderBy('priority', 'desc')
                ->orderBy('time', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($pendingOrders->isEmpty()) {
                return;
            }

            $lastAntrian = null;
            foreach ($pendingOrders as $index => $pendingOrder) {
                // Hitung antrian berdasarkan urutan
                if ($index === 0) {
                    // Untuk order pertama, hitung berdasarkan created_at dan time

                    $lastAntrian = Carbon::parse($pendingOrder->created_at)->addDays((int)$pendingOrder->time);
                    Log::error($pendingOrder->name."-".$pendingOrder->priority.":".$pendingOrder->created_at."+".$pendingOrder->time." =".$lastAntrian);
                } else {
                    // Untuk order selanjutnya, tambah waktu (time) dari antrian sebelumnya
                    Log::error($pendingOrder->name."-".$pendingOrder->priority.":".$lastAntrian."+".$pendingOrder->time);
                    $lastAntrian = $lastAntrian->addDays((int)$pendingOrder->time);
                }

                // Update tanpa memengaruhi iterasi
                $pendingOrder->antrian = $lastAntrian;
                $pendingOrder->save();
            }
        });

    }


public function orderDetail() : HasMany
{
    return $this->hasMany(OrderDetail::class);
}

public function productMaterial(): HasMany
{
    return $this->hasMany(ProductMaterial::class,'order_id');
}

public static array $allowedFilters = [
    'priority','status','finish'
];

public static array $allowedFields = [
    'priority'
];

public static array $allowedSorts = [
    'created_at',
];
}
