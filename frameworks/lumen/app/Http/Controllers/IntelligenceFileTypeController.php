<?php

namespace App\Http\Controllers;

use App\IntelligenceDataType;
use App\NeuralNetwork;
use App\Quotation;

class IntelligenceDataTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }


    public function getIntelligenceDataTypes(){

        $aObjIntelligenceDataType = IntelligenceDataType::all();

        return response()->json($aObjIntelligenceDataType);

    }

}
