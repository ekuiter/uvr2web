/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 uvr2web.ino:
 Phasenwechsel zwischen Empfangen und Verarbeiten
 Phase change between receiving and processing
 
 */

// wenn DEBUG definiert ist, werden die Dump-Befehle (dump.h/dump.ino) mitkompiliert 
// und es findet eine Ausgabe per Serial statt. Ist es auskommentiert, wird stattdessen 
// der Webupload (web.h/web.ino) kompiliert und verwendet. Beides gleichzeitig
// ist (auf dem Leonardo) nicht möglich, weil der RAM nicht genug Speicher bietet.
// If DEBUG is defined all dump commands (dump.h/dump.ino) are compiled and 
// output occurs via Serial. If DEBUG is commented out, the web upload 
// (web.h/web.ino) is compiled and used instead. Both at once doesn't work
// (on the Leonardo) because the RAM storage is too small.

//#define DEBUG

// wenn USINGPC definiert ist, werden Ausgaben auf der Konsole getätigt.
// Sollte dann eingeschaltet werden, wenn man das Arduino vom PC betreibt.
// Sobald es "im Einsatz" (am Netzteil) ist, sollte es auskommentiert werden.
// if USINGPC is defined, output is displayed on the Serial Monitor.
// Should be turned on if the Arduino is used on the PC,
// as soon as it is used with own power supply, you should comment it out.

//#define USINGPC

// Diese Werte anpassen, dazu die Kommentarzeichen /*  */ entfernen, die Werte die im Kommentar stehen, sind Beispiele
// adjust these values to your needs, for that remove the comments /*  */ (the values in the comments are examples)

const byte dataPin = /*2*/; // Pin mit Datenstrom // pin with data stream
// Interrupt für Daten-Pin, siehe: // interrupt for data pin, see:
// http://arduino.cc/en/Reference/AttachInterrupt
const byte interrupt = /*1*/;
// MAC-Adresse des Ethernet-Boards // MAC address of Ethernet board
byte mac[] = {
  /*0x00, 0x00, 0x00, 0x00, 0x00, 0x00*/  };
char server[] = /*"example.com"*/; // Adresse des Servers // server address
char script[] = /*"http://uvr2web.example.com/upload.php"*/;// Upload-Skript // upload script
// Eventuell das von der PHP-App generierte Passwort, Hauptsache sie sind identisch.
// Maybe use the one generated by the PHP app, as long as they are the same.
char pass[] = /*"123456789examplepassword123456789aslongaspossible"*/;
// Wartezeit zwischen zwei Uploads.
// Im Beispiel 7000 ms, also alle 10 Sekunden ein Upload.
// Den gleichen Wert wie in der PHP-App benutzen.
// Delay time between two uploads.
// In this example 10000 ms, so 10 seconds per upload.
// Use the same value as in the PHP app.
int upload_interval = /*10000*/;
// Nur anpassen, wenn der DL-Bus von anderer Hardware mitbenutzt wird.
// Wenn nur die UVR1611 den DL-Bus benutzt, den Wert nicht verändern.
// Wenn noch andere Hardware den Bus benutzt, Werte wie 100, 200, 500, 1000
// benutzen (viel mehr sollte es nicht werden) und sich die Konsole anschauen.
// Use only if you have additional hardware writing data to the DL bus.
// If only the UVR1611 uses the DL bus, leave it as 0.
// If other hardware uses it, try values like 100, 200, 500, 1000 (more is unlikely) 
// and observe the results.
const int additionalBits = /*0*/;

// ======================

#include <SPI.h>
#include <Ethernet.h>

#include "receive.h"
#include "process.h"
#include "dump.h"
#include "web.h"

void setup() {
#ifdef USINGPC
  Serial.begin(115200);
#endif
  // im Debug-Modus Ausgabe über Serial, ansonsten Hochladen ins Web (s.o.)
  // in DEBUG mode output via serial, otherwise web upload (see above)
#ifdef DEBUG
  Serial.println("Press key for detailed output.");
#else
  Web::start();  
#endif

  Receive::start();
}

void loop() {
  if (!Receive::receiving) {
    Process::start(); // Daten auswerten // process data
    Receive::start(); // Daten sammeln // receive data
  }

#ifdef DEBUG
  if (Serial.available()) {
    while (Serial.available())
      Serial.read(); 
    if (Dump::active)
      Serial.println("\nDetails deactivated.");
    else
      Serial.println("\nDetails activated.");
    Dump::active = !Dump::active;
  }
#endif
}