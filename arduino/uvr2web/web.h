/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 web.h:
 Hochladen der Datenrahmen ins Internet via Ethernet
 Upload data frames to the internet via Ethernet
 
 */

#ifndef DEBUG

namespace Web {

#define CONCAT request += "&"
  EthernetClient client;
  String request;
  unsigned long upload_finished = 0;

  void start(); // stellt eine Internetverbindung her // connects to the internet
  void upload(); // übertragt einen Datenrahmen zum Server // transfers a data frame to the server
  boolean working(); // wartet auf Abschluss der Übertragung // waits for the termination of the upload process

  void sensors(); // Sensoren
  void heat_meters(); // Wärmemengenzähler
  void outputs(); // Ausgänge
  void speed_steps(); // Drehzahlstufen

  // Ausgabe einzelner Elemente
  // output of particular elements
  void heat_meter();
  void sensor();
  void speed_step(int output);
}

#endif
