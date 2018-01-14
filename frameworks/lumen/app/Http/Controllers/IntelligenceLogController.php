<?php

namespace App\Http\Controllers;

use App\IntelligenceLog;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\DB;
use App\Quotation;
use App\Intelligence;

class IntelligenceLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function createIntelligenceLog(Request $request){

        try{

            if (!$request->input('file_name'))
                throw new Exception('A log file name is necessary');

            /** --------------- RESP_INC --------------- */
            if (!$request->input('userEmail'))
                throw new Exception('An email to identify the user is necessary');

            $objUser = DB::table('tbl_user')->where('email', 'like', $request->input('userEmail'))->first();

            if (!is_object($objUser))
                throw new Exception('Invalid user. Try with other user email');

            $request['id_resp_inc'] = $objUser->id;



            /** --------------- INTELLIGENCE --------------- */

            if (!$request->input('intelligenceName'))
                throw new Exception('A intelligence id is necessary');

            $objIntelligence = DB::table('tbl_intelligence')->where('intelligenceName', 'like', $request->input('name'))->first();

            if (!is_object($objIntelligence))
                throw new Exception('Invalid IntelligenceLog Category. Try with other intelligence name');

            //this field can't to go to the database
            $request->input('intelligenceName', null);

            $request['id_log_type'] = $objIntelligence->id;



            /** --------------- LOG TYPE --------------- */

            if (!$request->input('log_type'))
                throw new Exception('A log type is necessary');

            $objLogType = DB::table('tbl_log_type')->where('type', 'like', $request->input('log_type'))->first();

            if (!is_object($objLogType))
                throw new Exception('Invalid IntelligenceLog Category. Try with other log type name');

            //this field can't to go to the database
            $request->input('log_type', null);

            $request['id_log_type'] = $objLogType->id;



            $objIntelligenceLog = IntelligenceLog::create($request->all());

            return response()->json($objIntelligenceLog);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }

    }

    public function getIntelligenceLog($id){

        try {

            $objIntelligenceLog = IntelligenceLog::find($id);

            if (!is_object($objIntelligenceLog))
                throw new Exception('Intelligence\'s Log id invalid or doesn\'t exist');

            return response()->json($objIntelligenceLog);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }


    public function getAllIntelligenceLogsPerIntelligence($idIntelligence){

        try {

            if (!$idIntelligence)
                throw new Exception('A intelligence id is necessary');

            //consulting the intelligence to know if it exists
            $objIntelligence = Intelligence::find($idIntelligence);

            if (!is_object($objIntelligence))
                throw new Exception('Invalid intelligence. Insert other id');

            $aObjLogIntelligence = DB::table('tbl_neural_network')->where('id_intelligence', '=', $idIntelligence)->get();

            return response()->json($aObjLogIntelligence);

        } catch(Exception $e){
            return response()->json($e->getMessage());
        }

    }

}
