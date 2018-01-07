/*
 * Synapse.h
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */

#ifndef SYNAPSE_H_
#define SYNAPSE_H_

#include "Neuron.h"

//avoiding circular dependencies
class Neuron;

//class that represents synapsis in the various layers of the neural network
class Synapse {

    private:
        int id;
        Neuron *sourceNeuron;       //source-neuron of the signal
        Neuron *destinationNeuron;  //destination-neuron of the signal
        float weight;               //weight in the calc of destination neuron's output

    public:
        Synapse();
        void setId(int newId);
        void setSourceNeuron(Neuron newSourceNeuron);
        void setDestinationNeuron(Neuron newDestinationNeuron);
        void setWeight(float newWeight);

        Neuron* getSourceNeuron();
        Neuron* getDestinationNeuron();
        float getWeight();
        int getId();

};

#endif /* SYNAPSE_H_ */
