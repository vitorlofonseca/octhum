<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Intelligence extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_intelligence';

    protected $fillable = [
        'name',
        'id',
        'createdAt',
        'updatedAt',
        'id_resp_inc',
        'id_resp_alt',
        'description',
        'id_category',
        'id_data_type'
    ];

    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User', 'id_resp_inc', 'id');
    }

    public function category(){
        return $this->belongsTo('App\IntelligenceCategory', 'id_category', 'id');
    }

    public function dataType(){
        return $this->belongsTo('App\IntelligenceDataType', 'id_file_type', 'id');
    }

    public function mlp(){
        return $this->hasOne('App\Mlp', 'id_intelligence', 'id');
    }

    public function intellogenceLog(){
        return $this->hasMany('App\IntelligenceLog', 'id_intelligence', 'id');
    }

    public function delete(){

        if($this->mlp)
            $this->mlp->delete();

        foreach($this->intellogenceLog as $intelligenceLog){
            $intelligenceLog->delete();
        }

        return parent::delete();
    }

}