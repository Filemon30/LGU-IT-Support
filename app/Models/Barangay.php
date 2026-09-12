<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'barangays';

    protected $primaryKey = 'barangay_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'barangay_ref_num',
        'barangay_name',
        'secret_key_hash',
        'key_status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requesters(): HasMany
    {
        return $this->hasMany(Requester::class, 'barangay_id', 'barangay_id');
    }
}
