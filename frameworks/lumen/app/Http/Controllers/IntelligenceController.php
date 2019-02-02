<?php

namespace App\Http\Controllers;

use App\Intelligence;
use App\NeuralNetwork;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\DB;
use App\Quotation;

class IntelligenceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }


    /**
     * Create an intelligence
     *
     */
    public function createIntelligence(Request $request){

        try{

            DB::beginTransaction();

            if (!$request->input('name'))
                throw new Exception('An name to identify the intelligence is necessary');

            /** --------------- RESP_INC --------------- */
            if (!$request->input('userEmail'))
                throw new Exception('An email to identify the user is necessary');

            $objUser = DB::table('tbl_user')->where('email', 'like', $request->input('userEmail'))->first();

            if (!is_object($objUser))
                throw new Exception('Invalid user. Try with another user email');

            $request['id_resp_inc'] = $objUser->id;


            /** --------------- CATEGORY --------------- */

            if (!$request->input('category'))
                throw new Exception('A category is necessary');

            $objIntelligenceCategory = DB::table('tbl_intelligence_category')->where('category', 'like', $request->input('category'))->first();

            if (!is_object($objIntelligenceCategory))
                throw new Exception('Invalid Intelligence Category. Try with other category');

            //this field can't to go to the database
            $request->input('category', null);

            $request['id_category'] = $objIntelligenceCategory->id;


            /** --------------- INTELLIGENCE DATA TYPE --------------- */

            if (!$request->input('dataType'))
                throw new Exception('A data type is necessary');

            $request['id_data_type'] = (int)$request->input('dataType');

            $objIntelligence = Intelligence::create($request->all());


            /** --------------- CATEGORY TO DECIDE THE METHOD--------------- */

            $bMlp = false;

            switch($request['id_category']){

                //neural network
                case ID_INTELLIGENCE_CATEGORY_CLASSIFICATION:
                case ID_INTELLIGENCE_CATEGORY_CLUSTERING:
                case ID_INTELLIGENCE_CATEGORY_PREVISION:
                    $bMlp = true;
                    break;

            }

            /** --------------- MLP --------------- */

            if($bMlp){
                $idMlp = DB::table('tbl_mlp')->insertGetId(
                    ['id_intelligence' => $objIntelligence->id,
                     'id_resp_inc' => $request['id_resp_inc']
                    ]
                );

            }

            /** --------------- INSERT LOG --------------- */

            DB::table('tbl_intelligence_log')->insert(
                ['id_intelligence' => $objIntelligence->id,
                 'description' => 'Intelligence creation',
                 'id_log_type' => ID_LOG_TYPE_CREATION,
                 'file_name' => ' '
                ]
            );

            /** --------------- DATA TYPE--------------- */

            switch($request->input('dataType')){

                case ID_DATA_TYPE_SHEET:

                    if (!$request->fileTraining) {
                        throw new Exception("A file is necessary to train the intelligence");
                    }

                    $tmpName = $request->fileTraining;

                    $aData = array_map('str_getcsv', file($tmpName));

                    break;

                case ID_DATA_TYPE_JSON:

                    $jsonData = file_get_contents("php://input");

                    $aData = json_decode($jsonData);

                    //stdClass to array
                    $aData = (array)$aData;

                    //body of array
                    $aData = $aData["data"];

                    break;

                default:

                    throw new Exception("Invalid data type");

                    break;
            }

            //classes (distinct of class column in the sheet)
            $aClasses = array();

            //variables (columns, less class column)
            $aVariable = array();

            //minimum and maximum values of each variable
            $aMinMax = array();


            foreach ($aData as $i => $row) {

                //catching the variables
                if($i == 0){

                    //columns
                    foreach($row as $j => $column){

                        //mounting variables array of intelligence
                        if($j != 0){
                            $aVariable[] = $column;
                        }

                    }

                }

                //if class field isn't in array, and class isn't the min or max values, add
                if (!in_array($row[0], $aClasses) && $i != 0 && $row[0] != "min" && $row[0] != "max") {
                    $aClasses[] = $row[0];
                }

                if($row[0] == "min"){

                    //catch min values (columns in a row)
                    foreach($row as $key=>$minValue) {

                        //first column
                        if($key == 0){
                            continue;
                        }

                        $aMinMax["min"][] = $minValue;
                    }
                }

                if($row[0] == "max"){

                    //catch max values (columns in a row)
                    foreach($row as $key=>$minValue) {

                        //first column
                        if($key == 0){
                            continue;
                        }

                        $aMinMax["max"][] = $minValue;
                    }
                }

            }

            /** --------------- VARIABLES --------------- */

            if($bMlp && $idMlp) {
                foreach ($aVariable as $key=>$variable) {

                    //inserting the variables
                    $idVariable = DB::table('tbl_mlp_variable')->insertGetId(
                        ['id_mlp' => $idMlp,
                            'name' => $variable
                        ]
                    );

                    //inserting each max values of each variable
                    DB::table('tbl_min_max_values')->insert(
                        ['min_or_max' => "max",
                            'value' => $aMinMax["max"][$key],
                            'id_variable' => $idVariable
                        ]
                    );

                    //inserting each min values of each variable
                    DB::table('tbl_min_max_values')->insert(
                        ['min_or_max' => "min",
                            'value' => $aMinMax["min"][$key],
                            'id_variable' => $idVariable
                        ]
                    );

                }
            }

            /** --------------- CLASSIFICATIONS --------------- */

            if($bMlp && $idMlp) {
                foreach ($aClasses as $key=>$class) {

                    //the output number of each classification
                    $outputNumber = "";

                    //mounting the classification number
                    for($i=0 ; $i < count($aClasses) ; $i++){

                        //in color classification, the first classification is 1000, second 0100...
                        //considering that exists 4 classifications
                        if($key == $i){
                            $outputNumber .= "1";
                        } else {
                            $outputNumber .= "0";
                        }

                    }

                    DB::table('tbl_mlp_classification')->insert(
                        ['id_mlp' => $idMlp,
                            'name' => $class,
                            'output_number' => $outputNumber
                        ]
                    );
                }
            }

            MlpController::saveNetwork($aData, $objIntelligence->id);



            DB::commit();

            return response()->json("Intelligence Created");

        } catch (Exception $e){

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],500);
        }

    }


    /**
     * Get output, according inputs gave
     *
     */
    public function getOutput($inputs, $intelligenceId){

        $inputs = explode("-", $inputs);

        $rsMinMaxValues = DB::select("select * from tbl_min_max_values min_max
                              inner join tbl_mlp_variable variable on variable.id = min_max.id_variable
                              inner join tbl_mlp mlp on mlp.id = variable.id_mlp
                              where mlp.id_intelligence = {$intelligenceId};
                          ");

        foreach($rsMinMaxValues as $minMaxValue){

            if($minMaxValue->min_or_max == "max"){
                $aMax[] = $minMaxValue->value;
            }

            if($minMaxValue->min_or_max == "min"){
                $aMin[] = $minMaxValue->value;
            }

        }

        $inputs = MlpController::normalizeInput($inputs, $aMin, $aMax);

        $outputs = MlpController::getOutput($intelligenceId, $inputs);

        $rsClassifications = DB::select("select * from tbl_mlp_classification class
                              inner join tbl_mlp mlp on mlp.id = class.id_mlp
                              where mlp.id_intelligence = {$intelligenceId}
                              order by class.output_number desc;
                          ");

        foreach($rsClassifications as $classification){

            $aClassifications[] = $classification->name;

        }

        //index of greater value in classifications array
        $indexGreaterValue = 0;
        $greaterValue = $outputs[0];

        foreach($outputs as $key=>$output){
            if($greaterValue < $output){
                $indexGreaterValue = $key;
                $greaterValue = $output;
            }

        }

        return response()->json($aClassifications[$indexGreaterValue]);

    }


    /**
     * Return intelligence, according id gave
     *
     */
    public function getIntelligence($id){

        try {

            $objIntelligence = Intelligence::with('user', 'category', 'mlp', 'mlp.mlpVariable', 'mlp.mlpClassification')->find($id);

            //dd($objIntelligence->user);

            if (!is_object($objIntelligence))
                throw new Exception('Intelligence\'s id invalid or doesn\'t exist');

            return response()->json($objIntelligence);

        } catch (Exception $e){

            return response()->json(['returnMsg' => $e->getMessage()],400);

        }
    }

    /**
     * Delete intelligence, according id gave
     *
     */
    public function deleteIntelligence($id){

        try {

            DB::beginTransaction();

            $objIntelligence = Intelligence::find($id);

            if (!is_object($objIntelligence))
                throw new Exception('Intelligence\'s id invalid or doesn\'t exist');

            $objIntelligence->delete();

            //removing files
            unlink(DATA_TO_TRANING_FOLDER."{$id}.data");
            unlink(NETS_FOLDER."{$id}.net");

            DB::commit();

            return response()->json('Intelligence deleted');

        } catch(Exception $e){

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],400);

        }

    }

    /**
     * Update intelligence, according id and fields gave
     *
     */
    public function updateIntelligence(Request $request,$id){

        try {

            DB::beginTransaction();

            $objIntelligence = Intelligence::find($id);

            if (!is_object($objIntelligence))
                throw new Exception('Intelligence\'s id invalid or doesn\'t exist');

            $objIntelligence->name = $request->input('name') ? $request->input('name') : $objIntelligence->name;

            /** --------------- RESP_INC --------------- */
            if (!$request->input('userEmail'))
                throw new Exception('An email to identify the user is necessary');

            $objUser = DB::table('tbl_user')->where('email', 'like', $request->input('userEmail'))->first();

            if (!is_object($objUser))
                throw new Exception('Invalid user. Try with other user email');

            /** --------------- CATEGORY --------------- */

            $objIntelligence->id_resp_alt = $objUser->id;
            $objIntelligence->description = $request->input('description') ? $request->input('description') : $objIntelligence->description;

            $objIntelligence->save();

            DB::commit();

            return response()->json('Intelligence updated');

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],400);
        }
    }

    /**
     * Return intelligences by the id_resp_inc
     *
     */
    public function getAllIntelligencesPerUser($idUser){

        try {

            if (!$idUser)
                throw new Exception('A user id is necessary');

            $aObjIntelligence = DB::table('tbl_intelligence')->where('id_resp_inc', '=', $idUser)->get();

            if (!is_object($aObjIntelligence))
                throw new Exception('Invalid Intelligence Category. Try with other category');

            $aObjIntelligence = Intelligence::all();

            return response()->json($aObjIntelligence);

        } catch(Exception $e){

            return response()->json(['returnMsg' => $e->getMessage()],400);

        }

    }

}
