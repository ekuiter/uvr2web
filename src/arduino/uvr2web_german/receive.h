/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 receive.h:
 Empfangen und Speichern von Datenrahmen der Regelung
 
 */

namespace Receive {
  
  // Dekodierung des Manchester-Codes

  // Pulsweite bei 488hz: 1000ms/488 = 2,048ms = 2048µs
  // 2048µs / 2 = 1024µs (2 Pulse für ein Bit)
  const unsigned long pulse_width = 1024; // µs
  const int percentage_variance = 10; // % Toleranz für Abweichungen bei der Pulsweite
  // 1001 oder 0110 sind zwei aufeinanderfolgende Pulse ohne Übergang
  const unsigned long double_pulse_width = pulse_width * 2;
  // Berechnung der Toleranzgrenzen für Abweichungen
  const unsigned long low_width = pulse_width - (pulse_width *  percentage_variance / 100);
  const unsigned long high_width = pulse_width + (pulse_width * percentage_variance / 100);
  const unsigned long double_low_width = double_pulse_width - (pulse_width * percentage_variance / 100);
  const unsigned long double_high_width = double_pulse_width + (pulse_width * percentage_variance / 100);
  boolean got_first = 0; // erster oder zweiter Puls für ein Bit?
  unsigned long last_bit_change = 0; // Merken des letzten Übergangs
  int bit_count; // Anzahl der empfangenen Bits
  byte receiving; // Übertragungs-Flag
  
  void start(); // Übertragung beginnen
  void stop(); // Übertragung stoppen
  void pin_changed(); // wird aufgerufen, sobald sich der Zustand am Daten-Pin ändert
  void process_bit(unsigned char b); // speichert ein von pin_changed ermitteltes Bit

}




