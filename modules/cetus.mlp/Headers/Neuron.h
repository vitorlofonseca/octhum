/*
 * Neuron.h
 *
 *  Created on: Aug 19, 2017
 *      Author: vitorlofonseca
 */

#ifndef NEURON_H_
#define NEURON_H_

#include <vector>
#include "Synapse.h"

using namespace std;

//avoiding circular dependencies
class Synapse;

//class that represents each neuron on the network
class Neuron {

    private:
        int id;
        int idLayer;        //id of this neuron's layer
        float output;       //output of neuron
        bool bBia;          //flag to know if this neuron is a bia
        float expectedValue;  //target value to know if the training result is acceptable or no 

    public:
        void setId(int newId);
        void setIdLayer(int newIdLayer);
        void setOutput(float newOutput);
        void setExpectedValue(float newExpectedValue);
        void setBBia(bool newBBia);
        Neuron();

        int getIdLayer();
        float getOutput();
        float getExpectedValue();
        int getId();
        bool getBBia();
        
        vector<Synapse> getAInputSynapsis(vector<Synapse> aSynapse);

};

#endif /* NEURON_H_ */
