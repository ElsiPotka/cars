<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'engine_size' => $this->engine_size,
            'color' => $this->color,
            'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
            'car_model' => new CarModelResource($this->whenLoaded('carModel')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'features' => CarFeatureResource::collection($this->whenLoaded('features')),
            'photos' => CarPhotoResource::collection($this->whenLoaded('photos')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
