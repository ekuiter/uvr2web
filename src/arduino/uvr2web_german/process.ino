/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 process.ino:
 Verarbeitung der gespeicherten Datenrahmen
 
 */

namespace Process {

  void start() {
    // bereite Datenrahmen vor
    if (prepare()) {
      // entweder Dump auf Serial oder Upload
#ifdef DEBUG
      if (Dump::active)
        Dump::start();
#else
      Web::upload();
#endif
    } 
    else
      Serial.println("Datenrahmen beschaedigt.");
  }

  boolean prepare() {
    start_bit = analyze(); // Anfang des Datenrahmens finden
    // invertiertes Signal?
    if (start_bit == -1) {
      invert(); // erneut invertieren
      start_bit = analyze();
    }
    compress(); // Bits in eine Bitmap verschieben
    return check_device(); // nur für die UVR1611
  } 

  int analyze() {
    byte sync;
    // finde SYNC (16 * aufeinanderfolgend 1)
    for (int i = 0; i < bytes_count; i++) {
      if (data_bytes[i])
        sync++;
      else
        sync = 0;
      if (sync == 16) {
        // finde erste 0
        while (data_bytes[i] == 1)
          i++;
        return i; // Anfang des Datenrahmens
      }
    }
    return -1; // kein Datenrahmen vorhanden. Signal überprüfen?
  }

  void invert() {
    for (int i = 0; i < bytes_count; i++)
      data_bytes[i] = !data_bytes[i]; // jedes Bit umkehren
  }

  void write_bit(int pos, byte set) {
    int row = pos / 8; // Position in Bitmap ermitteln
    int col = pos % 8;
    if (set)
      data_bits[row] |= 1 << col; // Bit setzen
    else
      data_bits[row] &= ~(1 << col); // Bit löschen
  }

  void compress() {    
    for (int i = start_bit, bit = 0; i < bytes_count; i++) {
      int offset = i - start_bit;
      // Start- und Stop-Bits ignorieren:
      // Startbits: 0 10 20 30, also  x    % 10 == 0
      // Stopbits:  9 19 29 39, also (x+1) % 10 == 0
      if (offset % 10 && (offset + 1) % 10) {
        write_bit(bit, data_bytes[i]);
        bit++;
      }
    }
  }

  boolean check_device() {
    // Datenrahmen von einer UVR1611?
    if (data_bits[0] == 0x80 && data_bits[1] == 0x7f)
      return true;
    else
      return false;
  }

  void fetch_timestamp() {
    timestamp.minute = data_bits[3];
    timestamp.hour = data_bits[4] & 0x1f;
    timestamp.day = data_bits[5];
    timestamp.month = data_bits[6];
    timestamp.year = data_bits[7] + 2000;
    timestamp.summer_time = (data_bits[4] & 0x20) >> 5;
  }

  void fetch_sensor(int number) {
    sensor.number = number;
    sensor.invalid = false;
    sensor.mode = -1;
    float value;
    number = 6 + number * 2; // Sensor 1 liegt auf Byte 8 und 9
    byte sensor_low = data_bits[number];
    byte sensor_high = data_bits[number + 1];
    number = sensor_high << 8 | sensor_low;
    sensor.type = (number & 0x7000) >> 12;
    if (!(number & 0x8000)) { // Vorzeichen positiv
      number &= 0xfff;
      // Berechnungen für unterschiedliche Sensortypen
      switch (sensor.type) {
      case DIGITAL:
        value = false;
        break;
      case TEMP:
        value = number * 0.1;
        break;
      case RAYS:
        value = number;
        break;
      case VOLUME_FLOW:
        value = number * 4;
        break;
      case ROOM:
        sensor.mode = (number & 0x600) >> 9;
        value = (number & 0x1ff) * 0.1;
        break;
      default:
        sensor.invalid = true;
      }
    } 
    else { // Vorzeichen negativ
      number |= 0xf000;
      // Berechnungen für unterschiedliche Sensortypen
      switch (sensor.type) {
      case DIGITAL:
        value = true;
        break;
      case TEMP:
        value = (number - 65536) * 0.1;
        break;
      case RAYS:
        value = number - 65536;
        break;
      case VOLUME_FLOW:
        value = (number - 65536) * 4;
        break;
      case ROOM:
        sensor.mode = (number & 0x600) >> 9;
        value = ((number & 0x1ff) - 65536) * 0.1;
        break;
      default:
        sensor.invalid = true;
      }
    }
    sensor.value = value;
  }

  void fetch_heat_meter(int number) {
    heat_meter.number = number;
    heat_meter.invalid = false;
    // Momentanleistung
    int power_index, kwh_index, mwh_index = 0;
    if (number == 1) {
      if (!!(data_bits[46] & 0x1)) {
        power_index = 47;
        kwh_index = 51;
        mwh_index = 53;
      }
    } 
    else if (number == 2) {
      if (!!(data_bits[46] & 0x2)) {
        power_index = 55;
        kwh_index = 59;
        mwh_index = 61;
      }
    }
    if (!power_index) {
      heat_meter.invalid = true;
      return;
    }
    byte b1 = data_bits[power_index];
    byte b2 = data_bits[power_index + 1];
    byte b3 = data_bits[power_index + 2];
    byte b4 = data_bits[power_index + 3];
    int high = 65536 * b4 + 256 * b3 + b2;
    int low = (b1 * 10) / 256;
    float current_power;
    if (!(b4 & 0x80)) // Vorzeichen positiv
      current_power = (10 * high + low) / 100;
    else // Vorzeichen negativ
    current_power = (10 * (high - 65536) - low) / 100;
    heat_meter.current_power = current_power;
    // kWh
    low = data_bits[kwh_index];
    high = data_bits[kwh_index + 1];
    heat_meter.kwh = (high * 256 + low) * 0.1;
    // MWh
    low = data_bits[mwh_index];
    high = data_bits[mwh_index + 1];
    heat_meter.mwh = high * 256 + low;
  }

  boolean fetch_output(int output) {
    int outputs = data_bits[41] * 256 + data_bits[40];
    return !!(outputs & (1 << (output - 1)));
  }

  int fetch_speed_step(int output) {
    byte index;
    // nur für die Ausgänge 1, 2, 6 und 7
    switch (output) {
    case 1:
      index = 42;
      break;
    case 2:
      index = 43;
      break;
    case 6:
      index = 44;
      break;
    case 7:
      index = 45;
      break;
    default:
      return -2;
    } 
    if (!!(data_bits[index] & 0x80))
      return -1;
    return data_bits[index] & 0x1f;
  }

}







