<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'quantity',
        'type',
    ];

    public function productMaterial():BelongsTo
    {
        return $this->belongsTo(ProductMaterial::class,'material_id');
    }
}
