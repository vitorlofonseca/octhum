//============================================================================
// Name        : cetus-mlp.cpp
// Author      : vitorlofonseca
// Version     :
// Copyright   : Your copyright notice
// Description :
// Compilation : g++ cetus.mlp.cc Classes/Layer.cpp Classes/Synapse.cpp Classes/Neuron.cpp -o cetus.mlp
// Training    : "intelligenceId="
// Use         : "inputs=neuron_46:1-neuron_47:2-neuron_48:3" "date=2017-11-11_17:56:50" "intelligenceId=35"
//============================================================================

#include <iostream>

#include "Headers/Neuron.h"
#include "Headers/Layer.h"
#include "Headers/Synapse.h"

#include <boost/config.hpp>
#include <boost/property_tree/ptree.hpp>
#include <boost/property_tree/ini_parser.hpp>

#include <cmath>
#include <utility>

//fstream to read sheets, if necessary
typedef basic_ifstream<char> ifstream;
#include <iostream>
#include <sstream>
#include <fstream>
#include <string>

using namespace std;
#include <vector>

#include <algorithm>
#include <bitset>	

#include <iomanip>      // std::setprecision

#define LEARNING_RATE 0.5

//the acceptable margin of error to outputs
#define ACCEPTABLE_ERROR 0.04


/** ---------------------- FUNCTIONS ---------------------- **/


/**
 * adjust the weights in the synapsis, based in the total error
 * 
 * @param network
 */
void adjustWeights(vector<Layer> *network){ 
        
    cout << endl << "Weights adjust:" << endl << endl;
    
    int LAYERS_NUM = network->size();

    //iterating in the layers
    for(int i=LAYERS_NUM-2 ; i > -1 ; i--){
    
        vector<Neuron> neuronsLayer = network->at(i).getADestinationNeuron();
        
        //quantity of neurons in each layer depending your type
        int qtdNeuronsPerLayer = network->at(i+1).getQtdNeuronsNoBia();
    
        //iterating in the neurons 
        for(int j=0 ; j<qtdNeuronsPerLayer ; j++){
            
            //vector that have as destination neuron, the neuron of this loop
            vector<Synapse> vecSynapsisByDestNeuron = network->at(i).getASynapseByDestinationNeuron(neuronsLayer.at(j));
            vector<Synapse>::iterator itrSynapsisByNeuron = vecSynapsisByDestNeuron.begin();
            
            //updating the weights 
            while(itrSynapsisByNeuron != vecSynapsisByDestNeuron.end()){
                
                float error = 0;
                float delta = 0;
                float outputDestinationNeuron = itrSynapsisByNeuron->getDestinationNeuron()->getOutput();
                float expectedValue = itrSynapsisByNeuron->getDestinationNeuron()->getExpectedValue();
                float outputSourceNeuron = itrSynapsisByNeuron->getSourceNeuron()->getOutput();
                
                //if the layer to be an output layer, the error is calculated different
                if(network->at(i+1).getBOutput()){
                    error = outputDestinationNeuron * (1 - outputDestinationNeuron) * (expectedValue - outputDestinationNeuron);
                } else {
                    
                    //collecting the synapsis of the current neuron
                    vector<Synapse> vecSynapsisNextLayerBySourceNeuron = network->at(i+1).getASynapseBySourceNeuron(*(itrSynapsisByNeuron->getDestinationNeuron()));
                    vector<Synapse>::iterator itrSynapsisNextLayerBySourceNeuron = vecSynapsisNextLayerBySourceNeuron.begin();

                    float inverseOutput = 0;
                    
                    //iterating in the next layer synapsis to calculate the error of the hidden layer
                    while(itrSynapsisNextLayerBySourceNeuron != vecSynapsisNextLayerBySourceNeuron.end()){
                        
                        float outputDestinationNeuronTemp = itrSynapsisNextLayerBySourceNeuron->getDestinationNeuron()->getOutput();
                        float expectedValueTemp = itrSynapsisNextLayerBySourceNeuron->getDestinationNeuron()->getExpectedValue();
                        float outputSourceNeuronTemp = itrSynapsisNextLayerBySourceNeuron->getSourceNeuron()->getOutput();
                        
                        error = outputDestinationNeuronTemp * (1 - outputDestinationNeuronTemp) * (expectedValueTemp - outputDestinationNeuronTemp);
                        
                        float weight = itrSynapsisNextLayerBySourceNeuron->getWeight();
                        
                        inverseOutput += error * weight;
                        
                        itrSynapsisNextLayerBySourceNeuron++;
                    }                 
                    
                    error = (1.0 - outputDestinationNeuron);
                    error = error * outputDestinationNeuron;
                    error = error * inverseOutput;
                    
                }
                
                delta = LEARNING_RATE * error * outputSourceNeuron;
                
                float newWeight = itrSynapsisByNeuron->getWeight() + delta;
                
                cout << "Old Weight synapse #" << itrSynapsisByNeuron->getId() << ": " << itrSynapsisByNeuron->getWeight() << endl;
                cout << "New Weight synapse #" << itrSynapsisByNeuron->getId() << ": " << newWeight << endl;
                cout << endl;
                
                network->at(i).updateWeightBySynapse(*itrSynapsisByNeuron, newWeight);
                
                itrSynapsisByNeuron++;
            }
            
        }
        
    }
    
}


/**
 * calc of networks total error, and return true to continue the training
 *  
 * @param network
 * @return 
 */
