<?php

namespace App\Http\Controllers;

use App\IntelligenceFileType;
use App\NeuralNetwork;
use App\Quotation;

class IntelligenceFileTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }


    public function getIntelligenceFileTypes(){

        $aObjIntelligenceFileType = IntelligenceFileType::all();

        return response()->json($aObjIntelligenceFileType);

    }

}
