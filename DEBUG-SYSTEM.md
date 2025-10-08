# Debug-System für SchulAG v2

## Übersicht

Das Debug-System wurde implementiert, um Probleme mit Modals und HTMX-Requests zu diagnostizieren. Es bietet umfassende Logging-Funktionen für die Browser-Konsole.

## Verwendung

### Debug-Meldungen in der Konsole anzeigen

Öffnen Sie die Browser-Konsole (F12 oder Rechtsklick → "Untersuchen" → "Konsole") und Sie sehen automatisch farbcodierte Debug-Meldungen.

### Debug-System aktivieren/deaktivieren

```javascript
// Debugging deaktivieren
SchulAGDebug.enabled = false;

// Debugging aktivieren
SchulAGDebug.enabled = true;
```

### Manuelles Logging

Sie können auch eigene Debug-Meldungen hinzufügen:

```javascript
// Einfache Log-Meldung
SchulAGDebug.log('Info', 'Meine Nachricht');

// Log-Meldung mit Daten
SchulAGDebug.log('Modal', 'Button geklickt', { id: 123, name: 'Test' });

// Fehler loggen
SchulAGDebug.error('Fehler aufgetreten', errorObject);
```

## Kategorien

Das System unterstützt folgende Kategorien mit unterschiedlichen Farben:

- **Alpine** (türkis): Alpine.js Events und Store-Operationen
- **HTMX** (blau): HTMX Requests und Responses
- **Modal** (lila): Modal-Öffnungen und -Schließungen
- **Error** (rot): Fehler und Exceptions
- **Success** (grün): Erfolgreiche Operationen
- **Info** (blau): Allgemeine Informationen

## Überwachte Events

### Alpine.js
- Initialisierung von Alpine.js
- editModal Store Registrierung
- Modal-Öffnungen und -Schließungen
- Button-Klicks auf Bearbeiten-Buttons

### HTMX
- Request-Start (`htmx:beforeRequest`)
- Request-Abschluss (`htmx:afterRequest`)
- Content-Austausch (`htmx:afterSwap`)
- Content-Settling (`htmx:afterSettle`)
- Response-Fehler (`htmx:responseError`)
- Send-Fehler (`htmx:sendError`)

### Modals
- Initialisierung von Modal-Elementen
- Öffnen von Modals (Neuer Schüler, Bearbeiten)
- Schließen von Modals

## Fehlerbehebung

### Modal öffnet sich nicht

1. Überprüfen Sie in der Konsole, ob:
   - Alpine.js erfolgreich geladen wurde
   - Der editModal Store verfügbar ist
   - Der Button-Klick Event registriert wird

2. Prüfen Sie die Daten, die an das Modal übergeben werden

3. Überprüfen Sie, ob JavaScript-Fehler aufgetreten sind

### HTMX-Request schlägt fehl

1. Überprüfen Sie die Request-URL in der Konsole
2. Prüfen Sie den Status-Code der Response
3. Sehen Sie sich die Response-Daten an

### Alpine.js wird nach HTMX-Request nicht initialisiert

Das System re-initialisiert Alpine.js automatisch nach jedem HTMX-Content-Swap. Überprüfen Sie in der Konsole, ob die Re-Initialisierung erfolgreich war.

## System-Check beim Laden

Beim Laden der Seite wird automatisch ein System-Check durchgeführt:

```
🎓 SchulAG v2 Debug System
──────────────────────────────────────
Verwenden Sie SchulAGDebug.enabled = false um das Debugging zu deaktivieren
Debug-Kategorien: Alpine, HTMX, Modal, Error, Success, Info
──────────────────────────────────────
[Info] Alpine.js verfügbar: true
[Info] HTMX verfügbar: true
[Info] Tailwind verfügbar: true
```

## Wichtige Hinweise

- Das Debug-System ist standardmäßig aktiviert (`SchulAGDebug.enabled = true`)
- Alle Debug-Meldungen werden nur in der Browser-Konsole angezeigt
- Das System hat keine Auswirkungen auf die Performance, wenn es deaktiviert ist
- Für Produktions-Deployments sollte das Debug-System deaktiviert werden

## Beispiel-Session

```javascript
// Seite lädt
[Info] 📄 Seite vollständig geladen
[Alpine] Alpine.js wird initialisiert...
[Alpine] editModal Store wurde registriert
[Alpine] ✅ Alpine.js erfolgreich initialisiert!
[Alpine] ✅ editModal Store ist verfügbar

// Benutzer klickt auf "Bearbeiten"-Button
[Modal] Bearbeiten-Button geklickt
📊 Data: { id: 5, name: "Max Mustermann", typ_gl: "G" }
[Modal] Modal wird geöffnet
📊 Data: { id: 5, name: "Max Mustermann", typ_gl: "G" }
[Modal] Modal Status: show=true

// Benutzer speichert Änderungen
[HTMX] 🚀 Request wird gestartet...
📊 Data: { method: "PUT", url: "/admin/klassen/1/schueler/5" }
[HTMX] ✅ Request abgeschlossen
📊 Data: { successful: true, status: 200 }
[HTMX] 🔄 Content wurde getauscht
📊 Data: { target: "schueler-list" }
[Alpine] Initialisiere Alpine für neuen Content...
[Alpine] ✅ Alpine für neuen Content initialisiert
[Modal] Modal wird geschlossen
```

## Support

Bei Problemen mit dem Debug-System oder wenn Sie zusätzliche Debug-Funktionen benötigen, öffnen Sie bitte die Browser-Konsole und teilen Sie die Debug-Ausgaben mit.

