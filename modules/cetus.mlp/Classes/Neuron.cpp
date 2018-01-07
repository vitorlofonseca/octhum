/*
 * Neuron.cpp
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */
#include <iostream>

#include "../Headers/Synapse.h"
#include "../Headers/Neuron.h"
#include <vector>

using namespace std;

Neuron::Neuron() {
    // TODO Auto-generated constructor stub

}

void Neuron::setIdLayer(int newIdLayer){
    idLayer = newIdLayer;
}

void Neuron::setOutput(float newOutput){
    output = newOutput;
}

void Neuron::setExpectedValue(float newExpectedValue){
    expectedValue = newExpectedValue;
}

int Neuron::getIdLayer(){
    return idLayer;
}

float Neuron::getOutput(){
    return output;
}

float Neuron::getExpectedValue(){
    return expectedValue;
}

void Neuron::setId(int newId){
    id = newId;
}

int Neuron::getId(){
    return id;
}

void Neuron::setBBia(bool newBBia){
    bBia = newBBia;
}

bool Neuron::getBBia(){
    return bBia;
}

/**
 * Getting previous layer's synapsis, where have as destination neuron, this neuron
 *
 */
vector<Synapse> Neuron::getAInputSynapsis(vector<Synapse> aSynapse){

    vector<Synapse>::iterator itrSynapsis = aSynapse.begin();

    //declaring synapsis' array to return
    vector<Synapse> aSynapseToReturn;

    //iterating in synapsis of this layer
    while(itrSynapsis != aSynapse.end()) {

        //comparing the neuron's id passed by parameter, with the idSourceNeuron iterator
        if(id == itrSynapsis->getDestinationNeuron()->getId()){
            aSynapseToReturn.push_back(*itrSynapsis);
        }

        itrSynapsis++;
    }

    return aSynapseToReturn;
}

