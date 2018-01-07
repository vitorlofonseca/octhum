<?php

namespace App\Http\Controllers;

use Mockery\CountValidator\Exception;

class MlpController extends Controller
{


    static public $desired_error = 0.001;
    static public $max_epochs = 500000;
    static public $epochs_between_reports = 1000;


    /**
     * Create a new mlp controller instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public static function getClassifications($sample){

        $aClassification = array();


        //looping in sample take classifications
        foreach($sample as $keyRow=>$row){

            //first position is the variables
            //second is the max values
            //third is the min values
            if(in_array($keyRow, array(0, 1, 2))){
                continue;
            }

            //looping in rows take classifications
            foreach($row as $keyField=>$field){

                //classifications are in 0 column
                if($keyField != 0){
                    continue;
                }

                //no repeated
                if(!in_array($field, $aClassification)) {
                    $aClassification[] = $field;
                }

            }

        }

        return $aClassification;
    }


    public static function getMinOrMaxValues($sample, $case){

        $aMinOrMax = array();

        //boolean to know if is the min or max values row
        $bMinOrMax = false;


        //looping in sample to take the max or min values of each input
        foreach($sample as $keyRow=>$row){

            //looping in row to take the max or min values of each input
            foreach($row as $keyField=>$field){

                if($field == $case && $keyField == 0){
                    //is the row of max or min values (depending the case)
                    $bMinOrMax = true;
                    continue;
                }

                //max values in fact (without "max" column)
                if($bMinOrMax) {
                    $aMinOrMax[] = $field;
                }

            }

            $bMinOrMax = false;
        }

        return $aMinOrMax;
    }


    public static function getVariables($sample){

        $aVariable = array();

        //looping in samples to catch variables
        foreach($sample as $keyRow=>$row){

            //looping in row to catch variables
            foreach($row as $keyField=>$field){

                //catching the variables in the sheet
                if($keyRow == 0 && $keyField != 0){
                    $aVariable[] = $field;
                }

            }

        }

        return $aVariable;
    }


    public static function shuffleSample($sample){

        srand((float)microtime()*1000000);
        shuffle($sample);

        return $sample;
    }


    /**
     * Linear normalization in all array
     *
     */
    public static function normalizeSample($sample, $aMin, $aMax){

        //looping in samples to normalize fields
        foreach($sample as $keyRow=>$row){

            //looping in row to catch variables
            foreach($row as $keyField=>$field){

                //expected output doesn't need normalized
                if($keyField == 0){
                    continue;
                }

                //-1 cause $aMin and $aMax, in example of color, have 3 positions (r, g, b), and $row have 4 (expected, r, g, b)
                //linear normalization
                $sample[$keyRow][$keyField] = ($field - $aMin[$keyField-1])/($aMax[$keyField-1] - $aMin[$keyField-1]);

            }

        }

        return $sample;
    }

    /**
     * Linear normalization in inputs
     *
     */
    public static function normalizeInput($inputs, $aMin, $aMax){

        //looping in samples to normalize fields
        foreach($inputs as $key=>$input){

            $input = str_replace(",", ".", $input);

            //-1 cause $aMin and $aMax, in example of color, have 3 positions (r, g, b), and $row have 4 (expected, r, g, b)
            //linear normalization
            $inputs[$key] = ($input - $aMin[$key])/($aMax[$key] - $aMin[$key]);

        }

        return $inputs;
    }


    /**
     * Codify an output index to readable output to neural network
     *
     */
    public static function codifyOutputIndex($indexArrayClassification, $numOutputs){

        $codifiedOutput = "";

        for($i=0 ; $i<$numOutputs ; $i++){

            //the unique position that is one, is the index of right output in the aExpectedValues
            if($i != $indexArrayClassification){
                $codifiedOutput .= 0;
            } else {
                $codifiedOutput .= 1;
            }

        }

        return $codifiedOutput;
    }



    /**
     * replacing expected outputs with readable outputs to neural network
     *
     */
    public static function replaceExpectedOutputs($sample, $aClassifications){

        //looping in samples to normalize fields
        foreach($sample as $keyRow=>$row){

            //looping in row to catch variables
            foreach($row as $keyField=>$field){

                //catching the index of this row variable
                if($keyField == 0){
                    $indexArrayClassification = array_search ($field, $aClassifications);
                }

                //replacing only classifications
                if($keyField != 0){
                    continue;
                }

                //transform an index in readable output to neural network
                $output = self::codifyOutputIndex($indexArrayClassification, count($aClassifications));

                //linear normalization
                $sample[$keyRow][$keyField] = $output;

            }

        }

        return $sample;
    }



