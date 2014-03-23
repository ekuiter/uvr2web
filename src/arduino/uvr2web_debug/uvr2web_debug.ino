/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 uvr2web_debug.ino:
 Verbindung zur UVR pruefen
 
 */

// Dieser Sketch ueberprüft die Verbindung zur UVR1611.
// dataPin und interrupt muessen angepasst werden.
// ( siehe http://arduino.cc/en/Reference/AttachInterrupt )
// Im Serial Monitor sollte nach einigen Sekunden
// etwas vergleichbares erscheinen:

//Der Datenempfang wird jetzt gestartet.
//Der Datenempfang wurde gestoppt.
//000010000010000010000000000000000000001001101111
//101001001001010000000000010100101000100010100111
//...
//100000000000000000000010000010000010000011001111
//000000100000001000000000

// Wenn das nicht erscheint, sind die Pinangaben nicht
// korrekt oder die physische Verbindung zur UVR funktioniert nicht.

const byte dataPin = /*2*/;
const byte interrupt = /*1*/;

// mit diesem Wert kannst du einstellen, wie viele Daten der Sketch
// mitschneiden soll. Für den Anfang belasse ihn auf 1, dann werden
// genau zwei UVR-Datenrahmen protokolliert (1312 Bytes).
// Falls du jedoch Zusatzhardware am DL-Bus benutzt, musst du evtl.
// hier Anpassungen vornehmen. (Damit ähnelt diese Einstellung
// 'additionalBits' aus dem Hauptsketch.)
// Um bspw. die vierfache Menge an Daten mitzuschreiben, trage hier 4 ein.
// Je größer dieser Wert, desto länger dauert der Datenempfang!
// Größer als 8 sollte dieser Wert nicht eingestellt werden, denn sonst
// reicht der Arbeitsspeicher des Arduino nicht aus.

const byte factor = /*1*/;

// sobald der Sketch hochgeladen wird

void setup() {
  Serial.begin(115200);
  while(!Serial);
  start();
}

byte finished = false;
// hier werden die von der UVR empfangenen Bits gespeichert.
const int bit_number = (64 * (8 + 1 + 1) + 16) * 2 * factor;
const int byte_number = bit_number / 8 + 1;
byte data_bits[byte_number];

void loop() {
  if (finished) { // wenn Daten empfangen wurden, Daten ausgeben
    Serial.println("Der Datenempfang wurde gestoppt.");
    for (int i = 0; i < byte_number; i++) {
      delay(5);
      if (i % 6 == 0)
        Serial.println();
      // fuehrende Nullen mit ausgeben
      if (data_bits[i]<128) Serial.print('0');
      if (data_bits[i]<64) Serial.print('0');
      if (data_bits[i]<32) Serial.print('0');
      if (data_bits[i]<16) Serial.print('0');
      if (data_bits[i]<8) Serial.print('0');
      if (data_bits[i]<4) Serial.print('0');
      if (data_bits[i]<2) Serial.print('0');
      // Daten ausgeben
      Serial.print(data_bits[i], BIN); 
    }
    finished = false;
  }
}

// diese Variablen sind dazu da, das Signal von der UVR zu 'entschluesseln'.
const unsigned long pulse_width = 1024;
const int percentage_variance = 10;
const unsigned long double_pulse_width = pulse_width * 2;
const unsigned long low_width = pulse_width - (pulse_width *  percentage_variance / 100);
const unsigned long high_width = pulse_width + (pulse_width * percentage_variance / 100);
const unsigned long double_low_width = double_pulse_width - (pulse_width * percentage_variance / 100);
const unsigned long double_high_width = double_pulse_width + (pulse_width * percentage_variance / 100);
boolean got_first = 0;
unsigned long last_bit_change = 0;
int bit_count = 0;

// hier wird der Datenempfang gestartet
void start() {
  Serial.println("Der Datenempfang wird jetzt gestartet.");
  bit_count = got_first = last_bit_change = 0;
  attachInterrupt(interrupt, pin_changed, CHANGE);
}

// Datenempfang beenden und loop() Bescheid geben, dass Daten da sind
void stop() {
  detachInterrupt(interrupt);
  finished = true;
}

// immer wenn sich die Spannung am dataPin ändert, wird pin_changed aufgerufen
// es wird berechnet, ob es sich um eine 0 oder eine 1 handelt
void pin_changed() {
  byte val = digitalRead(dataPin);
  unsigned long time_diff = micros() - last_bit_change;
  last_bit_change = micros();
  if (time_diff >= low_width && time_diff <= high_width) {
    process_bit(val);
    return;   
  }
  if (time_diff >= double_low_width && time_diff <= double_high_width) {
    process_bit(!val);
    process_bit(val);
    return;   
  } 
}

// für jedes Datenbit von der UVR wird process_bit aufgerufen
// die 0 oder 1 wird abgespeichert
void process_bit(byte b) {
  if (got_first == false) {
    got_first = true;
    return;  
  }
  got_first = false;
  write_bit(bit_count, b);
  bit_count++;
  if (bit_count == bit_number)
    stop();
}

// sichert das empfangene Datenbit
void write_bit(int pos, byte set) {
  int row = pos / 8; // detect position in bitmap
  int col = pos % 8;
  if (set)
    data_bits[row] |= 1 << col; // set bit
  else
    data_bits[row] &= ~(1 << col); // clear bit
}






