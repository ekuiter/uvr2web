/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 dump.ino:
 Ausgabe der Daten auf der seriellen Schnittstelle
 
 */

#ifdef DEBUG

namespace Dump {

  void start() {
    meta();
    sensors();
    heat_meters();
    outputs();
    speed_steps();
  }

  void meta() {
    Process::fetch_timestamp();
    Serial.print("\n\nUVR1611 am ");
    if (Process::timestamp.day < 10)
      Serial.print("0");
    Serial.print(Process::timestamp.day);
    Serial.print(".");
    if (Process::timestamp.month < 10)
      Serial.print("0");
    Serial.print(Process::timestamp.month);
    Serial.print(".");
    Serial.print(Process::timestamp.year);
    Serial.print(" um ");
    if (Process::timestamp.hour < 10)
      Serial.print("0");
    Serial.print(Process::timestamp.hour);
    Serial.print(":");
    if (Process::timestamp.minute < 10)
      Serial.print("0");
    Serial.print(Process::timestamp.minute);
    if (Process::timestamp.summer_time)
      Serial.println(" (Sommerzeit)");
    else
      Serial.println(" (Winterzeit)");
  }

  void bytes() {
    for (int i = 0; i < Process::bytes_count; i++)
      Serial.print(Process::data_bytes[i], BIN); 
    Serial.println();
  }

  void bits() {
    for (int i = 0; i < 64; i++) { // 64 Datenbytes ausgeben
      Serial.print(Process::data_bits[i], BIN); 
      Serial.print(" "); 
    }
    Serial.println();
  }

  void sensors() {
    Serial.println("Sensoren:");
    for (int i = 1; i <= 16; i++) {
      Process::fetch_sensor(i);
      sensor();
    }
  }

  void sensor() {
    Serial.print("   Sensor ");
    Serial.print(Process::sensor.number);
    Serial.print(": ");
    if (Process::sensor.invalid)
      Serial.print("-");
    else
      Serial.print(Process::sensor.value);
    switch (Process::sensor.type) {
    case UNUSED:
      Serial.print(" (unbenutzt)");
      break;
    case DIGITAL:
      Serial.print(" (digital)");
      break;
    case TEMP:
      Serial.print(" ^C");
      break;
    case VOLUME_FLOW:
      Serial.print(" l/h Volumenstrom");
      break;
    case RAYS:
      Serial.print(" W/m^2 Strahlung");
      break;
    case ROOM:
      Serial.print(" ^C Raum ");
      switch (Process::sensor.mode) {
      case AUTO:
        Serial.print("Automatik");
        break;
      case NORMAL:
        Serial.print("Normal");
        break;
      case LOWER:
        Serial.print("Absenk");
        break;
      case STANDBY:
        Serial.print("Standby");
        break;
      }
      break;
    }
    Serial.println();
  }

  void heat_meters() {
    Process::fetch_heat_meter(1);
    heat_meter();
    Process::fetch_heat_meter(2);
    heat_meter();
  }

  void heat_meter() {
    Serial.print("Waermemengenzaehler ");
    Serial.print(Process::heat_meter.number);
    Serial.println(":");
    if (Process::heat_meter.invalid) {
      Serial.print("   Inaktiv");
      return;
    }
    Serial.print("   Momentanleistung: ");
    Serial.print(Process::heat_meter.current_power);
    Serial.println(" kW");
    Serial.print("   ");
    Serial.print(Process::heat_meter.kwh);
    Serial.println(" kWh");
    Serial.print("   ");
    Serial.print(Process::heat_meter.mwh);
    Serial.println(" MWh");
  }

  void outputs() {
    Serial.println("\nAusgaenge: ");
    for (int i = 1; i <= 13; i++) {
      Serial.print("  Ausgang ");
      Serial.print(i);
      Serial.print(": ");
      Serial.println(Process::fetch_output(i) ? "An" : "Aus"); 
    }
  }

  void speed_steps() {
    Serial.println("Drehzahlstufen: ");
    speed_step(1);
    speed_step(2);
    speed_step(6);
    speed_step(7);
  }

  void speed_step(int output) {
    Serial.print("   Ausgang ");
    Serial.print(output);
    Serial.print(": ");
    int speed_step = Process::fetch_speed_step(output);
    if (speed_step == -2)
      Serial.println("Ungueltig");
    else if (speed_step == -1)
      Serial.println("Unbekannt");
    else
      Serial.println(speed_step); 
  }

}

#endif




