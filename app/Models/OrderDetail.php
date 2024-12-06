<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'file_design',
        'status',
        'size_s',
        'quantity_size_s',
        'size_m',
        'quantity_size_m',
        'size_l',
        'quantity_size_l',
        'size_xl',
        'quantity_size_xl',
        'size_2xl',
        'quantity_size_2xl',
        'size_3xl',
        'quantity_size_3xl',
        'size_4xl',
        'quantity_size_4xl',
        'total_product',
        'quantity_total_product',
        'total_time',
        'sum_price',
        'keterangan',
    ];

    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function order() :BelongsTo
    {
        return $this->belongsTo(OrderDetail::class,'order_id');
    }

    public function sewing() :BelongsTo
    {
        return $this->belongsTo(Sewing::class);
    }

    public function design() :BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function cutting() :BelongsTo
    {
        return $this->belongsTo(Cutting::class);
    }

    public function screenPrinting() :BelongsTo
    {
        return $this->belongsTo(ScreenPrinting::class,'screen_printing_id');
    }

    public function packaging() :BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }
}
