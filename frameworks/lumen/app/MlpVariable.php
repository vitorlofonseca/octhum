<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

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

    public function MinMaxValues(){
        return $this->hasMany('App\MinMaxValues', 'id_variable', 'id');
    }

    protected $guarded = [];

    public function delete(){

        foreach($this->minMaxValues as $minMaxValue){
            $minMaxValue->delete();
        }

        return parent::delete();
    }

}