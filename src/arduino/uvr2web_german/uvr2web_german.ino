/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 uvr2web.ino:
 Phasenwechsel zwischen Empfangen und Verarbeiten
 
 */

// wenn DEBUG definiert ist, werden die Dump-Befehle (dump.h/dump.ino) mitkompiliert 
// und es findet eine Ausgabe per Serial statt. Ist es auskommentiert, wird stattdessen 
// der Webupload (web.h/web.ino) kompiliert und verwendet. Beides gleichzeitig
// ist (auf dem Leonardo) nicht möglich, weil der RAM nicht genug Speicher bietet.
//#define DEBUG

#include <SPI.h>
#include <Ethernet.h>

#include "receive.h"
#include "process.h"
#include "dump.h"
#include "web.h"

// Diese Werte eventuell anpassen:

const byte dataPin = 2; // Pin mit Datenstrom
// Interrupt für Daten-Pin, siehe: http://arduino.cc/en/Reference/AttachInterrupt
const byte interrupt = 1;
byte mac[] = {
  0x90, 0xa2, 0xda, 0x0d, 0xb4, 0x95  }; // MAC-Adresse des Ethernet-Boards
char server[] = "example.com"; // Adresse des Servers
char script[] = "http://uvr2web.example.com/upload.php"; // Upload-Skript
// Eventuell das von der PHP-App generierte Passwort, Hauptsache sie sind identisch.
char pass[] = "123456789examplepassword123456789aslongaspossible"; 
// zusätzliche Wartezeit zwischen zwei Uploads. Wenn 0, beträgt die maximale Uploadrate
// etwa 3 Sekunden. Im Beispiel 7000 ms, also alle 3s + 7s = 10 Sekunden ein Upload.
// Den gleichen Wert wie in der PHP-App benutzen.
int upload_interval = 7000;

void setup() {
  Serial.begin(115200);
  // im Debug-Modus Ausgabe über Serial, ansonsten Hochladen ins Web (s.o.)
#ifdef DEBUG
  while (!Serial); // nur beim Leonardo
  Serial.println("Fuer detaillierte Ausgabe Taste druecken.");
#else
  Web::start();  
#endif

  Receive::start();
}

void loop() {
  if (!Receive::receiving) {
    Process::start(); // Daten auswerten
    Receive::start(); // Daten sammeln
  }

#ifdef DEBUG
  if (Serial.available()) {
    while (Serial.available())
      Serial.read(); 
    if (Dump::active)
      Serial.println("\nDetails deaktiviert.");
    else
      Serial.println("\nDetails aktiviert.");
    Dump::active = !Dump::active;
  }
#endif
}




































