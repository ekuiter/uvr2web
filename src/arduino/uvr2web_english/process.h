/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 process.h:
 Processing of saved data frames
 
 */

namespace Process {

  // one data frame has 64 data bytes + SYNC, so 64 * (8+1+1) + 16 = 656
  // 656 * 2 = 1312 (twice as much as a data frame is saved
  // so there's one complete data frame
  const int bytes_count = (64 * (8 + 1 + 1) + 16) * 2;
  byte data_bytes[bytes_count]; // every bit gets an own byte first
  byte data_bits[bytes_count / 8]; // later on every bit gets sorted into the bitmap
  int start_bit; // first bit of data frame

  // sensor types
#define UNUSED      0b000
#define DIGITAL     0b001
#define TEMP        0b010
#define VOLUME_FLOW 0b011
#define RAYS        0b110
#define ROOM   0b111

  // room sensor modes
#define AUTO        0b00
#define NORMAL      0b01
#define LOWER       0b10
#define STANDBY     0b11

  // heating control timestamp
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

  // sensor
  typedef struct {
    byte number;
    byte type;
    byte mode;
    boolean invalid;
    float value;
  }
  sensor_t;
  sensor_t sensor;

  // heat meter
  typedef struct {
    byte number;
    boolean invalid;
    float current_power;
    float kwh;
    int mwh;
  }
  heat_meter_t;
  heat_meter_t heat_meter;

  // data frame
  void start();
  boolean prepare();
  int analyze();
  void invert();
  void write_bit(int pos, byte set); // write to bitmap
  void compress(); // write data frame to bitmap
  boolean check_device(); // verify data frame

  // readout information
  void fetch_timestamp();
  void fetch_sensor(int sensor);
  void fetch_heat_meter(int heat_meter);
  boolean fetch_output(int output);
  int fetch_speed_step(int output);

}








