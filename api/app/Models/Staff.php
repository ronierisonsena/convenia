<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends User
{
    protected $table = 'staff';

    protected $fillable = [
        'manager_id',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
