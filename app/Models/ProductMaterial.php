<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'material_id',
        'quantity',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class,'material_id');
    }
}
