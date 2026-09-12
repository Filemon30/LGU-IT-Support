<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $table = 'issues';

    protected $primaryKey = 'issue_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'category_id',
        'issue_ref_num',
        'issue_name',
        'description',
        'default_priority_level_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function defaultPriority(): BelongsTo
    {
        return $this->belongsTo(PriorityLevel::class, 'default_priority_level_id', 'priority_level_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'issue_id', 'issue_id');
    }
}
