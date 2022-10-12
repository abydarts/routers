<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trans extends Model
{
    protected $table = 'trans';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'request' => 'array',
        'response' => 'array'
    ];
}
