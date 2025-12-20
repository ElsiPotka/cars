<?php

namespace App\Enums;

enum CarFuelType: string
{
    case Petrol = 'Petrol';
    case Diesel = 'Diesel';
    case Electric = 'Electric';
    case Hybrid = 'Hybrid';
}
