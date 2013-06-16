/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 receive.ino:
 Receive and save data frames by the heating control
 
 */

namespace Receive {

  void start() {
    bit_count = got_first = last_bit_change = 0;
    receiving = true;
    // on a CHANGE on the data pin pin_changed is called
    attachInterrupt(interrupt, pin_changed, CHANGE);
  }

  void stop() {
    detachInterrupt(interrupt); 
    receiving = false;
  }

  void pin_changed() {
    byte val = digitalRead(dataPin); // read state
    unsigned long time_diff = micros() - last_bit_change;
    last_bit_change = micros();
    // singe pulse width?
    if (time_diff >= low_width && time_diff <= high_width) {
      process_bit(val);
      return;   
    }
    // double pulse width?
    if (time_diff >= double_low_width && time_diff <= double_high_width) {
      process_bit(!val);
      process_bit(val);
      return;   
    } 
  }

  void process_bit(byte b) {
    // ignore first pulse
    if (got_first == false) {
      got_first = true;
      return;  
    }
    got_first = false;

    Process::data_bytes[bit_count] = b; // save bit
    bit_count++;

    // display progress bar
    if (bit_count == 1)
      Serial.print("\nReceiving "); 
    if (bit_count % 32 == 0)
      Serial.print("."); 

    if (bit_count == 1312)
      stop(); // stop receiving when data frame is complete
  }

}


