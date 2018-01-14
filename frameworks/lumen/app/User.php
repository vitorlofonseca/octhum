<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_user';

    protected $fillable = [
        'name',
        'id',
        'id_resp_inc',
        'id_resp_alt',
        'email',
        'username'
    ];

    protected $guarded = [
        'password'
    ];
}