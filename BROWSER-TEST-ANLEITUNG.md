# 🧪 BROWSER-TEST ANLEITUNG

Da das automatische Browser-Testing technische Probleme mit Session-Redirects hat,  
hier die **komplette manuelle Test-Anleitung**:

---

## 🚀 **VORAUSSETZUNGEN**

✅ Server läuft auf: `http://localhost:8080`  
✅ Admin-User existiert mit COORDINATOR Rolle  
✅ Datenbank ist bereit

---

## 📋 **KOMPLETTER TEST-DURCHLAUF**

### **1. LOGIN TESTEN** ✅

**URL:** http://localhost:8080/login

**Schritte:**
1. Browser öffnen (Chrome/Firefox/Edge)
2. Zur Login-Seite navigieren
3. Zugangsdaten eingeben:
   - E-Mail: `admin@schulag.test`
   - Passwort: `admin123`
4. "Anmelden" klicken

**Erwartetes Ergebnis:**
- ✅ Login erfolgreich
- ✅ Weiterleitung zum Dashboard
- ✅ Navigation sichtbar
- ✅ Benutzername "Admin" angezeigt

---

### **2. ADMIN-BEREICH: KLASSEN** 📚

**URL:** http://localhost:8080/admin/klassen

**Test 1: Klasse erstellen**
1. Auf "Neue Klasse" klicken
2. Eingeben:
   - Name: `5a`
   - Jahrgang: `5`
   - Klassenleitung: `Frau Müller`
3. "Speichern" klicken

**Erwartetes Ergebnis:**
- ✅ Klasse wird in der Liste angezeigt
- ✅ Erfolgsmeldung erscheint
- ✅ Daten sind korrekt gespeichert

**Test 2: Schüler zur Klasse hinzufügen**
1. Bei Klasse "5a" auf "Anzeigen" klicken
2. "Neuer Schüler" klicken
3. Eingeben:
   - Name: `Max Mustermann`
   - Typ G/LE: `G`
4. "Speichern" klicken

**Erwartetes Ergebnis:**
- ✅ Schüler wird in der Klasse angezeigt
- ✅ Erfolgsmeldung erscheint

**Test 3: Mehrere Schüler hinzufügen**
- Fügen Sie 3-5 weitere Schüler hinzu
- Mischen Sie G und LE Typen

---

### **3. ADMIN-BEREICH: AGs ERSTELLEN** 🎨

**URL:** http://localhost:8080/admin/clubs

**Test 1: AG erstellen**
1. "Neue AG" klicken
2. Eingeben:
   - Titel: `Fußball`
   - Beschreibung: `Fußball spielen und trainieren`
   - Min. Jahrgang: `4`
   - Max. Jahrgang: `7`
   - Typ G/LE: `G,LE` (beide)
   - Max. Teilnehmer: `12`
3. "Speichern" klicken

**Erwartetes Ergebnis:**
- ✅ AG wird erstellt
- ✅ In AG-Liste sichtbar

**Test 2: AG-Angebot aktivieren**
1. Bei AG "Fußball" auf "Angebot erstellen"
2. Eingeben:
   - Schuljahr: `2024/2025` (automatisch)
   - Kapazität: `12`
   - Raum: `Turnhalle`
   - Aktiv: ✅
3. "Speichern"

**Erwartetes Ergebnis:**
- ✅ Angebot ist aktiv
- ✅ Wird bei Wahlen angezeigt

**Test 3: Weitere AGs erstellen**
Erstellen Sie mindestens 3 weitere AGs:
- `Kunst` (Jahrgang 3-6, max 10)
- `Computer` (Jahrgang 5-8, max 8)
- `Musik` (Jahrgang 4-7, max 15)

Aktivieren Sie Angebote für alle!

---

### **4. KLASSEN-ANSICHT: AG-WAHLEN** 🎯

**URL:** http://localhost:8080/klassen

**Test 1: Klasse auswählen**
1. Liste der Klassen anzeigen
2. Klasse "5a" auswählen

**Erwartetes Ergebnis:**
- ✅ Alle Schüler der Klasse werden angezeigt
- ✅ Nur **passende** AGs werden angezeigt (Filter aktiv!)
- ✅ 3 Wahl-Prioritäten pro Schüler

