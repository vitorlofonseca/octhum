<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_log_type';

    public $timestamps = false;

    protected $fillable = [
        'type',
        'id'
    ];

    protected $guarded = [];
}