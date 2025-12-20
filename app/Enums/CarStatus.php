<?php

namespace App\Enums;

enum CarStatus: string
{
    case Available = 'Available';
    case Sold = 'Sold';
    case Reserved = 'Reserved';
}
