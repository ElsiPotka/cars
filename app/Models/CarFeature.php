<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class CarFeature extends BaseModel
{
    use Searchable;

    protected $fillable = [
        'name',
        'description',
    ];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
