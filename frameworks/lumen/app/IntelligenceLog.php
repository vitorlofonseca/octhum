<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntelligenceLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_intelligence_log';

    protected $fillable = [
        'file_name',
        'id',
        'id_intelligence',
        'id_log_type',
        'date',
        'description'
    ];

    protected $guarded = [];
}