bool bContinueTraining(vector<Layer> *network){
    
    int LAYERS_NUM = network->size();
    
    //collecting the neurons of the current layer (the first layer's neuron's outputs are pre-defined)
    vector<Neuron> neuronsLayer = network->at(LAYERS_NUM-2).getADestinationNeuron();
    vector<Neuron>::iterator itrNeurons = neuronsLayer.begin();
    
    float error = 0;

    //iterating in the neurons of hidden layers
    while(itrNeurons != neuronsLayer.end()) {
        
        error = (1.0/2.0) * pow((itrNeurons->getExpectedValue() - itrNeurons->getOutput()), 2);

        if(error > ACCEPTABLE_ERROR || error < ACCEPTABLE_ERROR){
            return true;
        }
        
        itrNeurons++;
    }
    
    return false;
    
}

/**
 * Execute the forward pass in the training
 * 
 * @param network
 */
void forward(vector<Layer> *network){
    
    cout << "Forward pass: " << endl << endl;
    
    int LAYERS_NUM = network->size();
    
    //iterating in the network's layers
        for(int i=0 ; i<LAYERS_NUM ; i++){
            
            //if is the first loop, continue, cause the outputs of neurons layer already are calculed
            if(i == 0){
                continue;
            }
                
            //collecting the neurons of the current layer (the first layer's neuron's outputs are pre-defined)
            vector<Neuron> neuronsLayer = network->at(i-1).getADestinationNeuron();
            vector<Neuron>::iterator itrNeurons = neuronsLayer.begin();
            
            //index to know which neuron is
            int countNeurons = 0;

            //iterating in the neurons of hidden layers
            while(itrNeurons != neuronsLayer.end()) {
                
                //if neuron be a bia, continue, cause bia's output already is defined (1)
                if(!itrNeurons->getBBia()){ 

                    //collecting the synapsis source of the previous neuron
                    vector<Synapse> synapsisLayer = network->at(i-1).getASynapseByDestinationNeuron(*itrNeurons);
                    vector<Synapse>::iterator itrSynapsis = synapsisLayer.begin();

                    //input of the current neuron, for the calc the output
                    float input = 0;

                    //iterating in the synapsis of hidden layers
                    while(itrSynapsis != synapsisLayer.end()) {

                        cout << std::setprecision(30) << itrSynapsis->getWeight() << " * " << itrSynapsis->getSourceNeuron()->getOutput() << " (neuronId: " << itrSynapsis->getSourceNeuron()->getId() << ")"<< endl;

                        input += itrSynapsis->getWeight() * itrSynapsis->getSourceNeuron()->getOutput();

                        itrSynapsis++;
                    }

                    float output;
                    
                    output = 1/(1+exp(-input));

                    cout << "new output neuron #" << itrNeurons->getId() << ": " << output << endl;
                    
                    network->at(i-1).updateOutputNeuron(*itrNeurons, output);
                    
                    //the last layer doesn't have synapsis' vector
                    if(i != LAYERS_NUM-1){
                        network->at(i).updateOutputNeuron(*itrNeurons, output);
                    }
                    
                    //</setting the outputs in the layer's neurons>

                    cout << "" << endl; 
                }

                ++itrNeurons;
                countNeurons++;

            }

            //itrLayers->setASynapse(aSynapseTemp);

            //(*network)[0] = *itrLayers;
        }
    
}


/**
 * Function that to train the network, adjusting the synapsis' weights
 * 
 * vector<Layer> *network - network created in main function
 * vector<vector<float>> vecInput - input vector
 * vector<vector<int>> vecExpectedOutputs - expected output vector of vector to each input (1000 to red, 0100 to blue, 0010 to yellow...)
 * 
 */
void toTrainNetwork(vector<Layer> *network, vector<vector<float>> vecInput, vector<vector<int>> vecExpectedOutputs){ 
    
    int LAYERS_NUM = network->size();
    
    //while there is sampling, loop
    for(int i=0 ; i<vecInput.size() ; i++){
    
        //setting the inputs in the layer input neurons
        vector<Synapse> aSynapseTemp = network->at(0).getASynapse();
        vector<Synapse>::iterator itrSynapse = aSynapseTemp.begin();

        //neurons count to know if jump neuron or no
        int countNeuron = 0;
        
        //the signal of synapse should be the same signal of all synapsis with the same neuron
        int indexInput = 0;
        int flagChangeIndex = 1;

        //index to know which neuron to upload
        int countUpdatedNeuron = 0;

        //setting de neuron input from sheet inputs
        while(itrSynapse != aSynapseTemp.end()){
            
            //if flag to be equal number of neurons of the second layer (combination of synapsis), 
            //change the index of input
            if(flagChangeIndex == network->at(1).getQtdNeurons()){
                flagChangeIndex = 1;
                indexInput++;
            }

            //if the neuron is a bia, the output is 1
            if(itrSynapse->getSourceNeuron()->getBBia()){
                network->at(0).updateOutputNeuron(*(itrSynapse->getSourceNeuron()), 1);
            } else {
                network->at(0).updateOutputNeuron(*(itrSynapse->getSourceNeuron()), vecInput.at(i).at(indexInput)); 
            }

            flagChangeIndex++;
            countUpdatedNeuron++;
            countNeuron++;
            itrSynapse++;
            
        }
        
        // ----------- setting the expected values to output ----------- 
        //-2, cause the last layer doesn't have a synapsis neuron (catching the penultimate layer)
        vector<Neuron> aNeuronTemp = network->at(LAYERS_NUM-2).getADestinationNeuron();
        vector<Neuron>::iterator itrNeuron = aNeuronTemp.begin();

        //index to upload the neuron
        countUpdatedNeuron = 0;

        while(itrNeuron != aNeuronTemp.end()){

            float expectedValue = (float)vecExpectedOutputs.at(i).at(countUpdatedNeuron);
            
            network->at(LAYERS_NUM-2).updateExpectedOutput(*itrNeuron, expectedValue);

            countUpdatedNeuron++;
            itrNeuron++;
        }        
        
        
        
        forward(network);
        
        //collecting the neurons of the current layer (the first layer's neuron's outputs are pre-defined)
        vector<Neuron> neuronsLayer = network->at(LAYERS_NUM-2).getADestinationNeuron();
        vector<Neuron>::iterator itrNeurons = neuronsLayer.begin();

        //iterating in the neurons of hidden layers
        while(itrNeurons != neuronsLayer.end()) {

            cout << "final outputs: " << itrNeurons->getOutput() << endl;

            itrNeurons++;
        }
        
        //if the output doesn't according with error limit, balance the weights
        if(bContinueTraining(network)){
            adjustWeights(network);
        } else {
            cout << "Adjust weights jumped!" << endl << endl;
        }
         

    }

}


