/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 receive.ino:
 Empfangen und Speichern von Datenrahmen der Regelung
 
 */

namespace Receive {

  void start() {
    bit_count = got_first = last_bit_change = 0;
    receiving = true;
    // bei einem CHANGE am Daten-Pin wird pin_changed aufgerufen
    attachInterrupt(interrupt, pin_changed, CHANGE);
  }

  void stop() {
    detachInterrupt(interrupt); 
    receiving = false;
  }

  void pin_changed() {
    byte val = digitalRead(dataPin); // Zustand einlesen
    unsigned long time_diff = micros() - last_bit_change;
    last_bit_change = micros();
    // einfache Pulsweite?
    if (time_diff >= low_width && time_diff <= high_width) {
      process_bit(val);
      return;   
    }
    // doppelte Pulsweite?
    if (time_diff >= double_low_width && time_diff <= double_high_width) {
      process_bit(!val);
      process_bit(val);
      return;   
    } 
  }

  void process_bit(byte b) {
    // den ersten Impuls ignorieren
    if (got_first == false) {
      got_first = true;
      return;  
    }
    got_first = false;

    Process::data_bytes[bit_count] = b; // Bit speichern
    bit_count++;

    // Fortschrittsleiste ausgeben
    if (bit_count == 1)
      Serial.print("\nEmpfange "); 
    if (bit_count % 32 == 0)
      Serial.print("."); 

    if (bit_count == 1312)
      stop(); // beende Übertragung, wenn Datenrahmen vollständig
  }

}


