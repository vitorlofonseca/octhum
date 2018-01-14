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

    public function createIntelligence(Request $request){

        try{

            DB::beginTransaction();

            /** --------------- RESP_INC --------------- */
            if (!$request->input('userEmail'))
                throw new Exception('An email to identify the user is necessary');

            $objUser = DB::table('tbl_user')->where('email', 'like', $request->input('userEmail'))->first();

            if (!is_object($objUser))
                throw new Exception('Invalid user. Try with other user email');

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


            /** --------------- INTELLIGENCE FILE TYPE --------------- */

            if (!$request->input('fileType'))
                throw new Exception('A file type is necessary');

            $request['id_file_type'] = $request->input('fileType');

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

            /** --------------- FILE --------------- */

            if($request->input('fileType') == ID_INTELLIGENCE_TYPE_FILE_SHEET) {

                if (!$request->fileTraining) {
                    throw new Exception("A file is necessary to train the intelligence");
                }

                $tmpName = $request->fileTraining;

                $csvAsArray = array_map('str_getcsv', file($tmpName));

                $filePath = SHEETS_TO_TRANING_FOLDER . "{$objIntelligence->id}.csv";

                $fp = fopen($filePath, 'w');

                //classes (distinct of class column in the sheet)
                $aClasses = array();

                //variables (columns, less class column)
                $aVariable = array();


                foreach ($csvAsArray as $i => $row) {

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

                    //if class field isn't in array, add
                    if (!in_array($row[0], $aClasses) && $i != 0) {
                        $aClasses[] = $row[0];
                    }

                    fputcsv($fp, $row);
                }

                /** --------------- VARIABLES --------------- */

                if($bMlp && $idMlp) {
                    foreach ($aVariable as $variable) {
                        DB::table('tbl_mlp_variable')->insert(
                            ['id_mlp' => $idMlp,
                                'name' => $variable
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

                MlpController::saveNetwork($tmpName, $objIntelligence->id);

                chmod($filePath, 0777);

                fclose($fp);

                //C++ MLP with a bug in synapsis correction
                //$statusCodeMlp = exec(EXEC_MLP . " filePath={$filePath} intelligenceId={$objIntelligence->id}");

            } else {

                //temp error
                throw new Exception("File type inactive. Select Sheet");
            }

            DB::commit();

            return response()->json("Intelligence Created");

        } catch (Exception $e){

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],400);
        }

    }

    public function getOutput($inputs, $intelligenceId){

        $inputs = explode("-", $inputs);

        $sample = array_map('str_getcsv', file(SHEETS_TO_TRANING_FOLDER."{$intelligenceId}.csv"));

        //array that represent the max value of each column
        $aMax = MlpController::getMinOrMaxValues($sample, "max");

        //array that represent the min value of each column
        $aMin = MlpController::getMinOrMaxValues($sample, "min");

        $inputs = MlpController::normalizeInput($inputs, $aMin, $aMax);


        /*
        foreach(explode("and", $inputs) as $strInput){

            //verifying if prefix contains id
            if(strpos($strInput, "neuron") === false) {

                //TODO: IMPLEMENT THE RECOGNIZE BY NAME
                throw new Exception("The index of inputs are invalid. Should be 'neuron_X', where X is the id of neuron");
            }

        }

        $date = date("Y-m-d_H:i:s");

        $output = exec(EXEC_MLP . " inputs={$inputs} date={$date} intelligenceId={$intelligenceId}");
        */

        $outputs = MlpController::getOutput($intelligenceId, $inputs);

        $aClassifications = MlpController::getClassifications($sample);

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
            unlink(SHEETS_TO_TRANING_FOLDER."{$id}.csv");

            DB::commit();

            return response()->json('Intelligence deleted');

        } catch(Exception $e){

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],400);

        }

    }

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

            DB::rollBack();

            return response()->json('Intelligence updated');

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json(['returnMsg' => $e->getMessage()],400);
        }
    }


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
