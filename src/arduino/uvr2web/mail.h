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
  const int buttonPin3 = 3;
  const int buttonPin4 = 4;
  const int buttonPin5 = 5;
  int buttonState3 = 0;
  int buttonState4 = 0;
  int buttonState5 = 0;
  int lastButtonState3 = 0;
  int lastButtonState4 = 0;
  int lastButtonState5 = 0;
  const int ledPin = 13;
  void start();
  void check();
}

#endif
