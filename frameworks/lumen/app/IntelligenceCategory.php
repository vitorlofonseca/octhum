<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntelligenceCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_intelligence_category';

    public $timestamps = false;

    protected $connection = 'mysql';

    protected $fillable = [
        'category',
        'id'
    ];

    protected $guarded = [];
}