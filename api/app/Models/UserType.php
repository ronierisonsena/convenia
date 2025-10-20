<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    public const TYPE_STAFF = 1;

    public const TYPE_MANAGER = 2;

    public const SCOPES = [
        'manager' => 'managers collaborators',
        'staff' => 'staff collaborators',
    ];

    protected $table = 'user_types';

    protected $fillable = [
        'name',
        'role',
        'description',
    ];
}
