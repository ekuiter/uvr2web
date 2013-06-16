/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 receive.h:
 Receive and save data frames by the heating control
 
 */

namespace Receive {
  
  // decoding the manchester code

  // pulse width at 488hz: 1000ms/488 = 2,048ms = 2048µs
  // 2048µs / 2 = 1024µs (2 pulses for one bit)
  const unsigned long pulse_width = 1024; // µs
  const int percentage_variance = 10; // % tolerance for variances at the pulse width
  // 1001 or 0110 are two sequential pulses without transition
  const unsigned long double_pulse_width = pulse_width * 2;
  // calculating the tolerance limits for variances
  const unsigned long low_width = pulse_width - (pulse_width *  percentage_variance / 100);
  const unsigned long high_width = pulse_width + (pulse_width * percentage_variance / 100);
  const unsigned long double_low_width = double_pulse_width - (pulse_width * percentage_variance / 100);
  const unsigned long double_high_width = double_pulse_width + (pulse_width * percentage_variance / 100);
  boolean got_first = 0; // first or second pulse for one bit?
  unsigned long last_bit_change = 0; // remember the last transition
  int bit_count; // number of received bits
  byte receiving; // currently receiving?
  
  void start(); // start receiving
  void stop(); // stop receiving
  void pin_changed(); // os called when the state of the data pin changes
  void process_bit(unsigned char b); // saves a bit detected by pin_changed

}