    /**
     * Save train data in filesystem
     *
     */
    public static function saveTrainingData($sample, $intelligenceId, $num_input, $num_output){

        $net = fopen(DATA_TO_TRANING_FOLDER . "{$intelligenceId}.data", "w") or die("Unable to open file!");

        $content = "";

        //count of inputs in sample array
        $countInputs = count($sample);

        $content .= "{$countInputs} $num_input $num_output\n";

        //looping in samples to write .net
        foreach($sample as $keyRow=>$row){

            //looping in row to write inputs in .net
            foreach($row as $keyField=>$field){

                //the expected value will be writed in a different form
                if($keyField == 0){
                    continue;
                }

                $content .= "$field ";     //inputs
            }

            $content .= "\n";

            $content .= implode(' ',str_split($row[0])); //outputs ex.: 0 0 0 1

            $content .= "\n";

        }

        fwrite($net, $content);
        fclose($net);

    }

    /**
     * Use neural network after training
     *
     */
    public static function getOutput($fileName, $inputs){

        if(!$inputs){
            throw new Exception("Inputs are necessary");
        }

        $train_file = (NETS_FOLDER . "{$fileName}.net");
        if (!is_file($train_file))
            die("The file ".NETS_FOLDER . "{$fileName}.net"." has not been created!");

        $annUse = fann_create_from_file($train_file);
        if (!$annUse)
            die("ANN could not be created");

        $calc_out = fann_run($annUse, $inputs);

        fann_destroy($annUse);

        return $calc_out;

    }

    /**
     * Train a network, according the values passed by parameter
     *
     */
    public static function trainNetwork($intelligenceId, $num_layers, $num_input, $num_neurons_hidden, $num_neurons_hidden_2 = null, $num_output){

        //if have two hidden layers, set
        if($num_neurons_hidden_2){
            $ann = fann_create_standard($num_layers, $num_input, $num_neurons_hidden, $num_neurons_hidden_2, $num_output);
        } else {
            $ann = fann_create_standard($num_layers, $num_input, $num_neurons_hidden, $num_output);
        }

        if ($ann) {
            fann_set_activation_function_hidden($ann, FANN_SIGMOID_SYMMETRIC);

            $filename = DATA_TO_TRANING_FOLDER . "{$intelligenceId}.data";
            if (fann_train_on_file($ann, $filename, self::$max_epochs, self::$epochs_between_reports, self::$desired_error)) {

                if($num_neurons_hidden_2) {
                    $netFileName = NETS_FOLDER . "{$intelligenceId}.$num_input-$num_neurons_hidden-$num_neurons_hidden_2-$num_output.net";
                } else {
                    $netFileName = NETS_FOLDER . "{$intelligenceId}.$num_input-$num_neurons_hidden-$num_output.net";
                }

                fann_save($ann, $netFileName);

            }

            fann_destroy($ann);

        }

    }


