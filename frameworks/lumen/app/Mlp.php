<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mlp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_mlp';

    protected $fillable = [
        'id',
        'id_intelligence',
        'id_resp_inc',
        'id_resp_alt',
        'conf_file_name'
    ];

    protected $guarded = [];

    public function mlpVariable(){
        return $this->hasMany('App\MlpVariable', 'id_mlp', 'id');
    }

    public function mlpClassification(){
        return $this->hasMany('App\MlpClassification', 'id_mlp', 'id');
    }

    public function delete(){

        foreach($this->mlpVariable as $variable){
            $variable->delete();
        }

        foreach($this->mlpClassification as $classification){
            $classification->delete();
        }

        return parent::delete();
    }

}