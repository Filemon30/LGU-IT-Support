<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $primaryKey = 'color_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'color_name',
        'color_value',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'color_id', 'color_id');
    }
}
