<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'release_year',
        'cover',
        'user_id'
    ];

    /**
     * RELATIONS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function getCategoriesText()
    {
        return $this->categories->implode('name', ', ');
    }
}
