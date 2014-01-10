/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 mail.h:
 Mailen, sobald das Relais (buttonPins) ausgelöst wird
 
 */
 
#ifndef DEBUG

namespace Mail {
  const int buttonPins[] = {/*3, 4, 5, 6*/};
  const int ledPin = /*13*/; // the number of the LED pin
  
  int buttonStates[4]; // variables for reading the pushbutton status
  int lastButtonStates[4]; // previous state of buttons
  
  //  save the last check's result
  bool lastCheck; // (once lastCheck was set true, reset the Arduino to re-enable checking)
  
  void start();
  void check();
}

#endif
