## uvr2web

![uvr2web](https://raw.githubusercontent.com/ekuiter/uvr2web/img/uvr2web-example.png)

**uvr2web ist ein Programm zur Überwachung deiner UVR1611-Heizungsregelung.**

Es kann Temperaturdaten visualisieren, diese auf deinem PC oder Smartphone darstellen und vieles mehr.

Die wichtigsten Infos auf einen Blick:

- **[online ausprobieren](http://uvr2web.elias-kuiter.de/?demo)** (keine Administrator-Funktionen und API)
- **[als ZIP herunterladen](https://github.com/ekuiter/uvr2web/archive/master.zip)**
- uvr2web funktioniert **nur mit der Regelung UVR1611** von [Technische Alternative](http://ta.co.at).
   Die Datenübertragung erfolgt über den DL-Bus (Datenleitung, Ausgang 14).
- Für andere Regelungen gibt es folgende Alternativen / Anregungen:
   - **UVR31**: [martinkropf/UVR31_RF24](https://github.com/martinkropf/UVR31_RF24)
   - **UVR1611 mit BL-NET**: [berwinter/uvr1611](https://github.com/berwinter/uvr1611)
   - **UVR1611 mit ESP8266/MQTT**: [Buster01/UVR2MQTT](https://github.com/Buster01/UVR2MQTT)
   - **UVR61-3 mit ESP8266**: [Instructable: Monitoring Gebäudetrocknung](http://www.instructables.com/id/Monitoring-Geb%C3%A4udetrocknung/)
   - *Falls du eine eigene Ansteuerung für eine TA-Regelung entworfen hast (gern auch auf Basis meines Codes), [schreib mich an](mailto:info@elias-kuiter.de) und ich füge sie zu dieser Liste hinzu!*

## Features

Die Features von uvr2web im Überblick:

- Upload von Heizungsdaten per Arduino
- Auswertung und Visualisierung dieser Daten durch:
  - mehrere Live-Übersichtsseiten mit Mini-Graphen
  - große interaktive Graphen mit Druck- und Downloadfunktionen (basiert auf [HighStock](http://www.highcharts.com/stock/demo/))
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
  - Auswählen einer Sprache, verfügbar sind zur Zeit Deutsch, Englisch und Französisch (bei Übersetzungsfehlern bitte melden ! :)
  - Verwaltung von Benutzern und Messgeräten
  - E-Mail-Benachrichtigungen: automatische Warnungs-Mail, falls das Arduino-Board ausfällt oder auf andere Weise der Upload beeinträchtigt ist sowie wöchentliche Aufforderung zum Backup
  - Backup aller Einstellungen und Datensätze von uvr2web
  - Installations- und Deinstallationsassistenten

uvr2web funktioniert nur mit der UVR1611, Unterstützung für andere Regelungen siehe oben.
Das Programm besteht aus zwei Teilen:

**Arduino-Sketch**

Der Arduino-Sketch kommuniziert mit der UVR1611. Dafür brauchst du natürlich ein Arduino-Board. Getestet wurde mit dem Arduino Leonardo und Uno.
Andere Boards wurden nicht getestet, sind aber prinzipiell möglich.
Außerdem musst du ein einfaches Arduino-Shield löten, einen Spannungsteiler (s. unten).
Die UVR1611 gibt ihre Daten mittels eines Manchester-Codes aus. Der Sketch dekodiert dieses Signal und schickt die Daten dann entweder an einen PC über eine serielle Verbindung oder über Ethernet an die uvr2web PHP-App.

**PHP-App**

Die PHP-App benötigt eine MySQL-Datenbank und die GD-Library. Sie empfängt die Daten vom Arduino-Board und speichert sie in der Datenbank. Danach hast du einige Möglichkeiten: Graphen anzeigen, Daten herunterladen, eine Live-Übersicht abrufen, und das alles mit einer schnellen, modernen Bedienung (durch Twitter Bootstrap) - außerdem noch eine Benutzerverwaltung, E-Mail-Versand und mehrere Sprachen.

## Erste Schritte

**Verbindung zur UVR1611**

Nachdem du uvr2web heruntergeladen hast, befindet sich im Ordner `arduino/uvr2web` der Arduino-Sketch und in `php` die Serversoftware.
Zunächst musst du einen Spannungsteiler löten, der die 12V-Spannung der UVR1611 auf eine 5V-Spannung reduziert. Wenn du dabei Hilfe brauchst, [kontaktiere mich](mailto:info@elias-kuiter.de). (Ich habe [hier](meta/voltage-divider.jpg) ein einfaches Schaltbild gezeichnet, wie dieser Spannungsteiler aussieht.)

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
Dieser ist unter `arduino/uvr2web_debug` zu finden. Gib hier noch einmal den `dataPin` und den `interrupt` an (siehe [AttachInterrupt](http://arduino.cc/en/Reference/AttachInterrupt)). Wenn auch dieser Sketch scheitert (keine Nullen-Einsen-Folge ausgibt), ist deine UVR1611 nicht richtig mit dem Arduino verbunden. [Melde dich](mailto:info@elias-kuiter.de) und ich werde versuchen, dir zu helfen.

**PHP**

Als nächstes kannst du die PHP-App auf deinem Server installieren. Lade dazu die PHP-Dateien im Ordner `php` auf den Server hoch, öffne dann das `install.php`-Skript in deinem Browser und befolge die Anweisungen.
Achte hier darauf, dass im Arduino-Sketch das gleiche Upload-Passwort und -Intervall eingestellt sind wie in der PHP-App und dass der Server richtig eingestellt ist. Nun kannst du die DEBUG-Option deaktivieren und damit den Web-Upload aktivieren.
Wenn alles funktioniert hat, solltest du nach dem Login eine Seite namens `Sensors` sehen, die dir einen Überblick über die Sensordaten gibt.
Glückwunsch! Du hast uvr2web installiert. Probier auch mal den `Admin`-Bereich aus, es gibt viele Features, die du ausprobieren kannst (z.B. die Sprache umzustellen).

## Dokumentation

Der Arduino-Sketch hat Inline-Kommentare, die zur Dokumentation dienen.
Die Dokumentation der PHP-App findest du [hier](http://ekuiter.github.io/uvr2web/).
(Wenn du die Dokumentation selbst generieren möchtest, führe `phpdoc` im Ordner `php` aus.)

## Mehr erfahren

Im `meta`-Ordner findest du unter anderem Datenblätter zur Ansteuerung der UVR1611 (diese stammen direkt von [TA](http://www.ta.co.at)). Dort gibt es Infos zum CAN- und DL-Bus und das Handbuch der Regelung. Für Arduino-Bastler ist insbesondere die (auch hier genutzte) [DL-Schnittstelle](meta/Schnittstelle%20Datenleitung%201.7.pdf) interessant, da diese relativ einfach und günstig anzusteuern ist.

