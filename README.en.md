(Auch auf [Deutsch](README.md) verfügbar.)

# uvr2web

**More information about uvr2web on [my website](http://elias-kuiter.de/projects/uvr2web/en).**

uvr2web is a program for monitoring your UVR1611 heating control.
It can visualize temperature data on your PC or smartphone and much more.

uvr2web features at a glance:

- heating data upload via Arduino
- processing and visualization:
  - several live overview pages with mini graphs
  - big interactive graphs, printable and downloadable (based on [HighStock](http://www.highcharts.com/stock/demo/))
- processes four kinds of measuring devices:
  - sensors (temperatures, volume flows, ...)
  - outputs (solar system powered on?, oil heating powered on?)
  - heat meters (current power, kWh/MWh counting)
  - speed steps (outputs regulated by stepping)
- measuring devices are 
  - nameable (e.g. "Temperature oil heating")
  - groupable (z.B. "Temperatures solar array")
- fast, always up-to-date live pages
- modern and responsive workflow with [Twitter Bootstrap](http://twitter.github.io/bootstrap/)
- access protection through user management
- special administrator features:
  - status page for a brief look on the latest data records
  - choosing a language, currently available are English, German and French (please report mistranslations ! :)
  - management of users and measuring devices
  - e-mail notifications, currently warning mail if the Arduino crashes or there is another problem with the upload as well as a weekly backup request
  - backup of all uvr2web settings and data records
  - installation and deinstallation wizards

uvr2web works only with the UVR1611, support for other heating controls is not planned.
The program consists of two parts:

**Arduino sketch**

The Arduino sketch communicates with the UVR1611. Obviously, you need an Arduino board for that. That works best with the Arduino Leonardo. The Uno is possible too, but more buggy concerning data receiving.
Other Boards were not tested, but are basically possible too.
Additionally, you have to solder a little Arduino shield, a voltage divider (see below).
The UVR1611 outputs its data with a manchester code. The sketch decodes this signal and then sends the data to either a PC via a serial connection or the uvr2web PHP app via Ethernet.

**PHP app**

The PHP app needs a MySQL database and the GD library. It receives the data from the Arduino board and saves it in the database. You then have a number of options: Display charts, download data, fetch a live overview, and all that with a fast and modern interface (built with Twitter Bootstrap) - topped off with user management, emailing and various languages.

## Getting started

**UVR1611 connection**

After downloading uvr2web, the folders `arduino/uvr2web` and `php` contain the Arduino sketch and the server software.
You will need to solder a voltage divider that reduces the 12V from the UVR1611 to 5V for the Arduino. If you need help, [contact me](mailto:info@elias-kuiter.de). (I drew a schematic [here](meta/voltage-divider.jpg).)

**Arduino**

After that you can configure the Arduino sketch. Try to compile it and Arduino will tell you where to make adjustments.
The comments explain what each setting means. Read them thoroughly to prevent your sketch from failing.
If you're ready, upload the sketch and connect the UVR1611 to the correct pin; different outputs are possible, depending on whether you activated the `DEBUG` or `USINGPC` options in the sketch.
- `DEBUG`: Remove the comment `//`, if you want the UVR1611 data (all sensor and temperature ata) to be shown in Serial Monitor. By inserting `//` you activate the upload mode again.
- `USINGPC`: Remove the comment `//`, if your Arduino is connected to your PC. You will get output in Serial Monitor. In production mode (with power adaptor) you should disable this by inserting `//`. (Background: The output in Serial Monitor makes UVR1611 data receiving difficult.)

If you get output similar to
```
Receiving ... 
Received. 
Upload ...
Upload finished.

Receiving ... 
Received. Data frame damaged.
Receiving ... 
Received. 
Upload ...
Upload finished.
```
you're all set. (A `Data frame damaged` is not a big deal because a new data frame will be received instantly.)

If you get `DHCP failed. Program abort.`, check your MAC address and your board's network connection.

If UVR1611 data receiving fails (e.g. `Receiving ...` and no more output), try out the debug sketch located at `arduino/uvr2web_debug`.
Enter `dataPin` and `interrupt` just as before (see [AttachInterrupt](http://arduino.cc/en/Reference/AttachInterrupt)).
If this sketch fails too (if there's no binary output) your UVR1611 is not connected properly. [Tell me](mailto:info@elias-kuiter.de), and I'll try to help you.

**PHP**

Next you can install the PHP app on your server. To do that, upload the PHP files in the `php` folder on your server, then open the `install.php` script in your browser and follow the instructions.
Now make sure the Arduino sketch has the same upload password and interval as the PHP app, and that the server is correct. You can then disable the `DEBUG` option and therefore enable the web upload.
If everything worked, you should see - after logging in - a page called `Sensors` that shows you an overview of the sensor data.
Congratulations! You installed uvr2web. Make sure to check out the `Admin` section, there are a lot of features you can try out.

## Documentation

The Arduino sketch has inline documentation through comments.
For the PHP app you can find some documentation [here](http://ekuiter.github.io/uvr2web/).
(If you want to generate the docs yourself, execute `phpdoc` in the `php` folder.)

## Learn more
The `meta` folder contains datasheets for the UVR1611 (from the heating control's manufacturer [TA](http://www.ta.co.at/en).) There's a manual and info on the CAN and DL buses. For use with Arduino I recommend the [DL interface](meta/Schnittstelle%20Datenleitung%201.6.pdf) because it's easy and cheap to use.