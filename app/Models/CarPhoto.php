<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarPhoto extends BaseModel
{
    protected $fillable = [
        'car_id',
        'path',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
