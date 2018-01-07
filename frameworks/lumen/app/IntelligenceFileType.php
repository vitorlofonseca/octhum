<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntelligenceFileType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_intelligence_file_type';

    protected $fillable = [
        'id',
        'type'
    ];

    protected $guarded = [];
}