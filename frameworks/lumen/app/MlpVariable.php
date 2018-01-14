<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MlpVariable extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_mlp_variable';

    protected $fillable = [
        'name',
        'id'
    ];

    protected $guarded = [];

}