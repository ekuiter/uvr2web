/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 process.h:
 Verarbeitung der gespeicherten Datenrahmen
 
 */

namespace Process {

  // ein Datenrahmen hat 64 Datenbytes + SYNC, also 64 * (8+1+1) + 16 = 656
  // 656 * 2 = 1312 (das Doppelte eines Datenrahmens wird gespeichert,
  // so dass ein ganzer Datenrahmen da ist)
  const int bytes_count = (64 * (8 + 1 + 1) + 16) * 2;
  byte data_bytes[bytes_count]; // jedes Bit bekommt zunächst ein eigenes Byte
  byte data_bits[bytes_count / 8]; // später wird jedes Bit in die Bitmap einsortiert
  int start_bit; // erstes Bit des Datenrahmens

  // Sensortypen
#define UNUSED      0b000
#define DIGITAL     0b001
#define TEMP        0b010
#define VOLUME_FLOW 0b011
#define RAYS        0b110
#define ROOM   0b111

  // Modi für Raumsensor
#define AUTO        0b00
#define NORMAL      0b01
#define LOWER       0b10
#define STANDBY     0b11

  // Zeitstempel der Regelung
  typedef struct {
    byte minute;
    byte hour;
    byte day;
    byte month;
    int year;
    boolean summer_time;
  } 
  timestamp_t;
  timestamp_t timestamp;

  // Sensor
  typedef struct {
    byte number;
    byte type;
    byte mode;
    boolean invalid;
    float value;
  }
  sensor_t;
  sensor_t sensor;

  // Wärmemengenzähler
  typedef struct {
    byte number;
    boolean invalid;
    float current_power;
    float kwh;
    int mwh;
  }
  heat_meter_t;
  heat_meter_t heat_meter;

  // Datenrahmen
  void start(); // Datenrahmen auswerten
  boolean prepare(); // Datenrahmen vorbereiten
  int analyze(); // Datenrahmen analysieren
  void invert(); // Datenrahmen invertieren
  void write_bit(int pos, byte set); // Bitmap beschreiben
  void compress(); // Datenrahmen in Bitmap schreiben
  boolean check_device(); // Datenrahmen überprüfen

  // Informationen auslesen
  void fetch_timestamp(); // Zeitstempel
  void fetch_sensor(int sensor); // Sensor
  void fetch_heat_meter(int heat_meter); // Wärmemengenzähler
  boolean fetch_output(int output); // Ausgang
  int fetch_speed_step(int output); // Drehzahlstufe

}








