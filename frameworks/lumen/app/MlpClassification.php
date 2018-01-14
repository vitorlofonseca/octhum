<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MlpClassification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_mlp_classification';

    protected $fillable = [
        'name',
        'id',
        'output_number'
    ];

    protected $guarded = [];

}