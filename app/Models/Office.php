<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasFactory;

    protected $table = 'offices';

    protected $primaryKey = 'office_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'office_ref_num',
        'office_name',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class, 'office_id', 'office_id');
    }
}
