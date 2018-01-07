<?php

namespace App\Http\Controllers;

use App\LogType;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

class LogTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function createLogType(Request $request){

        try{

            if (!$request->input('type'))
                throw new Exception('A type is necessary');

            $objLogType = LogType::create($request->all());

            return response()->json($objLogType);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }

    }

    public function getLogType($id){

        try {

            $objLogType = LogType::find($id);

            if (!is_object($objLogType))
                throw new Exception('Log Type id is invalid or doesn\'t exist');

            return response()->json($objLogType);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }

    public function deleteLogType($id){

        try {

            $objLogType = LogType::find($id);

            if (!is_object($objLogType))
                throw new Exception('Log Type id invalid or doesn\'t exist');

            $objLogType->delete();

            return response()->json('Log Type deleted');

        } catch(Exception $e){

            return response()->json($e->getMessage());

        }

    }

    public function updateLogType(Request $request,$id){

        try {

            $objLogType = LogType::find($id);

            if (!is_object($objLogType))
                throw new Exception('Log type id invalid or doesn\'t exist');

            if($request->input('type')) {
                $objLogType->type = $request->input('type');
                $objLogType->save();
            }

            return response()->json('Log Type uploaded');

        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }


    public function getAllLogTypes(){

        $aObjLogType = LogType::all();

        return response()->json($aObjLogType);

    }

}