/**
 * Function to initialize the weights, and create the neurons
 *
 * vector<Layer> *network           - pointer to the network created in the main function
 * vector<int> vecQuantityNeurons   - vector of neurons quantity of each layer
 * vector<float> vecInitialWeights  - vector of initial weights to synapsis
 * bool bBia                        - boolean to know if network will have bias
 * 
 */
void initializeMlp(vector<Layer> *network, vector<int> vecQuantityNeurons, vector<float> vecInitialWeights, bool bBia){
    
    //initializing the layers
    for(int i=0 ; i<vecQuantityNeurons.size() ; i++){
        
        //layer i
        Layer layer;
        layer.setId(i);
        
        //output layer
        if(i == vecQuantityNeurons.size()-1){
            layer.setBOutput(true);
            layer.setBInput(false);
            layer.setBHidden(false);
        } 
        
        //input layer
        else if (i == 0){
            layer.setBOutput(false);
            layer.setBInput(true);
            layer.setBHidden(false);
        } 
        
        //hidden layer
        else {
            layer.setBOutput(false);
            layer.setBInput(false);
            layer.setBHidden(true);
        }
        
        //setting the number of neurons defined in the start of main
        layer.setQtdNeurons(vecQuantityNeurons.at(i));
        
        network->push_back(layer);
        
    }

    int countNeuron = 0;
    
    //temporary network, to mount the synapsis (layers simulation)
    vector<vector<Neuron>> aLayer;

    //atributing neurons to your respectives layers
    for(int i=0 ; i<vecQuantityNeurons.size() ; i++){
        
        //neurons array, to insert in layers simulation
        vector<Neuron> aNeuronTemp;
        
        //quantity of neurons in each layer depending your type
        int qtdNeuronsPerLayer = network->at(i).getQtdNeurons();

        //number of neurons (counting with bia)
        for (int j=0 ; j<qtdNeuronsPerLayer ; j++){          

            //neuron for add in the respective layer
            Neuron neuron;
            bool bInsert = false;		//boolean to know if neuron must to be inserted (bug with output layer and bia)

            //if is not the output layer (there isn't bia in output layer), and is the last neuron, insert the bia
            if(network->at(i).getBOutput() != true && j == qtdNeuronsPerLayer-1 && bBia){
                neuron.setId(countNeuron);
                neuron.setIdLayer(i);
                neuron.setOutput(1);
                neuron.setBBia(true);
                bInsert = true;

            }

            //if j isn't the last loop (the loop destined to bia's creation), create a "normal" neuron
            // OR if the boolean bia is selected and is the last loop (the last layer doesn't have bia, only normal neurons)
            else {
                neuron.setId(countNeuron);
                neuron.setIdLayer(i);
                neuron.setOutput(-1);
                neuron.setBBia(false);
                bInsert = true;
            }

            //if a new neuron was created, insert
            if(bInsert){
                
                //adding neuron in the array of neurons
                aNeuronTemp.push_back(neuron);

                //counting neurons in the neural net (to insert in id)
                countNeuron++;
            }

        }
        
        aLayer.push_back(aNeuronTemp);
    }

    int countSynapsis=0;
    

    //atributing synapsis to your respectives layers (-1 cause in the front of output's layer doesn't exists synapsis)
    for(int i=0 ; i<vecQuantityNeurons.size()-1 ; i++){

        //neurons of this current "layer"
        vector<Neuron> neuronsCurLayer = aLayer.at(i);
        vector<Neuron>::iterator itrNeuronCurLayer = neuronsCurLayer.begin();

        //neurons of the next"layer"
        vector<Neuron> neuronsNextLayer = aLayer.at(i+1);
        vector<Neuron>::iterator itrNeuronNextLayer = neuronsNextLayer.begin();

        //iterating in the current layer
        while(itrNeuronCurLayer != neuronsCurLayer.end()) {

            itrNeuronNextLayer = neuronsNextLayer.begin();

            //iterating in the next layer to get the neurons' ids
            while(itrNeuronNextLayer != neuronsNextLayer.end()) {

                //doesn't exist synapsis between neuron -> bia, but bia -> neuron
                if(!itrNeuronNextLayer->getBBia()){

                    float weight = vecInitialWeights.at(countSynapsis);

                    //making the synapsis
                    Synapse synapse;
                    synapse.setId(countSynapsis);
                    synapse.setWeight(weight);                 
                    synapse.setDestinationNeuron(*itrNeuronNextLayer);
                    synapse.setSourceNeuron(*itrNeuronCurLayer);  
                    network->at(i).addSynapse(synapse);

                    countSynapsis++;
                }

                ++itrNeuronNextLayer;

            }
            ++itrNeuronCurLayer;
        }

    }

}






/**
 * Get MLP network from .ini
 * 
 * @param intelligenceId - to get .ini file
 */
