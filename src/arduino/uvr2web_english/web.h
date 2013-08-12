/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 web.h:
 Upload data frames to the internet via Ethernet
 
 */

#ifndef DEBUG

namespace Web {

#define CONCAT request += "&"
  EthernetClient client;
  String request;

  void start(); // connects to the internet
  void upload(); // transfers a data frame to the server
  boolean working(); // waits for the termination of the upload process

    void sensors();
  void heat_meters();
  void outputs(); 
  void speed_steps();

  // output of particular elements
  void heat_meter();
  void sensor();
  void speed_step(int output);
}

#endif


