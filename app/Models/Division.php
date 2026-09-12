<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $primaryKey = 'division_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'office_id',
        'division_ref_num',
        'division_name',
        'secret_key_hash',
        'key_status',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }

    public function requesters(): HasMany
    {
        return $this->hasMany(Requester::class, 'division_id', 'division_id');
    }
}
