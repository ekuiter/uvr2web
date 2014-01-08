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
  const int buttonPins[] = {2, 3, 4, 5};
  const int ledPin = 13; // the number of the LED pin
  
  int buttonStates[4]; // variables for reading the pushbutton status
  int lastButtonStates[4]; // previous state of buttons
  
  void start();
  void check();
}

#endif
