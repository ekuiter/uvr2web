/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 dump.h:
 Data output via serial interface
 
 */

#ifdef DEBUG

namespace Dump {
  
  boolean active; // dump active?

  void start(); // output all data
  void meta(); // meta data (heating control + timestamp)
  void bytes();
  void bits();

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



