<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manager extends User
{
    protected $table = 'managers';

    protected $fillable = [
        'user_id',
    ];

    /**
     * @return HasMany
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'manager_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
