<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MinMaxValues extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_min_max_values';

    protected $fillable = [
        'min_or_max',
        'values'
    ];

    protected $guarded = [];

}