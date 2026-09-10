<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'staff_ref_num',
        'role_id',
        'user_info_id',
        'user_acc_id',
        'status',
    ];

    protected $casts = [
        'role_id' => 'integer',
        'user_info_id' => 'integer',
        'user_acc_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class, 'user_info_id', 'user_info_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(UserAccount::class, 'user_acc_id', 'user_acc_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to', 'user_id');
    }

    public function createdKnowledge(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class, 'created_by', 'user_id');
    }

    public function updatedKnowledge(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class, 'updated_by', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'user_id', 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function adminTransactions(): HasMany
    {
        return $this->hasMany(AdminTransaction::class, 'handled_by', 'user_id');
    }
}
