# Stud.IP-Moodle-Connect
Moodle Konnektor für Stud.IP

Um den Konnektor zu verwenden, müssen einige Einstellungen in Moodle vorgenommen und der Konnektor in Stud.IP konfiguriert werden

## Moodle einrichten

Melden Sie sich als Admin in Ihrer Moodle-Installation an.

Gehen Sie zu "Website-Administration" -> "Plugins" -> "Webservices" -> "Übersicht" und erledigen Sie zumindest die beiden ersten Schritte "Webservices aktivieren" und "Protokolle aktivieren". Diese Plugin verwendet das "Protokoll REST".

### Benötigte Methoden der Moodle-API freigeben

Gehen Sie zu "Website-Administration" -> "Plugins" -> "Webservices" -> "Externe Services". Legen Sie dort mittels "hinzufügen" einen neuen Externen Service an. Die Benennung bleibt Ihnen überlassen. Außerdem können Sie nach Bedarf weitere Einstellungen vornehmen.

Nach Auswahl von "Service hinzufügen" kehren sie auf die Übersichtsseite zurück. Dort sehen Sie nun Ihren erstellten und benannten Service. Wählen Sie in der Zeile des Services "Funktionen" aus. Auf der folgenden Seite können Sie ausgewählte Moodle-API-Funktionen erlauben, auf die zugegriffen werden darf. Der Moodle-Konnektor benötigt Zugriff auf folgenden Funktionen (fügen Sie diese ALLE hinzu):

* core_course_create_courses
* core_enrol_get_enrolled_users
* core_user_create_users
* core_user_get_users
* core_course_get_courses
* enrol_manual_enrol_users
* core_enrol_get_users_courses

### Zugriffstoken erstellen

Bei Bedarf erstellen Sie einen speziellen Nutzeraccount für den Webzugriff oder wählen Sie im Folgenden einen vorhandenen Account aus:

Gehen Sie zu "Website-Administration" -> "Plugins" -> "Webservices" -> "Tokens verwalten". Wählen Sie dort Hinzufügen aus.
Wählen Sie einen Nutzeraccount, in dessen Namen die Webservice-Aufrufe ausgeführt werden. Bei Service wählen Sie den im vorherigen Schritt angelegten Eintrag. Bei Bedarf können Sie hier auch weitere Zugriffseinschränkungen vornehmen.

Nach Auswahl von ""Änderungen speichern" sehen Sie einen neuen Eintrag und einen Token. Diesen Token benötigen Sie für die Einrichtung des Konnektors in Stud.IP!

### Direkte Weiterleitung zum korrekten Kurs

Standardmäßig landen Sie nach der Weiterleitung zu Moodle auf der Kursübersichtsseite. Um direkt zum verknüpften Kurs in Moodle zu gelangen ist eine Anpassung im Moodle-Quellcode nötigt.

Ergänzen Sie in der Datei "moodle/login/index.php" an der folgenden Stelle den entsprechenden Dreizeiler:

    /// Initialize variables
    $errormsg = '';
    $errorcode = 0;

    + if ($_REQUEST['course_id']) {
    +     $SESSION->wantsurl = $CFG->wwwroot .'/course/view.php?id=' . (int)$_REQUEST['course_id'];
    + }

    // login page requested session test
    if ($testsession) {_

## Stud.IP konfigurieren

### Token und Webservice-URL eintragen

Nachdem Sie das Plugin in Stud.IP installiert und aktiviert haben, gehen Sie zu "Admin" -> "System" -> "Konfiguration" und klappen dort die Kategorie "moodle" auf. Darin befinden sich dei zwei Einstellungen MOODLE_API_TOKEN und MOODLE_API_URI. Den Token erhalten Sie bei der Konfiguration von Moodle (siehe oben), bei MOODLE_API_URI tragen Sie die URL zu Ihrer Moodle-Installation ohne abschließenden Slash ein.
