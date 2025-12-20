<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Brand extends BaseModel
{
    use Searchable;

    protected $fillable = [
        'name',
        'country',
        'description',
        'logo_path',
    ];

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'country' => $this->country,
        ];
    }
}
