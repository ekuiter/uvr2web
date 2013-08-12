#uvr2web

**Weitere Informationen über uvr2web auf [elias-kuiter.de](http://elias-kuiter.de) und [blog.elias-kuiter.de](http://blog.elias-kuiter.de).**

uvr2web ist ein Programm zur Überwachung deiner UVR1611-Heizungsregelung.
Es kann Temperaturdaten visualisieren, diese auf deinem PC oder Smartphone darstellen und vieles mehr.

uvr2web funktioniert nur mit der UVR1611, Unterstützung für andere Regelungen ist nicht geplant.
Das Programm besteht aus zwei Teilen:

**Arduino-Sketch**

Der Arduino-Sketch kommuniziert mit der UVR1611. Dafür brauchst du natürlich ein Arduino-Board (getestet auf dem Leonardo).
Außerdem musst du ein einfaches Arduino-Shield löten (Spannungsteiler).
Die UVR1611 gibt ihre Daten mittels eines Manchester-Codes aus. Der Sketch dekodiert dieses Signal und schickt die Daten dann entweder an einen PC über eine serielle Verbindung oder über Ethernet an die uvr2web PHP-App.

**PHP-App**

Die PHP-App benötigt eine MySQL-Datenbank und die GD-Library. Sie empfängt die Daten vom Arduino-Board und speichert sie in der Datenbank. Danach hast du einige Möglichkeiten: Graphen anzeigen, Daten herunterladen, eine Live-Übersicht abrufen, und das alles mit einer schnellen, modernen Bedienung (durch Twitter Bootstrap) - außerdem noch eine Benutzerverwaltung, E-Mail-Versand und mehrere Sprachen. Eine genaue Feature-Liste gibt es auf meinem [Blog](http://blog.elias-kuiter.de).

## Erste Schritte

Nachdem du uvr2web heruntergeladen hast, wirst du einen Ordner namens *src* sehen. Dieser enthält sowohl den Arduino-Sketch als auch die PHP-App.
Zunächst musst du einen Spannungsteiler löten, der die 12V-Spannung der UVR1611 auf eine 5V-Spannung reduziert. Wenn du dabei Hilfe brauchst, [kontaktiere mich](mailto:info@elias-kuiter.de).
Danach kannst du den Arduino-Sketch konfigurieren, auf dein Board übertragen und es an die UVR1611 anschließen. Wenn du die DEBUG-Option aktiviert hast, sollte dir dein Serial-Monitor jetzt einige Datenrahmen ausgeben.
Als nächstes kannst du die PHP-App auf deinem Server installieren. Lade dazu die PHP-Dateien im Ordner *src/server* auf den Server hoch, öffne dann das *install.php*-Skript in deinem Browser und befolge die Anweisungen.
Achte hier darauf, dass im Arduino-Sketch das gleiche Upload-Passwort und -Intervall eingestellt sind wie in der PHP-App und dass der Server richtig eingestellt ist. Nun kannst du die DEBUG-Option deaktivieren und damit den Web-Upload aktivieren.
Wenn alles funktioniert hat, solltest du nach dem Login eine Seite namens *Sensors* sehen, die dir einen Überblick über die Sensordaten gibt.
Glückwunsch! Du hast uvr2web installiert. Probier auch mal den *Admin*-Bereich aus, es gibt viele Features, die du ausprobieren kannst (z.B. die Sprache umzustellen).

## Dokumentation

Der Arduino-Sketch hat Inline-Kommentare, die zur Dokumentation dienen. Für die PHP-App gibt es eine zusätzliche, mit phpdoc generierte Dokumentation im Ordner *docs*.

## Skripte

Der Ordner *scripts* enthält ein paar Skripte. Sie sind zum Generieren der Dokumentation und zum Zählen von Codezeilen. Du kannst sie ignorieren.

# uvr2web

**More information about uvr2web on [elias-kuiter.de](http://elias-kuiter.de) and [blog.elias-kuiter.de](http://blog.elias-kuiter.de).**

uvr2web is a program for monitoring your UVR1611 heating control.
It can visualize temperature data on your PC or smartphone and much more.

uvr2web works only with the UVR1611, support for other heating controls is not planned.
The program consists of two parts:

**Arduino sketch**

The Arduino sketch communicates with the UVR1611. Obviously, you need an Arduino board for that (tested on Leonardo).
Additionally, you have to solder a little Arduino shield (voltage divider).
The UVR1611 outputs its data with a manchester code. The sketch decodes this signal and then sends the data to either a PC via a serial connection or the uvr2web PHP app via Ethernet.

**PHP app**

The PHP app needs a MySQL database and the GD library. It receives the data from the Arduino board and saves it in the database. You then have a number of options: Display charts, download data, fetch a live overview, and all that with a fast and modern interface (built with Twitter Bootstrap) - topped off with user management, emailing and various languages. A detailed feature list is on my [blog](http://blog.elias-kuiter.de).

## Getting started

After downloading uvr2web, you will see see a folder called *src*. It contains both Arduino sketch and PHP app.
You will need to solder a voltage divider that reduces the 12V from the UVR1611 to 5V for the Arduino. If you need help, [contact me](mailto:info@elias-kuiter.de).
After that you can configure the Arduino sketch, upload it to your board and connect it to the UVR1611. If you activated the DEBUG option, your serial monitor should show some data frames.
Next you can install the PHP app on your server. To do that, upload the PHP files in the *src/server* folder on your server, then open the *install.php* script in your browser and follow the instructions.
Now make sure the Arduino sketch has the same upload password and interval as the PHP app, and that the server is correct. You can then disable the DEBUG option and therefore enable the web upload.
If everything worked, you should see - after logging in - a page called *Sensors* that shows you an overview of the sensor data.
Congratulations! You installed uvr2web. Make sure to check out the *Admin* section, there are a lot of features you can try out.

## Documentation

The Arduino sketch has inline documentation through comments. For the PHP app there's an additional documentation (generated with phpdoc) in the folder *docs*.

## Scripts

The *scripts* folder contains a few scripts. They are for generating the documentation and counting lines of code. You can ignore them.