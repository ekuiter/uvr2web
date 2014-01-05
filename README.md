(There is also an English translation below.)

#uvr2web

**Weitere Informationen über uvr2web auf [meiner Website](http://elias-kuiter.de/projects/uvr2web/).**

uvr2web ist ein Programm zur Überwachung deiner UVR1611-Heizungsregelung.
Es kann Temperaturdaten visualisieren, diese auf deinem PC oder Smartphone darstellen und vieles mehr.

Die Features von uvr2web im Überblick:

- Upload von Heizungsdaten per Arduino
- Auswertung und Visualisierung dieser Daten durch:
  - mehrere Live-Übersichtsseiten mit Mini-Graphen
  - große interaktive Graphen mit Druck- und Downloadfunktionen (basiert auf [HighStock](http://www.highcharts.com/stock/demo/)
- verarbeitet 4 Arten von Messgeräten:
  - Sensoren (Temperaturen, Volumenströme, u.a.)
  - Ausgänge (Solaranlage angeschaltet?, Ölheizung angeschaltet?)
  - Wärmemengenzähler (Momentanleistung, kWh/MWh-Zähler)
  - Drehzahlstufen (Stufenregelung einzelner Ausgänge)
- Messgeräte sind zur besseren Übersicht:
  - benennbar (z.B. "Temperatur Holzofen")
  - gruppierbar (z.B. "Temperaturen Solaranlage")
- schnelle, ständig aktualisierte Live-Seiten
- moderne Bedienung durch [Twitter Bootstrap](http://twitter.github.io/bootstrap/)
- Schutz vor unbefugtem Zugriff durch Benutzerverwaltung
- spezielle Funktionen für Administratoren:
  - Statusseite für schnelles Einsehen der letzten Datensätze
  - Auswählen einer Sprache, verfügbar sind zur Zeit:
    - Deutsch
    - Englisch
    - Französisch (bei Übersetzungsfehlern bitte melden ! :)
  - Verwaltung von Benutzern und Messgeräten
  - E-Mail-Benachrichtigungen, zur Zeit:
    - automatische Warnungs-Mail, falls das Arduino-Board ausfällt oder auf andere Weise der Upload beeinträchtigt ist
    - wöchentliche Aufforderung zum Backup
  - Backup aller Einstellungen und Datensätze von uvr2web
  - Installations- und Deinstallationsassistenten

uvr2web funktioniert nur mit der UVR1611, Unterstützung für andere Regelungen ist nicht geplant.
Das Programm besteht aus zwei Teilen:

**Arduino-Sketch**

Der Arduino-Sketch kommuniziert mit der UVR1611. Dafür brauchst du natürlich ein Arduino-Board:
Am besten funktioniert das Arduino Leonardo. Das Arduino Uno ist auch möglich, allerdings fehlerträchtiger beim Datenempfang.
Andere Boards wurden nicht getestet, sind aber prinzipiell möglich.
Außerdem musst du ein einfaches Arduino-Shield löten (Spannungsteiler).
Die UVR1611 gibt ihre Daten mittels eines Manchester-Codes aus. Der Sketch dekodiert dieses Signal und schickt die Daten dann entweder an einen PC über eine serielle Verbindung oder über Ethernet an die uvr2web PHP-App.

**PHP-App**

Die PHP-App benötigt eine MySQL-Datenbank und die GD-Library. Sie empfängt die Daten vom Arduino-Board und speichert sie in der Datenbank. Danach hast du einige Möglichkeiten: Graphen anzeigen, Daten herunterladen, eine Live-Übersicht abrufen, und das alles mit einer schnellen, modernen Bedienung (durch Twitter Bootstrap) - außerdem noch eine Benutzerverwaltung, E-Mail-Versand und mehrere Sprachen.

## Erste Schritte

**Verbindung zur UVR1611**

Nachdem du uvr2web heruntergeladen hast, wirst du einen Ordner namens `src` sehen. Dieser enthält sowohl den Arduino-Sketch als auch die PHP-App.
Zunächst musst du einen Spannungsteiler löten, der die 12V-Spannung der UVR1611 auf eine 5V-Spannung reduziert. Wenn du dabei Hilfe brauchst, [kontaktiere mich](mailto:info@elias-kuiter.de). (Ich habe [hier](https://github.com/ekuiter/uvr2web/blob/master/voltage-divider.jpg) ein einfaches Schaltbild gezeichnet, wie dieser Spannungsteiler aussieht.)

**Arduino**

Danach kannst du den Arduino-Sketch konfigurieren. Versuche ihn zu kompilieren, und Arduino wird dir sagen, wo du noch Änderungen vornehmen musst. Die Kommentare erklären dir, was die Einstellungen bedeuten. Lies sie dir also genau durch, denn wenn du falsche Angaben machst, wird der Sketch nicht funktionieren.
Wenn du bereit bist, lade den Sketch hoch und schließe die UVR1611 am richtigen Pin an; je nachdem, ob du im Sketch die Optionen `DEBUG` oder `USINGPC` aktiviert hast, können unterschiedliche Ausgaben erfolgen.
- `DEBUG`: Entferne hier die Kommentarzeichen `//`, falls du die Daten, die die UVR1611 schickt, auf dem Serial Monitor ansehen willst. Dir werden dann ganz genau die Sensordaten etc. angezeigt. Durch Einfügen von `//` gelangst du wieder in den Upload-Modus.
- `USINGPC`: Entferne hier die Kommentarzeichen `//`, falls dein Arduino gerade am PC hängt. Dann wirst du Ausgaben im Serial Monitor erhalten. Im Produktivbetrieb (am Netzteil) solltest du diese Option durch Einfügen von `//` wieder ausschalten. (Hintergrund ist, dass die Ausgabe im Serial Monitor den Datenempfang von der UVR1611 fehlerhafter macht.)

Falls du Ausgaben wie
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
erhältst, sehr gut! (Dass ein `Data frame damaged` ist, kann schon einmal vorkommen, ist aber nicht weiter schlimm, da sofort ein neuer Datenrahmen mitgeschnitten wird.)

Wenn du die Meldung `DHCP failed. Program abort.` erhältst, überprüfe deine MAC-Adresse und ob dein Board korrekt mit dem Internet verbunden ist.

Falls der Datenempfang von der UVR1611 scheitert (z.B. `Receiving ...` und dann passiert nichts weiter), probiere einmal den Debug-Sketch aus.
Dieser ist unter `src/arduino/uvr2web_debug` zu finden. Gib hier noch einmal den `dataPin` und den `interrupt` an (siehe [AttachInterrupt](http://arduino.cc/en/Reference/AttachInterrupt)). Wenn auch dieser Sketch scheitert (keine Nullen-Einsen-Folge ausgibt), ist deine UVR1611 nicht richtig mit dem Arduino verbunden. [Melde dich](mailto:info@elias-kuiter.de) und ich werde versuchen, dir zu helfen.

**PHP**

Als nächstes kannst du die PHP-App auf deinem Server installieren. Lade dazu die PHP-Dateien im Ordner `src/server` auf den Server hoch, öffne dann das `install.php`-Skript in deinem Browser und befolge die Anweisungen.
Achte hier darauf, dass im Arduino-Sketch das gleiche Upload-Passwort und -Intervall eingestellt sind wie in der PHP-App und dass der Server richtig eingestellt ist. Nun kannst du die DEBUG-Option deaktivieren und damit den Web-Upload aktivieren.
Wenn alles funktioniert hat, solltest du nach dem Login eine Seite namens `Sensors` sehen, die dir einen Überblick über die Sensordaten gibt.
Glückwunsch! Du hast uvr2web installiert. Probier auch mal den `Admin`-Bereich aus, es gibt viele Features, die du ausprobieren kannst (z.B. die Sprache umzustellen).

## Dokumentation

Der Arduino-Sketch hat Inline-Kommentare, die zur Dokumentation dienen. Für die PHP-App gibt es eine zusätzliche, mit phpdoc generierte Dokumentation im Ordner `docs`.

## Skripte

Der Ordner `scripts` enthält ein paar Skripte. Sie sind zum Generieren der Dokumentation und zum Zählen von Codezeilen. Du kannst sie ignorieren.

# uvr2web

**More information about uvr2web on [my website](http://elias-kuiter.de/projects/uvr2web/en).**

uvr2web is a program for monitoring your UVR1611 heating control.
It can visualize temperature data on your PC or smartphone and much more.

uvr2web features at a glance:

- heating data upload via Arduino
- processing and visualization:
  - several live overview pages with mini graphs
  - big interactive graphs, printable and downloadable (based on [HighStock](http://www.highcharts.com/stock/demo/)
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
  - choosing a language, currently available are
    - English
    - German
    - French (please report mistranslations ! :)
  - management of users and measuring devices
  - e-mail notifications, currently:
    - warning mail if the Arduino crashes or there is another problem with the upload
    - weekly backup request
  - backup of all uvr2web settings and data records
  - installation and deinstallation wizards

uvr2web works only with the UVR1611, support for other heating controls is not planned.
The program consists of two parts:

**Arduino sketch**

The Arduino sketch communicates with the UVR1611. Obviously, you need an Arduino board for that.
That works best with the Arduino Leonardo. The Uno is possible too, but more buggy concerning data receiving.
Other Boards were not tested, but are basically possible too.
Additionally, you have to solder a little Arduino shield (voltage divider).
The UVR1611 outputs its data with a manchester code. The sketch decodes this signal and then sends the data to either a PC via a serial connection or the uvr2web PHP app via Ethernet.

**PHP app**

The PHP app needs a MySQL database and the GD library. It receives the data from the Arduino board and saves it in the database. You then have a number of options: Display charts, download data, fetch a live overview, and all that with a fast and modern interface (built with Twitter Bootstrap) - topped off with user management, emailing and various languages.

## Getting started

**UVR1611 connection**

After downloading uvr2web, you will see see a folder called `src`. It contains both Arduino sketch and PHP app.
You will need to solder a voltage divider that reduces the 12V from the UVR1611 to 5V for the Arduino. If you need help, [contact me](mailto:info@elias-kuiter.de). (I drew a schematic [here](https://github.com/ekuiter/uvr2web/blob/master/voltage-divider.jpg).)

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

If UVR1611 data receiving fails (e.g. `Receiving ...` and no more output), try out the debug sketch located at `src/arduino/uvr2web_debug`.
Enter `dataPin` and `interrupt` just as before (see [AttachInterrupt](http://arduino.cc/en/Reference/AttachInterrupt)).
If this sketch fails too (if there's no binary output) your UVR1611 is not connected properly. [Tell me](mailto:info@elias-kuiter.de), and I'll try to help you.

**PHP**

Next you can install the PHP app on your server. To do that, upload the PHP files in the `src/server` folder on your server, then open the `install.php` script in your browser and follow the instructions.
Now make sure the Arduino sketch has the same upload password and interval as the PHP app, and that the server is correct. You can then disable the `DEBUG` option and therefore enable the web upload.
If everything worked, you should see - after logging in - a page called `Sensors` that shows you an overview of the sensor data.
Congratulations! You installed uvr2web. Make sure to check out the `Admin` section, there are a lot of features you can try out.

## Documentation

The Arduino sketch has inline documentation through comments. For the PHP app there's an additional documentation (generated with phpdoc) in the folder `docs`.

## Scripts

The `scripts` folder contains a few scripts. They are for generating the documentation and counting lines of code. You can ignore them.
