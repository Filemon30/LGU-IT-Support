<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityLevel extends Model
{
    use HasFactory;

    protected $table = 'priority_levels';

    protected $primaryKey = 'priority_level_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'priority_name',
        'priority_description',
        'response_target_minutes',
        'resolution_target_minutes',
    ];

    protected $casts = [
        'response_target_minutes' => 'integer',
        'resolution_target_minutes' => 'integer',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'default_priority_level_id', 'priority_level_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_level_id', 'priority_level_id');
    }
}
