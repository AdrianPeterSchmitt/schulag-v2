# ⚡ 5-MINUTEN SCHNELL-TEST

**Status:** App ist bereit zum Testen  
**URL:** http://localhost/schulag-v2/public/login

---

## 🚀 **START:**

1. **Chrome Inkognito öffnen** (Strg+Shift+N)
2. **URL eingeben:** http://localhost/schulag-v2/public/login
3. **Login:**
   - Email: `admin@schulag.test`
   - Passwort: `admin123`
   - **Erwartung:** Weiterleitung zur Arbeitsseite ✅

---

## ✅ **TEST 1: Admin-Bereich (1 Min)**

**Navigation → "Verwaltung"**

### Klasse anlegen:
- Button "Neue Klasse" → Modal öffnet sich
- Name: `5a`
- Jahrgang: `5`
- Klassenleitung: `Frau Müller`
- Speichern
- **Erwartung:** Klasse erscheint in Liste ✅

### Schüler hinzufügen:
- Bei Klasse "5a" → "Anzeigen" klicken
- Button "Neuer Schüler"
- Name: `Max Mustermann`, Typ: `G` → Speichern
- Name: `Lisa Schmidt`, Typ: `LE` → Speichern
- **Erwartung:** 2 Schüler sichtbar ✅

---

## ✅ **TEST 2: AGs anlegen (1 Min)**

**Navigation → "Verwaltung" → Tab "AGs"**

### AG erstellen:
- Button "Neue AG"
- Titel: `Fußball`
- Lehrkraft: `Herr Meyer`
- Jahrgänge: `5,6,7`
- Kapazität: `12`
- Beschreibung: `Fußball spielen`
- Speichern
- **Erwartung:** AG erscheint in Liste ✅

### Angebot aktivieren:
- Bei AG "Fußball" → "Angebot erstellen"
- Kapazität: `12`
- Raum: `Turnhalle`
- Aktiv: ✅ Häkchen setzen
- Speichern
- **Erwartung:** Status "Aktiv" sichtbar ✅

---

## ✅ **TEST 3: Wahlen eintragen (1 Min)**

**Navigation → "Klassen"**

- Klasse `5a` auswählen
- Für `Max Mustermann`:
  - Priorität 1: `Fußball` wählen
  - Priorität 2: (kann leer bleiben)
  - Priorität 3: (kann leer bleiben)
- Für `Lisa Schmidt`:
  - Priorität 1: `Fußball` wählen
- Button "Wahlen speichern"
- **Erwartung:** Erfolgsmeldung ✅

---

## ✅ **TEST 4: Losverfahren (30 Sek)**

**Navigation → "Losverfahren"**

- Status prüfen:
  - Schüler mit Wahlen: 2
  - AG-Angebote: 1
  - Kapazität: 12
- Button "Losverfahren starten" (sollte aktiv sein)
- Klicken und warten
- **Erwartung:** 
  - Erfolgsmeldung ✅
  - Statistiken aktualisiert ✅
  - Run in Historie sichtbar ✅

---

## ✅ **TEST 5: Ergebnisse & Statistiken (1 Min)**

### Ergebnisse:
**Navigation → "Losverfahren" → "Ergebnisse anzeigen"**
- **Erwartung:** 
  - Beide Schüler haben Zuteilung ✅
  - AG "Fußball" sichtbar ✅

### Statistiken:
**Navigation → "Losverfahren" → "Statistiken"**
- **Erwartung:**
  - 4 Übersichts-Karten sichtbar ✅
  - AG-Tabelle mit Auslastung ✅
  - Losverfahren-Historie mit letztem Run ✅

---

## ✅ **TEST 6: Exporte (30 Sek)**

### PDF Export:
- Zur Statistik-Seite
- Button "PDF Export" (falls vorhanden)
- **Erwartung:** Download startet ✅

### Excel Export:
- Button "Excel Export" (falls vorhanden)
- **Erwartung:** Download startet ✅

---

## ✅ **TEST 7: Logout (10 Sek)**

- Oben rechts: "Logout" klicken
- **Erwartung:** 
  - Zurück zur Login-Seite ✅
  - Erfolgsmeldung "Erfolgreich abgemeldet" ✅

---

## 🎯 **CHECKLISTE:**

- [ ] Login funktioniert
- [ ] Klasse anlegen funktioniert
- [ ] Schüler hinzufügen funktioniert
- [ ] AG anlegen funktioniert
- [ ] Angebot aktivieren funktioniert
- [ ] Wahlen speichern funktioniert
- [ ] Losverfahren durchführen funktioniert
- [ ] Ergebnisse anzeigen funktioniert
- [ ] Statistiken anzeigen funktioniert
- [ ] Exporte funktionieren (PDF/Excel)
- [ ] Logout funktioniert

---

## ✅ **BEI ERFOLG:**

**→ ALLE FUNKTIONEN ARBEITEN!** 🎉

Die App ist **production-ready** und kann eingesetzt werden.

---

## ⚠️ **BEI PROBLEMEN:**

### "Zu viele Weiterleitungen":
- Chrome komplett schließen
- Erneut Inkognito öffnen
- Nochmal versuchen

### "Seite lädt nicht":
- XAMPP Apache läuft? (Port 80)
- URL korrekt: `http://localhost/schulag-v2/public/login`
- Ohne `:8080` am Ende!

### "Keine Berechtigung":
- Admin-Rolle prüfen: `php fix-admin-role.php`
- Erneut einloggen

---

**Viel Erfolg beim Testen! 🚀**

*Geschätzte Dauer: 5-7 Minuten*