vector<float> getIniWeights(string intelligenceId){
    
    vector<float> vecInitialWeights;
    
    string DIR_CONFIG_FILES = "/var/www/html/cetus/modules/cetus.mlp/intelligenceFiles/configs/";
    string configFilePath = DIR_CONFIG_FILES+"config_intelligence_mlp_"+intelligenceId+".ini";
    
    //parser of ini config
    boost::property_tree::ptree pt;
    boost::property_tree::ini_parser::read_ini(configFilePath, pt);
    
    boost::property_tree::ptree::const_iterator endLayer = pt.end();
    
    //iterating into tree created based in .ini conf
    for(boost::property_tree::ptree::const_iterator it = pt.begin() ; it != endLayer ; ++it){
        
        //if the parameter is the number of neurons, dont catch
        std::size_t found = it->first.find("hidden_layer");
        if (found==std::string::npos){
            float weightTemp = it->second.get_value<float>();
        
            //adding weights to weights vector
            vecInitialWeights.push_back(weightTemp);
        }
        
    }
    
    return vecInitialWeights;
    
}






/**
 * Get neurons quantity of MLP from .ini
 * 
 * @param intelligenceId - to get .ini file
 */
vector<int> getQttNeuronLayers(string intelligenceId){
    
    vector<int> vecQttNeurons;
    
    string DIR_CONFIG_FILES = "/var/www/html/cetus/modules/cetus.mlp/intelligenceFiles/configs/";
    string configFilePath = DIR_CONFIG_FILES+"config_intelligence_mlp_"+intelligenceId+".ini";
    
    //parser of ini config
    boost::property_tree::ptree pt;
    boost::property_tree::ini_parser::read_ini(configFilePath, pt);
    
    boost::property_tree::ptree::const_iterator endLayer = pt.end();
    
    //iterating into tree created based in .ini conf
    for(boost::property_tree::ptree::const_iterator it = pt.begin() ; it != endLayer ; ++it){
        
        //if the parameter is the number of neurons, dont catch
        std::size_t found = it->first.find("hidden_layer");
        if (found!=std::string::npos){
            
            int qttNeuron = it->second.get_value<float>();
        
            //adding weights to weights vector
            vecQttNeurons.push_back(qttNeuron);
        }
        
    }
    
    return vecQttNeurons;
    
}



/**
 * Get MLP process and return the output
 * 
 * @param network
 * @param vecInput - input vector
 */
int getOutput(vector<Layer> *network, vector<vector<float>> vecInput){
    
    int LAYERS_NUM = network->size();
    
    //setting the inputs in the layer input neurons
    vector<Synapse> aSynapseTemp = network->at(0).getASynapse();
    vector<Synapse>::iterator itrSynapse = aSynapseTemp.begin();

    //the signal of synapse should be the same signal of all synapsis with the same neuron
    int indexInput = 0;
    int flagChangeIndex = 1;

    //index to know which neuron to upload
    int countUpdatedNeuron = 0;

    //setting of neuron input from sheet inputs
    while(itrSynapse != aSynapseTemp.end()){

        //if flag be equal number of neurons of the second layer (combination of synapsis), 
        //change the index of input
        if(flagChangeIndex == network->at(1).getQtdNeurons()){
            flagChangeIndex = 1;
            indexInput++;
        }

        //if the neuron is a bia, the output is 1
        if(itrSynapse->getSourceNeuron()->getBBia()){
            network->at(0).updateOutputNeuron(*(itrSynapse->getSourceNeuron()), 1);
        } else {
            network->at(0).updateOutputNeuron(*(itrSynapse->getSourceNeuron()), vecInput.at(0).at(indexInput)); 
        }

        flagChangeIndex++;
        countUpdatedNeuron++;
        itrSynapse++;

    }

    forward(network);

    //collecting the neurons of the current layer (the first layer's neuron's outputs are pre-defined)
    vector<Neuron> neuronsLayer = network->at(LAYERS_NUM-2).getADestinationNeuron();
    vector<Neuron>::iterator itrNeurons = neuronsLayer.begin();
    
    //greatest output between the output neurons
    float greatestOutput = 0;
    int i = 0;
    int output;

    //iterating in the neurons of hidden layers
    while(itrNeurons != neuronsLayer.end()) {
        
        //if current output be greater than greatest, update output index, to use in classifications array
        if(greatestOutput < itrNeurons->getOutput()){
            output = i;
            greatestOutput = itrNeurons->getOutput();
        }

        cout << "final outputs: " << itrNeurons->getOutput() << endl;

        i++;
        itrNeurons++;
    }
    
    return output;
    
}


/**
 * Mount and save a mlp config file to use
 * 
 * @param network
 * @param intelligenceId
 */
void makeConfigMlp(vector<Layer> *network, string intelligenceId){
    
    string DIR_CONFIG_FILES = "/var/www/html/cetus/modules/cetus.mlp/intelligenceFiles/configs/";
    
    string configFilePath = DIR_CONFIG_FILES+"config_intelligence_mlp_"+intelligenceId+".ini";
    ofstream redirect_file(configFilePath);

    // save output buffer of cout
    streambuf * strm_buffer = cout.rdbuf();

    // redirect output into file
    cout.rdbuf(redirect_file.rdbuf());
    
    // ------------------------- <mounting the config file> -------------------------
    
    vector<Layer>::iterator itrLayer = network->begin();
    
    //iterating in the layers
    while(itrLayer != network->end()) {
        
        //neurons of this loop's layer
        vector<Synapse> vecSynapse = itrLayer->getASynapse();
        vector<Synapse>::iterator itrSynapse = vecSynapse.begin();
        
        while(itrSynapse != vecSynapse.end()) {
            
            cout << itrSynapse->getId() << " = " << itrSynapse->getWeight() << endl;
            
            itrSynapse++;
        }
        
        ++itrLayer;
    }
    
    //saving the count of neurons of each layer
    for(int i=1 ; i<network->size()-1 ; i++){
        cout << "hidden_layer_" << i-1 << " = " << network->at(i).getQtdNeurons() << endl;
    }
    
    // ------------------------- </mounting the config file> -------------------------

    // restore old buffer
    cout.rdbuf(strm_buffer);
   
    return;
}

