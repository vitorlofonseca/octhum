/*
 * Layer.h
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */

#ifndef LAYER_H_
#define LAYER_H_

#include "Neuron.h"
#include "Synapse.h"
#include <vector>

using namespace std;

//class that represents the layers in the neural network
class Layer {

    private:
        int id;
        bool bOutput; 				//flag to know if this layer is an output layer
        bool bHidden; 				//flag to know if this layer is a hidden layer
        bool bInput; 				//flag to know if this layer is an input layer
        int qtdNeurons; 			//specify how much neurons this layer have
        vector<Synapse> aSynapse;		//synapsis' array

    public:
        void setId(int newId);
        int getId();
        void setQtdNeurons(int newQtdNeurons);
        int getQtdNeurons();
        int getQtdNeuronsNoBia();
        bool getBOutput();
        void setBOutput(bool newBOutput);
        bool getBInput();
        void setBInput(bool newBInput);
        bool getBHidden();
        void setBHidden(bool newBHidden);
        Layer();
        void setASynapse(vector<Synapse> newASynapse);
        vector<Synapse> getASynapse();
        void addSynapse(Synapse newSynapse);

        vector<Synapse> getASynapseBySourceNeuron(Neuron neuron);
        vector<Synapse> getASynapseByDestinationNeuron(Neuron neuron);
        void updateOutputNeuron(Neuron neuronSource, float newOutput);
        bool updateExpectedOutput(Neuron neuronSource, float newExpectedValue);
        void updateWeightBySynapse(Synapse synapse, float newWeight);
        vector<Neuron> getADestinationNeuron();

};

#endif /* LAYER_H_ */
