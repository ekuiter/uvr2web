/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 dump.h:
 Ausgabe der Daten auf der seriellen Schnittstelle
 
 */

#ifdef DEBUG

namespace Dump {
  
  boolean active; // Dump aktivieren?

  void start(); // Ausgabe aller Daten
  void meta(); // Metadaten (Regelung + Zeitstempel)
  void bytes();
  void bits();

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