    /**
     * Train and save network
     *
     */
    public static function saveNetwork($tmpName, $intelligenceId){

        $sample = array_map('str_getcsv', file($tmpName));

        //array that represent the max value of each column
        $aMax = self::getMinOrMaxValues($sample, "max");

        //array that represent the min value of each column
        $aMin = self::getMinOrMaxValues($sample, "min");

        //array of variables
        $aVariable = self::getVariables($sample);

        //array of variables
        $aClassifications = self::getClassifications($sample);

        unset($sample[0]);  //unset the header
        unset($sample[1]);  //unset the max/min row
        unset($sample[2]);  //unset the max/min row

        //replacing expected outputs with readable outputs to neural network
        $sample = self::replaceExpectedOutputs($sample, $aClassifications);

        $sample = self::shuffleSample($sample);

        //linear normalization
        $sample = self::normalizeSample($sample, $aMin, $aMax);

        //position to slice and take sample to test and to train
        $posToSlice = (count($sample)*20)/100;

        $sampleToTrain = array_slice($sample, $posToSlice, count($sample));
        $sampleToTest = array_slice($sample, 0, $posToSlice);

        //num of input neurons
        $num_input = count($aVariable);

        //num of output neurons
        $num_output = count($aClassifications);

        //save .data in the file system
        self::saveTrainingData($sampleToTrain, $intelligenceId, $num_input, $num_output);

        //array of architectures in the format
        //$hitPercentages[stringOfArchitecture]
        $hitPercentages = array();


        //loop representing the number of hidden layers
        for($i=1 ; $i<=2 ; $i++){

            //maximum quantity of neurons in the first layer
            $maxQuantityOfNeurons1stHddnLayer = $num_input*2;

            //loop representing the neuron's number of the first hidden layer
            while($maxQuantityOfNeurons1stHddnLayer > 1){

                //to case of two hidden layers
                if($i == 2){

                    $maxQuantityOfNeurons2ndHddnLayer = $maxQuantityOfNeurons1stHddnLayer-2;

                    //loop representing the neuron's number of the first hidden layer
                    while($maxQuantityOfNeurons2ndHddnLayer > 1){

                        $architecture = "$num_input-$maxQuantityOfNeurons1stHddnLayer-$maxQuantityOfNeurons2ndHddnLayer-$num_output";

                        //hit and error count to calculate the hit percentage, to decide the best architecture
                        $hitCount = 0;
                        $errorCount = 0;


                        // ------------------------- <TRAINING AND TEST> -------------------------

                        self::trainNetwork($intelligenceId, 4, $num_input, $maxQuantityOfNeurons1stHddnLayer, $maxQuantityOfNeurons2ndHddnLayer, $num_output);

                        foreach($sampleToTest as $key=>$inputs){

                            $expectedOutput = array_shift($inputs);

                            $fileName = "$intelligenceId.$num_input-$maxQuantityOfNeurons1stHddnLayer-$maxQuantityOfNeurons2ndHddnLayer-$num_output";

                            $outputs = self::getOutput($fileName, $inputs);

                            //index of greater value in classifications array
                            $output = str_pad("", count($outputs), "0", STR_PAD_LEFT);
                            $greaterValue = $outputs[0];
                            $indexGreaterValue = 0;

                            foreach($outputs as $keyOutput=>$outputTemp){
                                if($greaterValue < $outputTemp){
                                    $indexGreaterValue = $keyOutput;
                                    $greaterValue = $outputTemp;
                                }

                            }

                            //setting 1 in greater value of returned output
                            $output[$indexGreaterValue] = "1";

                            //if hit, count
                            if($expectedOutput == $output){
                                $hitCount++;
                            } else {
                                $errorCount++;
                            }

                        }

                        // ------------------------- <TRAINING AND TEST> -------------------------



                        $hitPercentage = ($hitCount)/(($errorCount+$hitCount)*100.0);

                        $hitPercentages[$architecture] = $hitPercentage;
                        //echo "Hit Percentage ($architecture): $hitPercentage (Hits: $hitCount | Errors: $errorCount)\n<br>";

                        $maxQuantityOfNeurons2ndHddnLayer = $maxQuantityOfNeurons2ndHddnLayer-2;
                    }

                }

                //to case of one hidden layers
                else {

                    $architecture = "$num_input-$maxQuantityOfNeurons1stHddnLayer-$num_output";

                    //hit and error count to calculate the hit percentage, to decide the best architecture
                    $hitCount = 0;
                    $errorCount = 0;


                    // ------------------------- <TRAINING AND TEST> -------------------------

                    self::trainNetwork($intelligenceId, 3, $num_input, $maxQuantityOfNeurons1stHddnLayer, null, $num_output);

                    foreach($sampleToTest as $key=>$inputs){

                        $expectedOutput = array_shift($inputs);

                        $fileName = "$intelligenceId.$num_input-$maxQuantityOfNeurons1stHddnLayer-$num_output";

                        $outputs = self::getOutput($fileName, $inputs);

                        //index of greater value in classifications array
                        $output = str_pad("", count($outputs), "0", STR_PAD_LEFT);
                        $greaterValue = $outputs[0];
                        $indexGreaterValue = 0;


                        foreach($outputs as $keyOutput=>$outputTemp){
                            if($greaterValue < $outputTemp){
                                $indexGreaterValue = $keyOutput;
                                $greaterValue = $outputTemp;
                            }

                        }

                        //setting 1 in greater value of returned output
                        $output[$indexGreaterValue] = "1";

                        //if hit, count
                        if($expectedOutput == $output){
                            $hitCount++;
                        } else {
                            $errorCount++;
                        }

                    }

                    // ------------------------- <TRAINING AND TEST> -------------------------



                    $hitPercentage = ($hitCount)/(($errorCount+$hitCount)*100.0);

                    $hitPercentages[$architecture] = $hitPercentage;
                    //echo "Hit Percentage ($architecture): $hitPercentage (Hits: $hitCount | Errors: $errorCount)\n<br>";

                }

                //decrementing 2 of neurons first hidden layer's quantity
                $maxQuantityOfNeurons1stHddnLayer = $maxQuantityOfNeurons1stHddnLayer-2;
            }


        }

        $greaterHitPercentage = reset($hitPercentages);
        $bestArchitecture = key($hitPercentages);

        //choosing a network by your hit percentage
        foreach($hitPercentages as $architecture=>$hitPercentage){

            if($greaterHitPercentage < $hitPercentage){
                $bestArchitecture = $architecture;
                $greaterHitPercentage = $hitPercentage;
            }

        }

        //deleting networks in the folder, and renaming the best network to use
        foreach($hitPercentages as $architecture=>$hitPercentage){

            //delete the others architectures
            if($bestArchitecture != $architecture){
                unlink(NETS_FOLDER . "{$intelligenceId}.$architecture.net");
            }

            //rename best architecture
            else {
                rename(NETS_FOLDER . "{$intelligenceId}.$architecture.net", NETS_FOLDER . "{$intelligenceId}.net");
            }

        }

        return;

    }


}
