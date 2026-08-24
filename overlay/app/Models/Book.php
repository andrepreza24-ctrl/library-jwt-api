<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'available_copies',
    ];

    protected function casts(): array
    {
        return [
            'available_copies' => 'integer',
        ];
    }
}
