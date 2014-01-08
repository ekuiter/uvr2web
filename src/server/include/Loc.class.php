<?php

/**
 * Contains Loc class
 *
 * @package Localization
 */

/**
 * Localization
 *
 * Provides translations and language management.
 *
 * @package Localization
 */

class Loc {

  /**
   * Default language
   */
  private static $language = 'en';
  /**
   * Translations
   */
  private static $table = array(
    'en' => array(
      'header' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>
			uvr2web
		</title>
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
	</head>
	<body>
	<div style="font-family:Arial;font-size:18px;text-align:center;margin:100px auto;border:1px solid black;padding:20px;background-color:#eee;width:700px">',
	    'footer' => '</div>
	</body>
	</html>',
      'status' => 'Status',
      'sensor' => 'Sensor',
      'sensors' => 'Sensors',
      'output' => 'Output',
      'outputs' => 'Outputs',
      'heat meter' => 'Heat meter',
      'heat meters' => 'Heat meters',
      'speed step' => 'Speed step',
      'speed steps' => 'Speed steps',
      'admin' => 'Admin',
      'logout' => 'Logout',
      'about' => 'About',
      'docs' => 'Documentation',
      'imprint' => 'Imprint',
      'users' => 'Users',
      'user' => 'User',
      'overview' => 'Overview',
      'admin body' => 'You can browse through the settings on the left side.',
      'change aliases' => 'Change aliases',
      'change order' => 'Change order',
      'here you can' => 'Here you can change the aliases for the elements in the',
      'specified order' => 'specified order',
      'here you can 2' => '',
      'here you can heat meters' => 'Here you can change the aliases for the activated heat meters.',
      'save' => 'Save',
      'cancel' => 'Cancel',
      'all aliases' => 'All aliases have been saved.',
      'drag to' => 'Drag to rearrange the elements. Settings are saved immediately.',
      'add separator' => 'Add separator',
      'name separators' => 'you can name the separators',
      'here' => 'here',
      'name separators 2' => '',
      'group' => 'Group',
      'add user' => 'Add user',
      'username' => 'Username',
      'password hash' => 'Password hash',
      'role' => 'Role',
      'password' => 'Password',
      'password confirmation' => 'Confirm password',
      'edit' => 'Edit',
      'remove' => 'Remove',
      'language' => 'Language',
      'english' => 'English',
      'german' => 'German',
      'french' => 'French',
      'admin deleted' => 'Admin successfully deleted.',
      'user deleted' => 'User successfully deleted.',
      'last admin' => 'The last Admin can\'t be deleted.',
      'remove 1' => 'Are you sure you want do remove ',
      'remove 2' => '?',
      'remove 3' => 'You can\'t undo this.',
      'remove 4' => ' Remove ',
      'remove 5' => '',
      'sure' => 'I\'m sure',
      'passwords dont match' => 'Passwords don\'t match.',
      'edit 1' => 'Successfully updated ',
      'edit 2' => '.',
      'edit 3' => 'Edit ',
      'edit 4' => '',
      'add 1' => 'New user <em>dummy</em> generated. Please change username and password.',
      'add 2' => 'Adding a new user failed. Does <em>dummy</em> already exist?',
      'current power' => 'Current power',
      'kwh' => 'kWh',
      'mwh' => 'MWh',
      'log in' => 'Log in',
      'login incorrect' => 'Login incorrect.',
      'smallest value' => 'Smallest value',
      'highest value' => 'Highest value',
      'notifications' => 'Notifications',
      'email' => 'E-Mail address',
      'emails' => 'E-Mail addresses',
      'comma-separated' => '(comma-separated)',
      'notifications body' => 'Notifications are sent to your e-mail addresses. They contain status information or possible problems.',
      'notifications body 2' => 'Notify if no data has been uploaded for ',
      'notifications body 4' => 'Remind every ',
      'notifications body 5' => ' days that a backup should be created.',
      'notifications body 3' => ' minutes.',
      'data record' => 'New data record saved!',
      'md5 hash' => 'MD5 hash',
      'frames uploaded' => 'data frames uploaded',
      'frames until' => 'data frames until next data record (~',
      'frames until 2' => ' minutes)',
      'current data frame' => 'Current data frame',
      'last data record' => 'Last data record on ',
      'last data record 2' => ' at ',
      'last data record 3' => '',
      'upload issues' => 'Upload issues',
      'notification' => 'uvr2web notification',
      'no upload notification body' => 'Apparently there are upload issues with your uvr2web installation.',
      'no upload notification body 2' => 'The last data frame was uploaded more than',
      'no upload notification body 3' => 'minutes ago (on',
      'no upload notification body 4' => 'at',
      'no upload notification body 5' => 'If you don\'t take action, the Arduino <strong>won\'t upload new data frames</strong> to uvr2web.<br />
    You should make sure that the Arduino is connected to a power supply, the internet and the UVR1611. <br />
    In most cases <strong>restarting the Arduino solves the issue</strong>. If not, you can check the Arduino in the debug mode (described in the Arduino sketch).<br />
    If you need further help, <a href="mailto:info@elias-kuiter.de">contact me</a>.',
      'notification footer' => 'You received this mail because you activated the appropriate option',
      'notification footer 2' => 'here',
      'notification footer 3' => '.',
      'notification footer 4' => 'Please do not answer this mail. It was generated automatically.',
      'backup notification' => 'Backup',
      'backup notification body' => 'The last backup of uvr2web was',
      'backup notification body 2' => 'days ago.',
      'backup notification body 3' => 'Here',
      'backup notification body 4' => 'you can download the most recent backup.',
      'backup' => 'Backup',
      'do backup' => 'Create backup',
      'backup body' => 'The backup contains all database tables of uvr2web.
    <ul>
    <li>Settings</li>
    <li>Data frames</li>
    <li>Users</li>
    </ul>
    It can be restored while installing uvr2web.',
      'uninstall' => 'Uninstall',
      'uninstall uvr2web' => 'Uninstall uvr2web',
      'uninstall body' => '<p><strong>Pity! You want to deinstall uvr2web :(</strong></p>
    <p>Please tell me <a href="mailto:info@elias-kuiter.de">here</a> why, hence I can improve the program.</a></p>
    <p>The deinstallation consists of two steps:</p>
    <ol>
    <li>Deleting the database tables</li>
    <li>Deleting the files</li>
    </ol>
    <p>
    uvr2web can take care of step 1.<br />
    But deleting the files on the FTP server afterwards is up to you.
    <p>It is recommended to create a <a href="index.php?p=admin&sub=backup">backup</a> before the deinstallation.</p>',
    'uninstall backup' => 'Do you want to create a backup before the deinstallation?\nIf you did this already, click Cancel.',
    'uninstall sure' => 'Are you REALLY SURE you want to deinstall uvr2web?',
    'uninstall body 2' => 'The database tables have been deleted.<br />
    uvr2web is now disabled.</p>
    <p>Please delete the files on the FTP server now:</p>
    <ol>
    <li>Open your FTP client (z.B. <a href="http://sourceforge.net/projects/filezilla/" target="_blank">Filezilla</a>).</li>
    <li>Connect to this server.</li>
    <li>Delete the uvr2web folder.</li>',
    'no data frames' => '<h4>No data frames!</h4>
    No data frames are in the database. All graphs and device pages won\'t work until a data frame is uploaded.<br />
    Make sure that the Arduino board is configured correctly. (Especially the upload password and interval.)<br />
    If it is, this warning should disappear if you reload the page in ~',
    'no data frames 2' => 'seconds.',
    'months' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
    'object removed' => 'Object removed successfully.',
    'object enabled' => 'Object enabled successfully.',
    'disabled' => 'is disabled',
    'enable' => 'enable',
    'status ok' => 'Everything alright!',
    'status failed' => 'Upload problems!',
    ),

    'de' => array(
      'status' => 'Status',
      'sensor' => 'Sensor',
      'sensors' => 'Sensoren',
      'outputs' => 'Ausgänge',
      'output' => 'Ausgang',
      'heat meter' => 'Wärmemengenzähler',
      'heat meters' => 'Wärmemengenzähler',
      'speed step' => 'Drehzahlstufe',
      'speed steps' => 'Drehzahlstufen',
      'admin' => 'Admin',
      'logout' => 'Abmelden',
      'about' => 'Über',
      'docs' => 'Dokumentation',
      'imprint' => 'Impressum',
      'user' => 'Benutzer',
      'users' => 'Benutzer',
      'overview' => 'Übersicht',
      'admin body' => 'Auf der linken Seite kannst du weitere Einstellungen abrufen.',
      'change aliases' => 'Bezeichnungen ändern',
      'change order' => 'Reihenfolge ändern',
      'here you can' => 'Hier kanst du die Bezeichnungen für die Elemente in der',
      'specified order' => 'angegebenen Reihenfolge',
      'here you can 2' => ' ändern',
      'here you can heat meters' => 'Hier kannst du die Bezeichnungen für die aktiven Wärmemengenzähler ändern.',
      'save' => 'Speichern',
      'cancel' => 'Abbrechen',
      'all aliases' => 'Alle Bezeichnungen wurden gespeichert.',
      'drag to' => 'Um Elemente neu anzuordnen, kannst du sie mit der Maus verschieben. Die Änderungen werden automatisch gespeichert.',
      'add separator' => 'Trenner hinzufügen',
      'name separators' => 'Du kannst die Trenner',
      'here' => 'hier',
      'name separators 2' => ' benennen',
      'group' => 'Gruppe',
      'add user' => 'Benutzer hinzufügen',
      'username' => 'Benutzername',
      'password hash' => 'Passwort-Hash',
      'role' => 'Berechtigungen',
      'password' => 'Passwort',
      'password confirmation' => 'Passwort bestätigen',
      'edit' => 'Bearbeiten',
      'remove' => 'Löschen',
      'language' => 'Sprache',
      'english' => 'Englisch',
      'german' => 'Deutsch',
      'french' => 'Französisch',
      'admin deleted' => 'Admin erfolgreich gelöscht.',
      'user deleted' => 'Benutzer erfolgreich gelöscht.',
      'last admin' => 'Der letzte Admin kann nicht gelöscht werden.',
      'remove 1' => 'Willst du ',
      'remove 2' => ' wirklich löschen?',
      'remove 3' => 'Dies kannst du nicht rückgängig machen.',
      'remove 4' => '',
      'remove 5' => ' löschen',
      'sure' => 'Löschen',
      'passwords dont match' => 'Die Passwörter stimmen nicht überein.',
      'edit 1' => '',
      'edit 2' => ' erfolgreich gespeichert.',
      'edit 3' => '',
      'edit 4' => ' bearbeiten',
      'add 1' => 'Neuer Benutzer <em>dummy</em> wurde erstellt. Bitte ändere Benutzernamen und Passwort.',
      'add 2' => 'Benutzer hinzufügen fehlgeschlagen. Gibt es den Benutzer <em>dummy</em> schon?',
      'current power' => 'Momentanleistung',
      'kwh' => 'kWh',
      'mwh' => 'MWh',
      'log in' => 'Anmelden',
      'login incorrect' => 'Falsche Zugangsdaten.',
      'smallest value' => 'Kleinster Messwert',
      'highest value' => 'Größter Messwert',
      'notifications' => 'Benachrichtigungen',
      'email' => 'E-Mail-Adresse',
      'emails' => 'E-Mail-Adressen',
      'comma-separated' => '(kommagetrennt)',
      'notifications body' => 'Benachrichtigungen werden an deine E-Mail-Adressen versendet. Sie enthalten Statusinformationen oder mögliche Probleme.',
      'notifications body 2' => 'Benachrichtigen, wenn seit ',
      'notifications body 3' => ' Minuten keine Daten mehr hochgeladen wurden.',
      'notifications body 4' => 'Alle ',
      'notifications body 5' => ' Tage erinnern, dass eine Sicherung erstellt werden sollte.',
      'data record' => 'Neuer Datensatz gespeichert!',
      'md5 hash' => 'MD5-Prüfsumme',
      'frames uploaded' => 'Datenrahmen hochgeladen',
      'frames until' => 'Datenrahmen bis zum nächsten Datensatz (~',
      'frames until 2' => ' Minuten)',
      'current data frame' => 'Aktueller Datenrahmen',
      'last data record' => 'Letzter Datensatz vom ',
      'last data record 2' => ' um ',
      'last data record 3' => ' Uhr',
      'upload issues' => 'Upload-Probleme',
      'notification' => 'uvr2web Benachrichtigung',
      'no upload notification body' => 'Anscheinend gibt es Upload-Probleme mit deiner uvr2web-Installation.',
      'no upload notification body 2' => 'Der letzte Datenrahmen wurde vor mehr als',
      'no upload notification body 3' => 'Minuten hochgeladen (am',
      'no upload notification body 4' => 'um',
      'no upload notification body 5' => 'Wenn du nicht eingreifst, wird das Arduino <strong>keine neuen Daten mehr zu uvr2web hochladen</strong>.<br />
    Gehe bitte sicher, dass das Arduino mit einer Stromquelle, dem Internet und der UVR1611 verbunden ist. <br />
    In den meisten Fällen kannst du das Problem lösen, indem du <strong>das Arduino neu startest</strong>. Wenn das nicht funktioniert, kannst du das Arduino im Debug-Modus betreiben (näheres dazu im Arduino-Sketch).<br />
    Für weitere Hilfe kannst du mich <a href="mailto:info@elias-kuiter.de">hier</a> kontaktieren.',
      'notification footer' => 'Du hast diese E-Mail erhalten, weil du die entsprechende Option',
      'notification footer 2' => 'hier',
      'notification footer 3' => ' aktiviert hast.',
      'notification footer 4' => 'Bitte antworte nicht auf diese E-Mail. Sie wurde automatisch generiert.',
      'backup notification' => 'Sicherung',
      'backup notification body' => 'Die letzte Sicherung von uvr2web ist',
      'backup notification body 2' => 'Tage her.',
      'backup notification body 3' => 'Hier',
      'backup notification body 4' => 'kannst du die neueste Sicherung herunterladen.',
      'backup' => 'Sicherung',
      'do backup' => 'Sicherung erstellen',
      'backup body' => 'Die Sicherung umfasst alle Datenbanktabellen von uvr2web.
    <ul>
    <li>Einstellungen</li>
    <li>Datenrahmen</li>
    <li>Benutzer</li>
    </ul>
    Sie kann während der Installation von uvr2web wieder eingespielt werden.',
      'uninstall' => 'Deinstallieren',
      'uninstall uvr2web' => 'uvr2web deinstallieren',
      'uninstall body' => '<p><strong>Schade, dass du uvr2web deinstallieren willst :(</strong></p>
    <p>Teile mir <a href="mailto:info@elias-kuiter.de">hier</a> doch bitte mit, warum, damit ich das Programm verbessern kann.</a></p>
    <p>Die Deinstallation besteht aus zwei Schritten:</p>
    <ol>
    <li>Löschen der Datenbanktabellen</li>
    <li>Löschen der Dateien</li>
    </ol>
    <p>
    Schritt 1 kann uvr2web für dich erledigen.<br />
    Das Löschen der Dateien auf dem FTP-Server musst du danach hingegen selbst durchführen.
    <p>Es wird empfohlen, vor der Deinstallation eine <a href="index.php?p=admin&sub=backup">Sicherung</a> zu erstellen.</p>',
    'uninstall backup' => 'Willst du vor der Deinstallation noch eine Sicherung erstellen?\nWenn du dies bereits getan hast, klicke auf Abbrechen.',
    'uninstall sure' => 'Bist du WIRKLICH SICHER, dass du uvr2web deinstallieren willst?',
    'uninstall body 2' => 'Die Datenbanktabellen wurden gelöscht.<br />
    uvr2web ist nun deaktiviert.</p>
    <p>Lösche jetzt bitte die Dateien auf dem FTP-Server:</p>
    <ol>
    <li>Öffne deinen FTP-Client (z.B. <a href="http://sourceforge.net/projects/filezilla/" target="_blank">Filezilla</a>).</li>
    <li>Verbinde dich auf diesen Server.</li>
    <li>Lösche den uvr2web-Ordner.</li>',
    'no data frames' => '<h4>Keine Datenrahmen!</h4>
    Es sind keine Datenrahmen in der Datenbank. Alle Graphen und Messgeräte werden fehlerhaft angezeigt, bis ein Datenrahmen hochgeladen wird.<br />
    Sorge dafür, dass das Arduino-Board richtig konfiguriert ist. (Vor allem das Upload-Passwort und -Intervall.)<br />
    Wenn alles funktioniert, sollte diese Warnung verschwinden, sobald du die Seite in ~',
    'no data frames 2' => 'Sekunden neu lädst.',
      'months' => array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'),
      'object removed' => 'Objekt erfolgreich entfernt.',
      'object enabled' => 'Objekt erfolgreich aktiviert.',
      'disabled' => 'ist deaktiviert',
      'enable' => 'aktivieren',
      'status ok' => 'Alles in Ordnung!',
    'status failed' => 'Upload-Probleme!',
    ),
    
    'fr' => array(
      'status' => 'Statut',
      'sensor' => 'Capteur',
      'sensors' => 'Capteurs',
      'outputs' => 'Sorties',
      'output' => 'Sortie',
      'heat meter' => 'Compteur de chaleur',
      'heat meters' => 'Compteurs de chaleur',
      'speed step' => 'Palier de vitesse',
      'speed steps' => 'Paliers de vitesse',
      'admin' => 'Admin',
      'logout' => 'Se déconnecter',
      'about' => 'Plus d\'informations',
      'docs' => 'Documentation',
      'imprint' => 'Mentions légales',
      'user' => 'Utilisateur',
      'users' => 'Utilisateurs',
      'overview' => 'Vue d\'ensemble',
      'admin body' => 'Sur la gauche tu peux consulter autres paramètres.',
      'change aliases' => 'Changer des désignations',
      'change order' => 'Changer d\'ordre',
      'here you can' => 'Ici tu peux changer les désignations des éléments dans',
      'specified order' => 'l\'ordre indiqué',
      'here you can 2' => '',
      'here you can heat meters' => 'Ici tu peux changer les désignations des compteurs de chaleur actifs.',
      'save' => 'Enregistrer',
      'cancel' => 'Annuler',
      'all aliases' => 'Toutes les désignations étaient enregistrés.',
      'drag to' => 'Pour agencer des éléments tu peux les déplacer avec la souris. Les changements sont enregistrés automatique.',
      'add separator' => 'Ajouter séparateur',
      'name separators' => 'Te peux nommer les séparateurs',
      'here' => 'ici',
      'name separators 2' => '',
      'group' => 'Groupe',
      'add user' => 'Ajouter utilisateur',
      'username' => 'Nom d\'utilisateur',
      'password hash' => 'Mot de passe (hash)',
      'role' => 'Autorisations',
      'password' => 'Mot de passe',
      'password confirmation' => 'Valider mot de passe',
      'edit' => 'Modifier',
      'remove' => 'Effacer',
      'language' => 'Langue',
      'english' => 'Anglais',
      'german' => 'Allemande',
      'french' => 'Français',
      'admin deleted' => 'Admin effacé avec succès.',
      'user deleted' => 'Utlilisateur effacé avec succès.',
      'last admin' => 'Le dernier admin ne peut pas être effacé.',
      'remove 1' => 'Est-ce que tu veux vraiment effacer ',
      'remove 2' => '?',
      'remove 3' => 'Tu ne peut pas rapporter ça.',
      'remove 4' => 'Effacer ',
      'remove 5' => '',
      'sure' => 'Je suis sûr',
      'passwords dont match' => 'Les mots de passe ne corrrespondent pas.',
      'edit 1' => '',
      'edit 2' => ' enregistré avec succès.',
      'edit 3' => 'Modifier',
      'edit 4' => '',
      'add 1' => 'Le nouveau utilisateur <em>dummy</em> était crée. Change le nom d\'utilisateur et le mot de passe.',
      'add 2' => 'Ajouter un utilisateur est échoué. Est-ce que l\'utilisateur <em>dummy</em> existe déjà?',
      'current power' => 'Puissance instantanée',
      'kwh' => 'kWh',
      'mwh' => 'MWh',
      'log in' => 'S\'inscrire',
      'login incorrect' => 'Données d\'accès fausses.',
      'smallest value' => 'Plus petite mesure',
      'highest value' => 'Plus grande mesure',
      'notifications' => 'Notifications',
      'email' => 'Adresse électronique',
      'emails' => 'Adresses électroniques',
      'comma-separated' => '(séparés par des virgules)',
      'notifications body' => 'Des notifications sont envoyées à tes adresses électroniques. Elles contiennent d\'informations d\'état ou des problèmes possibles.',
      'notifications body 2' => 'Informer si aucunes informations n\'étaient téléchargé depuis ',
      'notifications body 3' => ' minutes.',
      'notifications body 4' => 'Rappeler tous les ',
      'notifications body 5' => ' jours qu\'une copie de sauvegarde doit être crée.',
      'data record' => 'Nouveau enregistrement sauvegardé!',
      'md5 hash' => 'Somme de contrôle (MD5)',
      'frames uploaded' => 'cadre d\'informations téléchargé',
      'frames until' => 'cadre d\'informations jusqu\'au prochain cadre d\'informations (~',
      'frames until 2' => ' minutes)',
      'current data frame' => 'Actuel cadre d\'informations',
      'last data record' => 'Dernier cadre d\'informations du ',
      'last data record 2' => ' à ',
      'last data record 3' => '',
      'upload issues' => 'Problèmes à tèlècharger',
      'notification' => 'uvr2web notification',
      'no upload notification body' => 'Apparemment il y a des problèmes à tèlècharger avec ton installation de uvr2web.',
      'no upload notification body 2' => 'Il y a plus que',
      'no upload notification body 3' => 'minutes que le dernier cadre d\'informations était téléchargé (le ',
      'no upload notification body 4' => 'à',
      'no upload notification body 5' => 'Si tu n\'agis pas, ta carte Arduino <strong>ne téléchargera aucunes informations à uvr2web</strong>.<br />
     Assure-toi que la carte Arduino est raccordé à une source de courant,  au internet et à la UVR1611.<br />
    Dans la plupart des cas tu peux résoudre le problème <strong>en redémarrant la carte Arduino</strong>. Si ça ne marche pas, tu peux utiliser la carte Arduino dans le mode débogage (pour plus de détails, voir le sketch de la carte Arduino).<br />
    Pour plus d\'aide, tu peux me contacter <a href="mailto:info@elias-kuiter.de">ici</a>.',
      'notification footer' => 'Tu as reçu cet e-mail parce que tu as activé l\'option correspondante',
      'notification footer 2' => 'ici',
      'notification footer 3' => '.',
      'notification footer 4' => 'Ne réponds pas à cet e-mail. Il était généré automatique.',
      'backup notification' => 'Copie de sauvegarde',
      'backup notification body' => 'La dernière copie de sauvegarde de uvr2web était il y a',
      'backup notification body 2' => 'jours.',
      'backup notification body 3' => 'Ici',
      'backup notification body 4' => 'tu peux télécharger la plus nouvelle copie de sauvegarde.',
      'backup' => 'Copie de sauvegarde',
      'do backup' => 'Créer une copie de sauvegarde',
      'backup body' => 'La copie de sauvegarde comprend tous les tableaux de la base de données de uvr2web.
    <ul>
    <li>Paramètres</li>
    <li>Cadres d\'informations</li>
    <li>Utilisateurs</li>
    </ul>
    Elle peut être restauré de nouveau pendant l\'installation de uvr2web.',
      'uninstall' => 'Désinstaller',
      'uninstall uvr2web' => 'Désinstaller uvr2web',
      'uninstall body' => '<p><strong>C\'est dommage que tu veux désinstaller uvr2web :(</strong></p>
    <p>Dis-moi pourquoi <a href="mailto:info@elias-kuiter.de">ici</a> pour que je peux améliorer uvr2web.</a></p>
      <p>La désinstallation a deux pas:</p>
    <ol>
    <li>Effacer les tableaux de la base de données</li>
    <li>Effacer les fichiers</li>
    </ol>
    <p>
    uvr2web peut accomplir pas 1 pour toi.<br />
    Mais effacer les fichiers sur le serveur ensuite, c\'est à toi.
    <p>C\'est recommandé de créer une <a href="index.php?p=admin&sub=backup">copie de sauvegarde</a> avant la désinstallation.</p>',
    'uninstall backup' => 'Veux-tu créer une copie de sauvegarde avant la désinstallation?\nSi tu l\'as fait déjà, clique Annuler.',
    'uninstall sure' => 'Es-tu VRAIMENT SÛR que tu veux désinstaller uvr2web?',
    'uninstall body 2' => 'Les tableaux de la base de données étaient effacés.<br />
    uvr2web est desactivé.</p>
    <p>Maintenant, efface les fichiers sur le serveur:</p>
    <ol>
    <li>Ouvre ta logiciel de FTP (par exemple <a href="http://sourceforge.net/projects/filezilla/" target="_blank">Filezilla</a>).</li>
    <li>Raccorde à ce serveur.</li>
    <li>Efface le classeur « uvr2web ».</li>
    </ol>',
    'no data frames' => '<h4>Aucuns cadres d\'informations!</h4>
    Il n\'y a aucuns cadres d\'informations dans la base de données. Tous les graphes et mesures sont affichés incorrectement jusqu\'au cadre d\'information est téléchargé.<br />
    Occupe-toi de la configuration correcte de la carte Arduino. (Surtout le mot de passe et l\'intervalle à télécharger.)<br />
    Si tout fonctionne, cet avertissement disparaîtra si tu actualise la page dans ~',
    'no data frames 2' => ' secondes.',
      'months' => array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'),
      'object removed' => 'Objet effacé avec succès.',
      'object enabled' => 'Objet activé avec succès.',
      'disabled' => 'est desactivé',
      'enable' => 'activer',
      'status ok' => 'Tout est bien!',
    'status failed' => 'Problèmes avec le téléchargement!',
    ),
  );

  /**
   * Gets the language
   */
  public static function get_language() {
    return self::$language;
  }

  /**
   * Sets the language
   * @param string $language
   */
  public static function set_language($language) {
    DB::query("UPDATE uvr2web_config SET config_value='" . DB::escape($language) . "' WHERE config_key='language'");
    self::$language = $language;
  }

  /**
   * Reads the current language
   */
  public static function init() {
    $result = DB::query("SELECT * FROM uvr2web_config WHERE config_key='language'");
    if ($result) {
      $language = $result[0]['config_value'];
    } else {
      $language = 'en';
      DB::query("INSERT INTO uvr2web_config (config_key, config_value) VALUES('language', '$language')");
    }
    self::$language = $language;
  }

  /**
   * Translates a string
   * @param string $key
   */
  public static function t($key) {
    return self::$table[self::$language][$key];
  }

  /**
   * Creates an array from a MySQL timestamp
   */
  public static function mysql_timestamp($timestamp) {
    $parts = explode(' ', $timestamp);
    $timestamp1 = explode('-', $parts[0]);
    $timestamp2 = explode(':', $parts[1]);
    $timestamp = array_merge($timestamp1, $timestamp2);
    return $timestamp;
  }

  /**
   * Localizes a value
   * @param mixed $value
   */
  public static function l($value) {
    if (is_array($value)) {
      if (isset($value['l'])) {
        if ($value['l'] == 'date') {
          $months = Loc::t('months');
          $month = $months[(int) $value[1] - 1];
          switch (self::$language) {
          case 'en':
          case 'fr':
            return "$value[2] $month $value[0]";
            break;
          case 'de':
            return "$value[2]. $month $value[0]";
            break;
          }
        } else if ($value['l'] == 'time') {
          switch (self::$language) {
          case 'en':
          case 'de':
          case 'fr':
            return "$value[3]:$value[4]";
            break;
          }
        }
      } else {
        throw new Exception('Please specify l=>date or l=>time');
      }
    } else {
      switch (self::$language) {
      case 'en':
        return (string) $value;
        break;
      case 'de':
      case 'fr':
        return str_replace('.', ',', (string) $value);
        break;
      }
    }
  }

}

?>