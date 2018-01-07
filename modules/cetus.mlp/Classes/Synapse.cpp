/*
 * Synapse.cpp
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */

#include "../Headers/Synapse.h"
#include "../Headers/Neuron.h"
#include <vector>
#include <stdlib.h>     /* malloc, free, rand */

Synapse::Synapse() {
    // TODO Auto-generated constructor stub

}

void Synapse::setSourceNeuron(Neuron newSourceNeuron){
    sourceNeuron = (Neuron*)malloc(sizeof(Neuron));
    *sourceNeuron = newSourceNeuron;
}

void Synapse::setDestinationNeuron(Neuron newDestinationNeuron){
    destinationNeuron = (Neuron*)malloc(sizeof(Neuron));
    *destinationNeuron = newDestinationNeuron;
}

void Synapse::setWeight(float newWeight){
    weight = newWeight;
}

void Synapse::setId(int newId){
    id = newId;
}

Neuron* Synapse::getSourceNeuron(){
    return sourceNeuron;
}

Neuron* Synapse::getDestinationNeuron(){
    return destinationNeuron;
}

float Synapse::getWeight(){
    return weight;
}

int Synapse::getId(){
    return id;
}