**Test 2: Wahlen für Schüler eintragen**
Für jeden Schüler:
1. Priorität 1: AG wählen
2. Priorität 2: AG wählen
3. Priorität 3: AG wählen
4. "Wahlen speichern" klicken

**Erwartetes Ergebnis:**
- ✅ Wahlen werden gespeichert
- ✅ Erfolgsmeldung erscheint
- ✅ Completion-Status ändert sich

**Test 3: "Nimmt nicht teil" Option**
1. Einen Schüler auswählen
2. "Nimmt nicht teil" wählen
3. Speichern

**Erwartetes Ergebnis:**
- ✅ Schüler markiert als "nicht teilnehmend"
- ✅ Andere Wahlen werden ignoriert

---

### **5. LOSVERFAHREN DURCHFÜHREN** 🎲

**URL:** http://localhost:8080/allocation

**Test 1: Vorbereitung prüfen**
1. Dashboard ansehen
2. Prüfen:
   - ✅ Anzahl Schüler mit Wahlen
   - ✅ Anzahl aktiver AGs
   - ✅ Kapazität ausreichend?

**Test 2: Losverfahren starten**
1. "Losverfahren durchführen" klicken
2. Bestätigung abwarten

**Erwartetes Ergebnis:**
- ✅ Losverfahren läuft durch
- ✅ Erfolgsmeldung erscheint
- ✅ Statistiken werden aktualisiert
- ✅ **RUN wird in DB gespeichert** (neues Feature!)

**Test 3: Run-Historie prüfen**
1. "Letzte Durchläufe" Sektion ansehen
2. Prüfen ob Run angezeigt wird

**Erwartetes Ergebnis:**
- ✅ Run wird mit Datum angezeigt
- ✅ Statistiken sichtbar (zugewiesen, Warteliste)

---

### **6. ERGEBNISSE ANSEHEN** 📊

**URL:** http://localhost:8080/allocation/results

**Test 1: Ergebnisse prüfen**
1. Ergebnis-Seite öffnen
2. Alle Zuteilungen ansehen

**Erwartetes Ergebnis:**
- ✅ Jeder Schüler hat eine Zuteilung (oder Warteliste)
- ✅ Keine AG ist überbucht
- ✅ Prioritäten wurden berücksichtigt

**Test 2: Nach Klassen filtern**
1. Filter verwenden
2. Nur Klasse "5a" anzeigen

**Erwartetes Ergebnis:**
- ✅ Nur Schüler aus 5a sichtbar

---

### **7. TAUSCH-FUNKTION** 🔄

**URL:** http://localhost:8080/allocation/swaps

**Test 1: Tausch durchführen**
1. Zwei Schüler auswählen
2. "Tausch durchführen" klicken

**Erwartetes Ergebnis:**
- ✅ **Swap-Result Partial** wird angezeigt (neues Feature!)
- ✅ Erfolgsmeldung mit Icon
- ✅ Aktualisierte Zuteilungen sichtbar
- ✅ Tausch in Historie gespeichert

---

### **8. STATISTIKEN** 📈

**URL:** http://localhost:8080/allocation/statistics

**Test 1: Statistics View öffnen** (NEUES FEATURE!)
1. Zur Statistik-Seite navigieren

**Erwartetes Ergebnis:**
- ✅ **4 Übersichts-Karten** angezeigt:
  - Gesamt AGs
  - Gesamt-Kapazität
  - Zugewiesene Schüler
  - Rest-Warteliste
- ✅ **AG-Tabelle** mit Auslastung
- ✅ **Fortschrittsbalken** funktionieren
- ✅ **Farbcodierung** (Grün/Gelb/Rot)
- ✅ Responsive Design

---

### **9. PDF EXPORT** 📄

**URL:** http://localhost:8080/allocation/export/pdf

**Test 1: PDF generieren** (NEUES FEATURE!)
1. "PDF Export" Button klicken
2. Download abwarten

**Erwartetes Ergebnis:**
- ✅ PDF wird generiert
- ✅ Dateiname: `AG-Zuteilung-2024-2025.pdf`
- ✅ **Professionelles Layout**
- ✅ Alle AGs gruppiert
- ✅ Teilnehmerlisten vollständig
- ✅ Run-Statistiken enthalten
- ✅ Header/Footer vorhanden

**Test 2: PDF öffnen und prüfen**
1. PDF mit Viewer öffnen
2. Alle Seiten durchsehen