/**
 * Explode an string in array, by the ch
 * 
 * https://stackoverflow.com/questions/890164/how-can-i-split-a-string-by-a-delimiter-into-an-array
 * 
 */ 
vector<string> explode(const string& str, const char& ch) {
    string next;
    vector<string> result;

    // For each character in the string
    for (string::const_iterator it = str.begin(); it != str.end(); it++) {
        // If we've hit the terminal character
        if (*it == ch) {
            // If we have some characters accumulated
            if (!next.empty()) {
                // Add them to the result vector
                result.push_back(next);
                next.clear();
            }
        } else {
            // Accumulate the next character into the sequence
            next += *it;
        }
    }
    if (!next.empty())
         result.push_back(next);
    return result;
}


/** ---------------------- FUNCTIONS ---------------------- **/

/**
 * Intermediate function, that connect initialing function, training function ... save weights function
 *
 **/
int main (int argc, char *variables[]){
    
    /** return error and success codes */
    int MLP_TRAINED_WITH_SUCCESS = 0001;
    int MLP_EMPTY_INTELLIGENCE_ID = 1002;
    
    
    //VARIABLES NAMES COMING FROM API
    string INPUTS = "inputs";
    string DATE = "date";
    string INTELLIGENCE_ID = "intelligenceId";
    string DIR_LOGS_FILE = "/var/www/html/cetus/modules/cetus.mlp/intelligenceFiles/logs/";
    string FILES_FOLDER = "/var/www/html/cetus/files/";
    string SHEETS_TO_TRANING_FOLDER = FILES_FOLDER+"training_data/sheets/";
    
    //VARIABLES CONTENT (IF IS EMPTY, THROW EXCEPTION)
    string intelligenceId = "";
    string inputs = "";
    string date = "";
    
    for(int i=1 ; i<=argc-1 ; i++){
        
        //brute parameter "intelligenceId=3"
        string parameter = variables[i];
        
        string variable = parameter.substr(0, parameter.find("=")); 
        string value = parameter.substr(parameter.find("=") + 1);
        
        if(!variable.compare(INTELLIGENCE_ID)){
            intelligenceId = value;
        }
        
        if(!variable.compare(INPUTS)){
            inputs = value;
        }
        
        if(!variable.compare(DATE)){
            date = value;
        }
        
    }
    
    // -------------------- <RETURN ERROR CODES> --------------------
    
    if(!intelligenceId.compare("")){
        cout << MLP_EMPTY_INTELLIGENCE_ID;
        return 0;
    }
    
    //true if is training, false if is for use
    bool bTraining = !inputs.compare("");
    
    // -------------------- </RETURN ERROR CODES> --------------------   
    
    
    string filePath = SHEETS_TO_TRANING_FOLDER+"intelligence_"+intelligenceId+".csv";
    
    
    // -------------------- <VARIABLES TO NETWORK> --------------------   
    //vector of neural network's layers
    vector<Layer> *network = new vector<Layer>[3];
    
    //vector of inputs to test
    vector<vector<float>> vecInput;
    
    //vector of expected outputs to each input
    vector<vector<int>> vecExpectedOutputs;
    
    //variables in the sheet. In the decision of color, for example, the variables are the RGB values
    int countVariables = 0;
    
    //classifications array (in the class column
    std::vector<std::string> classifications;
    // -------------------- </VARIABLES TO NETWORK> --------------------   
    
    //if inputs is setted, this instance is for use
    if(!bTraining){
        
        // -------------------- <INPUTS FROM API> --------------------   
        //input vector from api
        std::vector<std::string> vecInputFromAPI = explode(inputs, '-');
        
        vector<float> vecInputUse;

        for (size_t i = 0; i < vecInputFromAPI.size(); i++) {
            string inputStrTemp = vecInputFromAPI[i].substr(vecInputFromAPI[i].find(":") + 1);
            string::size_type sz;
            float inputFltTemp = stof(inputStrTemp, &sz);
            
            vecInputUse.push_back(inputFltTemp);
        }
        
        vecInput.push_back(vecInputUse);
        // -------------------- <INPUTS FROM API> --------------------   
            
    }    
    
    
    //------------------------ <CATCHING THE CLASSIFICATIONS ON THE SHEET> ----------------------------    
    bool bMaxLine;
    bool bMinLine;
    
    //csv file that we will read
    ifstream data (filePath); 

    bool bHeader = true;

    string line;
    while(getline(data,line)){ 

        //skip the header
        if(bHeader){
            bHeader = false;
            continue;
        }

        stringstream  lineStream(line);
        string        cell;

        int indexCell = 0;

        while(getline(lineStream,cell,',')){

            //taking off the double quotes of the cells
            //cell.erase(0, 1);             // erase the first character (")
            //cell.erase(cell.size() - 1);  // erase the last character  (")

            //column classification, in classifications array
            if(indexCell == 0){
                
                bMaxLine = cell == "max";
                bMinLine = cell == "min";

                //if classification is not in classifications array
                //and not be the max/min column
                //add to classifications array
                if (find(classifications.begin(), classifications.end(), cell) == classifications.end() && (!bMinLine && !bMaxLine)) {
                    // someName not in name, add it
                    classifications.push_back(cell);
                }
            }

            indexCell++;
        }

    }
    //------------------------ </CATCHING THE CLASSIFICATIONS ON THE SHEET> ----------------------------
    
    
    
    
    //------------------------ <CATCHING THE INPUTS ON THE SHEET> ----------------------------
    bHeader = true;
            
    //csv file that we will read
    ifstream data2 (filePath);
    line = "";
    
    //min and max values of each variable
    vector<float> minValues;
    vector<float> maxValues;
    
    while(getline(data2,line)){
            
        //skip the header
        if(bHeader){
            bHeader = false;
            continue;
        }
        
        stringstream  lineStream(line);
        string        cell;
        
        int indexCell = 0;
        
        //vector of input (the vector that will be passed to the network, is a vector of this).
        vector<float> input;
        
        //temp to see the highest value, and assign to the original variable
        int countVariablesTemp = 0;
        
        //each expected output vector
        // ex.: 1000 to red, 0100 to blue, 0010 to yellow, and etc
        vector<int> expectedOutput;
        
        string firstColum = "";
        
        while(getline(lineStream,cell,',')){
            
            if(indexCell == 0){                
                firstColum = cell;
            }
            
            bMaxLine = firstColum == "max";
            bMinLine = firstColum == "min";
            
            //if is the min line, store the min values of each input
            if(bMinLine){
                if(cell != "min"){
                    float floatCell = strtof((cell).c_str(),0);
                    minValues.push_back(floatCell);
                }
            }
            
            //if is the min line, store the min values of each input
            if(bMaxLine){
                if(cell != "max"){
                    float floatCell = strtof((cell).c_str(),0);
                    maxValues.push_back(floatCell);
                }
            }
            
            //the variables are the columns of sheet, less the column of classification (-1 below)
            countVariablesTemp++;
            
            //taking off the double quotes of the cells
            //cell.erase(0, 1);             // erase the first character (")
            //cell.erase(cell.size() - 1);  // erase the last character  (")
            
            //if this script is using for training
            //else, vecInput already store above
            if(bTraining){
                
                //if column isn't the class column, is a input values in sheet
                if(indexCell != 0 && (!bMinLine && !bMaxLine)){                
                    input.push_back(strtof((cell).c_str(),0));
                }
            }
            
            //classifications in sheet 
            //ignoring the max and min values
            //(first column)
            if(indexCell == 0 && (!bMinLine && !bMaxLine)){
                
                //catch the index of the classification of this loop
                for(int i=0 ; i<classifications.size() ; i++){
                    
                    //expected value is a integer representing each classification
                    int expectedValue;

                    //if this cell is equal to current classification, this is the correctly
                    if(cell.compare(classifications.at(i)) == 0){
                        
                        //setting the expected values vector, to associate to output neurons
                        //ex.: 1 to first neuron and 0 to rest, 1 to second, ...
                        //          red                             blue
                        expectedValue = 1;
                    } else {
                        expectedValue = 0;
                    }
                    
                    expectedOutput.push_back(expectedValue);
                    
                }   
                
                vecExpectedOutputs.push_back(expectedOutput);
                
            }
            
            indexCell++;
        }
        
        //taking off the column of classifications of variables
        countVariablesTemp--;
        
        if(countVariablesTemp > countVariables){
            countVariables = countVariablesTemp;
        }
        
        //if this script is using for training
        //else, vecInput already store above
        if(bTraining && (!bMinLine && !bMaxLine)){
            
            //inserting the input in the inputs vector
            vecInput.push_back(input);
        }
                
    }   
    
    //linear normalization
    for(int i=0 ; i<vecInput.size() ; i++){
        
        for(int j=0 ; j<vecInput.at(i).size() ; j++){
            
            vecInput.at(i).at(j) = (vecInput.at(i).at(j) - minValues.at(j))/(maxValues.at(j) - minValues.at(j));
            
        }
        
    }
    
    //index of inputs vector and expected output vector
    vector<int> inputAndOutputsIndexes;
    
    //vecInput and vecExpectedOutput have the same indexes. We are storing to shuffle after
    for(int i=0 ; i<vecInput.size() ; i++){  
        inputAndOutputsIndexes.push_back(i);
    }
    
    
    //srand to shuffle
    srand (time(NULL));
    
    //shuffling the indexes to update the arrays
    random_shuffle(inputAndOutputsIndexes.begin(), inputAndOutputsIndexes.end());
    
    vector<vector<float>> vecInputTemp;
    vector<vector<int>> vecExpectedOutputTemp;
    
    //shuffling input and output vectors
    for(int i=0 ; i<inputAndOutputsIndexes.size() ; i++){
        
        int index = inputAndOutputsIndexes.at(i);
        
        vecInputTemp.push_back(vecInput.at(index));
        vecExpectedOutputTemp.push_back(vecExpectedOutputs.at(index));
    }
    
    
    vecExpectedOutputs = vecExpectedOutputTemp;
    vecInput = vecInputTemp;
    
    //quantity of inputs to test, before training (20%)
    int testQtt = (int)(20 * vecInput.size()) / 100;
    
    //iterators to separate the train sample, and the test sample
    vector<vector<float>>::const_iterator firstInput = vecInput.begin();
    vector<vector<float>>::const_iterator separatorInput = vecInput.begin() + testQtt;
    vector<vector<float>>::const_iterator lastInput = vecInput.end();
    
    //iterators to separate the train sample, and the test sample
    vector<vector<int>>::const_iterator firstExOutput = vecExpectedOutputs.begin();
    vector<vector<int>>::const_iterator separatorExOutput = vecExpectedOutputs.begin() + testQtt;
    vector<vector<int>>::const_iterator lastExOutput = vecExpectedOutputs.end();
    
    //separating train sample, and the test sample
    vector<vector<float>> vecInputToTrain(separatorInput, lastInput);
    vector<vector<float>> vecInputToTest(firstInput, separatorInput);
    
    //separating train expected outputs, and the test expected outputs sample
    vector<vector<int>> vecExpectedOutputToTrain(separatorExOutput, lastExOutput);
    vector<vector<int>> vecExpectedOutputToTest(firstExOutput, separatorExOutput);
    
    
    //------------------------ </CATCHING THE INPUTS ON THE SHEET> ----------------------------
    
    
    //----------- <DECLARING NECESSARY VARIABLES TO THE MOUNT AND TRAINING FUNCTIONS OF NETWORK> -----------
    //boolean to know if bia is necessary
    bool bBia = true;
    
    //classifications in the sheet. In the decision of color, for example, the classifications are the different lines in the column "class"
    int countClassifications = classifications.size();
    
    //deciding the number of hidden neurons
    int countHiddenNeurons = 0;
    
    //the number of hidden neurons ever should be between the greater and minor value
    if(countClassifications > countVariables){
        countHiddenNeurons = rand()%(countClassifications-countVariables + 1) + countVariables;
    } else {
        countHiddenNeurons = rand()%(countVariables-countClassifications + 1) + countClassifications;
    }
    
    //if bia is setted, add a neuron to input and hidden layer
    if(bBia){
        countVariables++;
        countHiddenNeurons++;
    }
    
    int countSynapsis = countVariables * countHiddenNeurons * countClassifications;
    
    //neuron quantity that each layer have
    vector<int> vecQuantityNeurons {countVariables,countHiddenNeurons,countClassifications};
    
    //initial weights 
    vector<float> vecInitialWeights;
    
    if(bTraining){
        //assigning random weights to the synapsis
        for(int i=0 ; i<countSynapsis ; i++){
            float newWeight = rand()%(5-(-5) + 1) - 5;
            vecInitialWeights.push_back(newWeight);
        }
    }
        
    //----------- </DECLARING NECESSARY VARIABLES TO THE MOUNT AND TRAINING FUNCTIONS OF NETWORK> -----------
    
    
    //------------------------------------------------- <TRAINING AND LOG> ------------------------------------------------- 
    
    
    // |-|-| IN THE CASE OF TRAINING |-|-| 
    //vector of networks hit percentages 
    vector<float> hitPercentages;

    //vector of all network architectures
    vector<vector<Layer>> networksArchitectures;
    // |-|-| IN THE CASE OF TRAINING |-|-| 
    
    
    
    //if this script is not using for training
    //else, vecInputToTrain already store above
    //storing use intelligence
    if(!bTraining){
        
        string logFilePath = DIR_LOGS_FILE+"log_use_intelligence_"+intelligenceId+"_"+date+".txt";
        ofstream redirect_file(logFilePath);

        // save output buffer of cout
        streambuf * strm_buffer = cout.rdbuf();

        // redirect output into file
        cout.rdbuf(redirect_file.rdbuf());
        
        vecInitialWeights = getIniWeights(intelligenceId);
        
        vector<int> qttHiddenLayer = getQttNeuronLayers(intelligenceId);
        
        vecQuantityNeurons = {countVariables};
        
        //setting the neurons quantity in hidden layers
        for(int i=0 ; i<qttHiddenLayer.size() ; i++){
            vecQuantityNeurons.push_back(qttHiddenLayer.at(i));
        }
        
        vecQuantityNeurons.push_back(countClassifications);
        
        initializeMlp(network, vecQuantityNeurons, vecInitialWeights, bBia);
        
        int outputIndex = getOutput(network, vecInput);
        
        // restore old buffer
        cout.rdbuf(strm_buffer);
        
        cout << classifications.at(outputIndex);
        
        return 0;
        
    } else {
        
        string logFilePath = DIR_LOGS_FILE+"training_log_training_intelligence_"+intelligenceId+".txt";
        ofstream redirect_file(logFilePath);

        // save output buffer of cout
        streambuf * strm_buffer = cout.rdbuf();

        // redirect output into file
        cout.rdbuf(redirect_file.rdbuf());
        
        
        //loop representing the number of hidden layers
        for(int i=1 ; i<=2 ; i++){
            
            //vector of possibilities of neurons in the first hidden layer
            vector<int> countHiddenNeuronFirstHdnLayer;
            
            //maximum quantity of neurons in the first layer
            int maxQuantityOfNeurons1stHddnLayer = (int)countVariables*2;
            
            //loop representing the neuron's number of the first hidden layer
            while(maxQuantityOfNeurons1stHddnLayer > 1){
                
                //hit percentage of each architecture
                float hitPercentage;

                //to case of two hidden layers
                if(i == 2){
                    
                    int maxQuantityOfNeurons2ndHddnLayer = maxQuantityOfNeurons1stHddnLayer-2;
                    
                    //loop representing the neuron's number of the first hidden layer
                    while(maxQuantityOfNeurons2ndHddnLayer > 1){
                        
                        cout << " ----------------------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << maxQuantityOfNeurons2ndHddnLayer << " - " << countClassifications << " architecture" << endl;
        
                        vecQuantityNeurons = {countVariables,maxQuantityOfNeurons1stHddnLayer,maxQuantityOfNeurons2ndHddnLayer,countClassifications};        
                        
                        //synapsis quantity
                        countSynapsis = countVariables*maxQuantityOfNeurons1stHddnLayer*maxQuantityOfNeurons2ndHddnLayer*countClassifications;

                        //clearing the initial weights to have the certain number of random weights
                        vecInitialWeights.clear();

                        //assigning random weights to the synapsis
                        for(int j=0 ; j<countSynapsis ; j++){
                            float newWeight = ((double) rand() / (RAND_MAX));
                            vecInitialWeights.push_back(newWeight);
                        }
                        
                        
                        network = new vector<Layer>[vecQuantityNeurons.size()];
                
                        initializeMlp(network, vecQuantityNeurons, vecInitialWeights, bBia);

                        toTrainNetwork(network, vecInputToTrain, vecExpectedOutputToTrain);    
                        
                        cout << " ----------------------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << maxQuantityOfNeurons2ndHddnLayer << " - " << countClassifications << " architecture test" << endl;
                        
                        vector<int> onlyOutputsTest;
                        
                        //testing the fresh created network
                        for(int l=0 ; l<vecInputToTest.size() ; l++){
                            
                            //vector to pass to output function
                            vector<vector<float>> vecInputToTestTemp;
                            vecInputToTestTemp.push_back(vecInputToTest.at(l));                           
                                                        
                            int outputIndex = getOutput(network, vecInputToTestTemp);
                                                        
                            onlyOutputsTest.push_back(outputIndex);
                        }
                        
                        
                        int hitCount = 0;
                        int errorCount = 0;

                        //counting the hit and errors and catching the networks to compare below
                        for(int l=0 ; l<onlyOutputsTest.size() ; l++){

                            int expectedOutputToCompare;

                            for(int m=0 ; m<vecExpectedOutputToTest.at(l).size() ; m++){

                                //transforming 0001 in 3 (format that come in getOutput function)
                                if(vecExpectedOutputToTest.at(l).at(m) == 1){
                                    expectedOutputToCompare = m;
                                }
                            }

                            if(expectedOutputToCompare == onlyOutputsTest.at(l)){
                                hitCount++;
                            } else {
                                errorCount++;
                            }

                        }

                        hitPercentage = (hitCount)/((errorCount+hitCount)*100.0);

                        cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << maxQuantityOfNeurons2ndHddnLayer << " - " << countClassifications << " architecture hit number: " << hitCount << endl;
                        cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << maxQuantityOfNeurons2ndHddnLayer << " - " << countClassifications << " architecture error number: " << errorCount << endl;
                        cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << maxQuantityOfNeurons2ndHddnLayer << " - " << countClassifications << " architecture hit percentage: " << hitPercentage << endl << endl;
                        
                        //each architecture being tested and stored
                        hitPercentages.push_back(hitPercentage);
                        networksArchitectures.push_back(*network);
                        
                        
                        maxQuantityOfNeurons2ndHddnLayer = maxQuantityOfNeurons2ndHddnLayer-2;
                    }
                    
                } 
                
                //to case of one hidden layers
                else {
                    
                    cout << " ----------------------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << countClassifications << " architecture" << endl;
                    
                    vecQuantityNeurons = {countVariables,maxQuantityOfNeurons1stHddnLayer,countClassifications};        
                    
                    //synapsis quantity
                    countSynapsis = countVariables*maxQuantityOfNeurons1stHddnLayer*countClassifications;
                    
                    //clearing the initial weights to have the certain number of random weights
                    vecInitialWeights.clear();

                    //assigning random weights to the synapsis (the old vector doesn't have enough weights)
                    for(int i=0 ; i<countSynapsis ; i++){
                        float newWeight = ((double) rand() / (RAND_MAX));
                        vecInitialWeights.push_back(newWeight);
                    }
                    
                    network = new vector<Layer>[vecQuantityNeurons.size()];

                    initializeMlp(network, vecQuantityNeurons, vecInitialWeights, bBia);

                    toTrainNetwork(network, vecInputToTrain, vecExpectedOutputToTrain);    
                    
                    cout << " ----------------------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << countClassifications << " architecture test" << endl;
                        
                    vector<int> onlyOutputsTest;

                    //testing the fresh created network
                    for(int l=0 ; l<vecInputToTest.size() ; l++){

                        //vector to pass to output function
                        vector<vector<float>> vecInputToTestTemp;
                        vecInputToTestTemp.push_back(vecInputToTest.at(l));                           

                        int outputIndex = getOutput(network, vecInputToTestTemp);

                        onlyOutputsTest.push_back(outputIndex);
                    }
                    
                    int hitCount = 0;
                    int errorCount = 0;
                    
                    //counting the hit and errors and catching the networks to compare below
                    for(int l=0 ; l<onlyOutputsTest.size() ; l++){
                        
                        int expectedOutputToCompare;
                        
                        for(int m=0 ; m<vecExpectedOutputToTest.at(l).size() ; m++){
                            
                            //transforming 0001 in 3 (format that come in getOutput function)
                            if(vecExpectedOutputToTest.at(l).at(m) == 1){
                                expectedOutputToCompare = m;
                            }
                        }
                        
                        if(expectedOutputToCompare == onlyOutputsTest.at(l)){
                            hitCount++;
                        } else {
                            errorCount++;
                        }
                        
                    }
                    
                    hitPercentage = (hitCount)/((errorCount+hitCount)*100.0);
                    
                    cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << countClassifications << " architecture hit number: " << hitCount << endl;
                    cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << countClassifications << " architecture error number: " << errorCount << endl;
                    cout << " ------------------- " << countVariables << " - " << maxQuantityOfNeurons1stHddnLayer << " - " << countClassifications << " architecture hit percentage: " << hitPercentage << endl << endl;
                    
                    //each architecture being tested and stored
                    hitPercentages.push_back(hitPercentage);
                    networksArchitectures.push_back(*network);
                    
                }
                
                //decrementing 2 of neurons first hidden layer's quantity
                maxQuantityOfNeurons1stHddnLayer = maxQuantityOfNeurons1stHddnLayer-2;
            }
            
            
        }

        // restore old buffer
        cout.rdbuf(strm_buffer);
    
    }
    //------------------------------------------------- </TRAINING AND LOG> ------------------------------------------------- 

    //storing the config of the network with best hit percentage
    if(bTraining){
        
        //greater hit percentage to compare
        float greaterHit = 0;
        
        //index with best hit percentage
        int indexBestHit = -1;
        
        for(int i=0 ; i<hitPercentages.size() ; i++){
            
            if(hitPercentages.at(i) > greaterHit){
                greaterHit = hitPercentages.at(i);
                indexBestHit = i;
            }
        }
        
        vector<Layer> bestNetwork = networksArchitectures.at(indexBestHit);
        
        //create and save the config of network
        makeConfigMlp(&bestNetwork, intelligenceId);
    
        cout << MLP_TRAINED_WITH_SUCCESS;
    }
    
    return 0;
}
