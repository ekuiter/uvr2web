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
  }

  void start() {
    pinMode(ledPin, OUTPUT);
    for (int i = 0; i < 4; i++)
      pinMode(buttonPins[i], INPUT_PULLUP);

    sendMail("/mailsender3.php");
    Serial.println("Test Mail zum Neustart wurde versendet");
  }
  
  void check() {
    for (int i = 0; i < 4; i++)
      buttonStates[i] = digitalRead(buttonPins[i]);
      
    // buttonStates[3] is the fourth button!
    if (buttonStates[3] != lastButtonStates[3]) {
      if (buttonStates[3] == LOW) {
        Serial.println("Schalter 4 ausgeloest Emailversand aktiviert");
        digitalWrite(ledPin, HIGH);
        sendMail("/mailsender.php");
      }
    } else
    digitalWrite(ledPin, LOW);
    
    // buttonStates[2] is the third button!
    if (buttonStates[2] != lastButtonStates[2]) {
      if (buttonStates[2] == LOW) {
        Serial.println("Schalter 3 ausgeloest Emailversand aktiviert");
        digitalWrite(ledPin, HIGH);
        sendMail("/mailsender1.php");
      }
    } else
    digitalWrite(ledPin, LOW);
      
    if (Web::client.available()) {
      char c = Web::client.read();
      Serial.print(c);
    }
    
    if (!Web::client.connected())
      Web::client.stop();
      
    for (int i = 0; i < 4; i++)
      lastButtonStates[i] = buttonStates[i];
  }
}

#endif