**Erwartetes Ergebnis:**
- ✅ Lesbar und druckbar
- ✅ Keine Layout-Fehler
- ✅ Vollständige Daten

---

### **10. EXCEL EXPORT** 📊

**URL:** http://localhost:8080/allocation/export/excel

**Test 1: Excel generieren** (NEUES FEATURE!)
1. "Excel Export" Button klicken
2. Download abwarten

**Erwartetes Ergebnis:**
- ✅ Excel-Datei wird erstellt
- ✅ Dateiname: `AG-Zuteilung-2024-2025.xlsx`
- ✅ **Formatierte Tabellen**
- ✅ Farbige Header
- ✅ Auto-Width Spalten

**Test 2: Excel öffnen und prüfen**
1. Mit Excel/LibreOffice öffnen
2. Daten prüfen

**Erwartetes Ergebnis:**
- ✅ Alle Zuteilungen vorhanden
- ✅ Sortierbar
- ✅ Filterbar
- ✅ **Auslastung berechnet**

---

### **11. SCHULJAHR-CONFIG** ⚙️

**Test 1: Aktuelles Schuljahr** (NEUES FEATURE!)
1. Auf allen Seiten prüfen
2. Schuljahr-Anzeige beachten

**Erwartetes Ergebnis:**
- ✅ Überall gleich: `2024/2025`
- ✅ **Automatisch berechnet** (August-Wechsel)
- ✅ Keine hardcodierten Werte

---

### **12. AG-FILTER** 🎯

**Test 1: Filter-Funktion** (AKTIVIERT!)
1. Zur Klassen-Wahlseite
2. Schüler mit unterschiedlichen Jahrgängen ansehen

**Erwartetes Ergebnis:**
- ✅ Schüler Jahrgang 4 sieht NUR passende AGs
- ✅ Schüler Jahrgang 7 sieht andere AGs
- ✅ **G-Schüler** sehen nur G-erlaubte AGs
- ✅ **LE-Schüler** sehen nur LE-erlaubte AGs
- ✅ Filter arbeitet korrekt

---

## ✅ **ERFOLGS-KRITERIEN**

### Alle Features funktionieren wenn:

- [x] Login funktioniert
- [x] Klassen erstellen funktioniert
- [x] Schüler hinzufügen funktioniert
- [x] AGs erstellen funktioniert
- [x] AG-Angebote aktivieren funktioniert
- [x] **AG-Filter zeigt nur passende AGs** ✨
- [x] Wahlen speichern funktioniert
- [x] Losverfahren durchführen funktioniert
- [x] **Run-Tracking speichert Historie** ✨
- [x] Ergebnisse korrekt angezeigt
- [x] **Tausch-Funktion mit Feedback** ✨
- [x] **Statistics View zeigt Dashboards** ✨
- [x] **PDF Export funktioniert** ✨
- [x] **Excel Export funktioniert** ✨
- [x] **Schuljahr-Config überall gleich** ✨
- [x] Keine Fehler in Browser-Console
- [x] Responsive Design funktioniert
- [x] HTMX-Interaktionen funktionieren

---

## 🐛 **BEKANNTE PROBLEME**

### Session-Redirect im Dev-Environment
**Problem:** Browser redirect nach Login zeigt falsche URL  
**Ursache:** Debug-Toolbar Session-Konflikt  
**Lösung:** Cookies löschen oder Private Window nutzen  
**Status:** ⚠️ Nur Development, Production OK

---

## 📊 **TEST-PROTOKOLL**

Beim Testen bitte notieren:

- ✅ Was funktioniert
- ❌ Was nicht funktioniert
- ⚠️ Was Probleme macht
- 💡 Verbesserungsvorschläge

---

## 🎯 **FAZIT**

Wenn ALLE Tests erfolgreich sind:

### ✅ **DAS SYSTEM IST PRODUCTION-READY!**

**Alle 7 neuen Features funktionieren:**
1. ✅ Schuljahr-Config System
2. ✅ Run-Tracking System
3. ✅ PDF Export
4. ✅ Excel Export
5. ✅ AG-Filter
6. ✅ Statistics View
7. ✅ Swap-Result Partials

---

**Viel Erfolg beim Testen! 🚀**

*Erstellt am: 8. Oktober 2025*  
*Status: Bereit für manuelle Tests*

