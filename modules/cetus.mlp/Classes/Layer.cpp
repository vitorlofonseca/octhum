/*
 * Layer.cpp
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */

#include "../Headers/Layer.h"
#include <vector>
#include <iostream>
#include <algorithm>

using namespace std;

Layer::Layer() {
    // TODO Auto-generated constructor stub

}

void Layer::setId(int newId){
    id = newId;
}

int Layer::getId(){
    return id;
}

void Layer::setQtdNeurons(int newQtdNeurons){
    qtdNeurons = newQtdNeurons;
}

int Layer::getQtdNeurons(){
    return qtdNeurons;
}

/**
 * return the neurons count, without count with bia
 * 
 * @return 
 */
int Layer::getQtdNeuronsNoBia(){
    
    //if this layer is output, return, cause this layer doesn't have bia
    if(this->getBOutput()){
        return qtdNeurons;
    }
    
    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();
    
    //neuron count, to return
    int countNeuron = 0;
    
    //neurons ids that already was saw
    vector<int> neuronToSkip;

    //iterating in synapsis of this layer to get the source neuron
    while(itrSynapsis != aSynapse.end()) {
        
        bool bAlreadySaw = std::find(neuronToSkip.begin(), neuronToSkip.end(), itrSynapsis->getSourceNeuron()->getId()) != neuronToSkip.end();
        
        //if the current neuron is not a bia, and the neuron yet was not saw, make
        if(!itrSynapsis->getSourceNeuron()->getBBia() && !bAlreadySaw){
            countNeuron++;
            
            //assigning the id in the array, to the next time skip this neuron
            neuronToSkip.push_back(itrSynapsis->getSourceNeuron()->getId());
        }

        itrSynapsis++;
    }

    return countNeuron;
}

bool Layer::getBHidden(){
    return bHidden;
}

void Layer::setBHidden(bool newBHidden){
    bHidden = newBHidden;
}

bool Layer::getBInput(){
    return bInput;
}

void Layer::setBInput(bool newBInput){
    bInput = newBInput;
}

bool Layer::getBOutput(){
    return bOutput;
}

void Layer::setBOutput(bool newBOutput){
    bOutput = newBOutput;
}

void Layer::setASynapse(vector<Synapse> newASynapse){
    aSynapse = newASynapse;
}

vector<Synapse> Layer::getASynapse(){
    return aSynapse;
}

void Layer::addSynapse(Synapse newSynapse){
    aSynapse.push_back(newSynapse);
}

/**
 * returns a vector of synapsis passing the destination neuron of them by parameter
 * 
 * @param neuron
 * @return 
 */
vector<Synapse> Layer::getASynapseByDestinationNeuron(Neuron neuron){

    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //declaring synapsis' array to return
    vector<Synapse> aSynapseToReturn;

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {
        
        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(neuron.getId() == itrSynapsis->getDestinationNeuron()->getId()){
                aSynapseToReturn.push_back(*itrSynapsis);
        }

        itrSynapsis++;
    }

    return aSynapseToReturn;

}

/**
 * returns a vector of synapsis passing the source neuron of them by parameter
 * 
 * @param neuron
 * @return 
 */
vector<Synapse> Layer::getASynapseBySourceNeuron(Neuron neuron){

    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //declaring synapsis' array to return
    vector<Synapse> aSynapseToReturn;

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(neuron.getId() == itrSynapsis->getSourceNeuron()->getId()){
                aSynapseToReturn.push_back(*itrSynapsis);
        }

        itrSynapsis++;
    }

    return aSynapseToReturn;

}

/**
 * update the neurons outputs of this layer
 * 
 * @param neuron
 * @param newOutput
 */
void Layer::updateOutputNeuron(Neuron neuron, float newOutput){

    // ------------------- UPDATING THE OUTPUT OF THE SOURCE NEURONS ------------------- 
    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(neuron.getId() == itrSynapsis->getSourceNeuron()->getId()){
            itrSynapsis->getSourceNeuron()->setOutput(newOutput);
        }

        itrSynapsis++;
    }
    
    
    // ------------------- UPDATING THE OUTPUT OF THE DESTINATION NEURONS ------------------- 
    itrSynapsis = aSynapse.begin();

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(neuron.getId() == itrSynapsis->getDestinationNeuron()->getId()){
            itrSynapsis->getDestinationNeuron()->setOutput(newOutput);
        }

        itrSynapsis++;
    }

    return;

}


/**
 * Update the synapsis weight passed by parameter
 * 
 * @param synapse
 * @param newWeight
 */
void Layer::updateWeightBySynapse(Synapse synapse, float newWeight){

    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(synapse.getId() == itrSynapsis->getId()){
            itrSynapsis->setWeight(newWeight);
        }

        itrSynapsis++;
    }

    return;

}
 
/**
 * function to return the neurons destination vector of this layer / source neurons of the next layer
 * 
 * @return 
 */
vector<Neuron> Layer::getADestinationNeuron(){

    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();
    
    vector<Neuron> aNeuron;

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {
        
        //seeing if neuron exist in array, so as not to duplicate
        vector<Neuron>::iterator itrNeuron = aNeuron.begin();
        
        //boolean to know if insert or no
        bool bInsert = true;
        
        while(itrNeuron != aNeuron.end()) {
            
            if(itrSynapsis->getDestinationNeuron()->getId() == itrNeuron->getId()){
                bInsert = false;
            }
            
            itrNeuron++;
        }
        
        if(bInsert){
            Neuron *neuronTemp = itrSynapsis->getDestinationNeuron();
            aNeuron.push_back(*neuronTemp);
        }

        itrSynapsis++;
    }

    return aNeuron;

}

/**
 * Update the expect output of the neurons of the output layer 
 * 
 * @param neuron
 * @param newExpectedValue
 */
bool Layer::updateExpectedOutput(Neuron neuron, float newExpectedValue){
    
    // ------------------- UPDATING THE EXPECTED VALUE OF THE DESTINATION NEURON ------------------- 
    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(neuron.getId() == itrSynapsis->getDestinationNeuron()->getId()){
            itrSynapsis->getDestinationNeuron()->setExpectedValue(newExpectedValue);
        }

        itrSynapsis++;
    }

    return true;

}
