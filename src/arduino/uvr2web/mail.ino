/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 mail.ino:
 Mailen, sobald das Relais (buttonPins) ausgelöst wird
 
 */

#ifndef DEBUG

namespace Mail {
  void sendMail(String mailer) {
    Web::client.connect(server, 80);
    Web::client.println("GET " + mailer + " HTTP/1.1");
    Web::client.println("Host: " + String(server));
    Web::client.println("Connection: close");
    Web::client.println();
    Web::client.stop();
  }

  void start() {
    pinMode(buttonPin3, INPUT_PULLUP);
    pinMode(buttonPin4, INPUT_PULLUP);
    pinMode(buttonPin5, INPUT_PULLUP);
    pinMode(ledPin, OUTPUT); 

    sendMail("/mailsender3.php");
  }

  void check() {
    buttonState3 = digitalRead(buttonPin3);
    buttonState4 = digitalRead(buttonPin4);
    buttonState5 = digitalRead(buttonPin5);


    if (buttonState3 != lastButtonState3){
      if (buttonState3 == LOW) { 
        digitalWrite(ledPin, HIGH);
        Serial.println("Schalter 3 ausgeloest Emailversand aktiviert");
        sendMail("/mailsender.php");
      }
    } else {
      digitalWrite(ledPin, LOW);
    }   

    if (buttonState4 != lastButtonState4){
      if (buttonState4 == LOW) {
        digitalWrite(ledPin, HIGH);
        Serial.println("Schalter 4 ausgeloest Emailversand aktiviert");
        sendMail("/mailsender1.php");
      }
    } else {
      digitalWrite(ledPin, LOW);
    }   

    if (buttonState5 != lastButtonState5){
      if (buttonState5 == LOW) {
        digitalWrite(ledPin, HIGH);
        Serial.println("Schalter 5 ausgeloest Emailversand aktiviert");
        sendMail("/mailsender2.php");
      }
    } else {
      digitalWrite(ledPin, LOW);
    }   

    lastButtonState3 = buttonState3;
    lastButtonState4 = buttonState4;
    lastButtonState5 = buttonState5;
  }
}

#endif

