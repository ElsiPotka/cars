<?php

namespace App\Models;

use App\Enums\CarFuelType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Car extends BaseModel
{
    use Searchable;

    protected $fillable = [
        'car_model_id',
        'category_id',
        'name',
        'year',
        'mileage',
        'transmission',
        'fuel_type',
        'engine_size',
        'color',
        'price',
        'status',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     * Overriding parent cast method but keeping timestamps via array_merge
     * if necessary, or just defining specific ones here.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'transmission' => CarTransmission::class,
            'fuel_type' => CarFuelType::class,
            'status' => CarStatus::class,
            'year' => 'integer',
            'mileage' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(CarFeature::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing(['carModel.brand', 'category', 'features']);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'color' => $this->color,
            'brand' => $this->carModel?->brand?->name,
            'model' => $this->carModel?->name,
            'category' => $this->category?->name,
            'transmission' => $this->transmission?->value,
            'fuel_type' => $this->fuel_type?->value,
            'status' => $this->status?->value,
            'price' => (float) $this->price,
            'mileage' => $this->mileage,
            'features' => $this->features->pluck('name')->toArray(),
        ];
    }
}
