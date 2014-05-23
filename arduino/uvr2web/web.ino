/*  
 ~~~~~~~
 uvr2web
 ~~~~~~~
 © Elias Kuiter 2013 (http://elias-kuiter.de)
 
 web.ino:
 Hochladen der Datenrahmen ins Internet via Ethernet
 Upload data frames to the internet via Ethernet
 
 */

#ifndef DEBUG

namespace Web {

  void start() {
    Serial.println("Initialising ...");
    if (Ethernet.begin(mac) == 0) {
      Serial.println("DHCP failed. Program abort.");
      Receive::stop();
      while(1);
    }
  }

  void upload() {
    while(millis() - upload_finished < upload_interval);
    if (client.connect(server, 80)) {
      Serial.println("\nUpload ...");
      request = "GET ";
      request += script;
      request += "?";
      request += pass;
      CONCAT;
      client.print(request);
      request = "";
      sensors();
      heat_meters();
      outputs();
      speed_steps();
      request += " HTTP/1.0";
      client.println(request);
      client.println();
      while (working());
      upload_finished = millis();
    } 
    else
      Serial.println("Server connection failed.");
  }

  boolean working() {
    while (client.available())
      client.read();
    if (!client.connected()) {
      Serial.println("Upload finished.");
      client.stop();
      return false;
    }
    return true;
  }

  void sensors() {
    for (int i = 1; i <= 16; i++) {
      Process::fetch_sensor(i);
      sensor();
    }
    client.print(request);
    request = "";
  }

  // number&value*10&type&mode
  void sensor() {
    request += Process::sensor.number;
    CONCAT;
    if (Process::sensor.invalid)
      request += "-";
    else
      request += (int) (Process::sensor.value * 10);
    CONCAT;
    request += Process::sensor.type;
    CONCAT;
    if (Process::sensor.type == ROOM)
      request += Process::sensor.mode;
    else
      request += "-";
    CONCAT;
  }

  void heat_meters() {
    Process::fetch_heat_meter(1);
    heat_meter();
    Process::fetch_heat_meter(2);
    heat_meter();
    client.print(request);
    request = "";
  }

  // number&current_power*100&kwh*10&mwh
  void heat_meter() {
    request += Process::heat_meter.number;
    CONCAT;
    if (Process::heat_meter.invalid) {
      request += "-";
      CONCAT;
      return;
    }
    request += (int) (Process::heat_meter.current_power * 100);
    CONCAT;
    request += (int) (Process::heat_meter.kwh * 10);
    CONCAT;
    request += Process::heat_meter.mwh;
    CONCAT;
  }

  // number&value
  void outputs() {
    for (int i = 1; i <= 13; i++) {
      request += i;
      CONCAT;
      request += Process::fetch_output(i); 
      CONCAT;
    }
    client.print(request);
    request = "";
  }

  void speed_steps() {
    speed_step(1);
    CONCAT;
    speed_step(2);
    CONCAT;
    speed_step(6);
    CONCAT;
    speed_step(7);
  }

  //number&value
  void speed_step(int output) {
    request += output;
    CONCAT;
    int speed_step = Process::fetch_speed_step(output);
    if (speed_step == -2)
      request += "-";
    else if (speed_step == -1)
      request += "-";
    else
      request += speed_step;
  }

}

#endif
