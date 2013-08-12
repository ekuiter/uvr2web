/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 web.h:
 Hochladen der Datenrahmen ins Internet via Ethernet
 
 */

#ifndef DEBUG

namespace Web {

#define CONCAT request += "&"
  EthernetClient client;
  String request;

  void start(); // stellt eine Internetverbindung her
  void upload(); // übertragt einen Datenrahmen zum Server
  boolean working(); // wartet auf Abschluss der Übertragung

    void sensors(); // Sensoren
  void heat_meters(); // Wärmemengenzähler
  void outputs(); // Ausgänge
  void speed_steps(); // Drehzahlstufen

  // Ausgabe einzelner Elemente
  void heat_meter();
  void sensor();
  void speed_step(int output);
}

#endif


