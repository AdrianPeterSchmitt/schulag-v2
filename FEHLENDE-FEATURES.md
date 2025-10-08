# Fehlende Features und Methoden - Übersicht

## 🎯 Zusammenfassung

Aktueller Stand: **85% Production-Ready**

---

## 📋 Kategorien

### 1. **Kritisch (Must-Have für MVP)** 🔴
- [ ] Run-Tracking System für Losverfahren
- [ ] Config-Klasse für Schuljahr

### 2. **Wichtig (Should-Have)** 🟡
- [ ] PDF Export
- [ ] Excel Export  
- [ ] AG-Filter nach Schüler-Eignung

### 3. **Nice-to-Have** 🟢
- [ ] Statistics View
- [ ] Swap-Result Partials

---

## 🔴 **KRITISCH**

### 1.1 Run-Tracking System für Losverfahren

**Aktuell:** Simulierte Methoden in `AllocationModel.php`

**Fehlende Implementierungen:**
```php
// AllocationModel.php - Zeile 147-172
getLatestRun()        // Gibt aktuell null zurück
getRunWithResults()   // Gibt aktuell leeres Array zurück  
getRecentRuns()       // Gibt aktuell leeres Array zurück
```

**Was benötigt wird:**
1. **Neue Datenbank-Tabelle:** `allocation_runs`
   - `id` (PK)
   - `schoolyear` (VARCHAR)
   - `run_date` (DATETIME)
   - `total_students` (INT)
   - `total_assigned` (INT)
   - `total_waitlist` (INT)
   - `total_rest_waitlist` (INT)
   - `created_by` (INT, FK zu users)
   - `created_at`, `updated_at`

2. **Neues Model:** `AllocationRunModel.php`

3. **Integration in AllocationService:**
   - Bei jedem Losverfahren-Durchlauf einen Run speichern
   - Ergebnisse mit Run verknüpfen

**Nutzen:**
- ✅ Historie aller Durchläufe
- ✅ Vergleich verschiedener Ergebnisse
- ✅ Rollback-Möglichkeit
- ✅ Audit-Trail

---

### 1.2 Config-Klasse für Schuljahr

**Aktuell:** Hardcodiert in mehreren Controllern

**Betroffene Stellen:**
```php
// Allocation.php - Zeile 41
$schoolyear = '2024/2025'; // TODO: Aus Config holen

// Klassen.php - Zeile 65
$schoolyear = '2024/2025'; // TODO: Aus Config holen
```

**Was benötigt wird:**
1. **Neue Config-Datei:** `app/Config/SchulAG.php`
2. **Helper-Funktion:** `getCurrentSchoolyear()`

**Nutzen:**
- ✅ Zentrale Verwaltung
- ✅ Einfaches Schuljahr-Wechsel
- ✅ Keine hartcodierten Werte

---

## 🟡 **WICHTIG**

### 2.1 PDF Export

**Aktuell:** Stub-Implementierung

**Betroffene Datei:** `Allocation.php - Zeile 357-364`

**Was exportiert werden soll:**
- Liste aller Schüler mit Zuteilungen
- Gruppiert nach AGs
- Gruppiert nach Klassen
- Wartelisten

**Technologie:**
- ✅ **Dompdf** ist bereits installiert (`vendor/dompdf`)

**Nutzen:**
- ✅ Druckbare Listen für Lehrkräfte
- ✅ Archivierung
- ✅ Offizieller Nachweis

---

### 2.2 Excel Export

**Aktuell:** Stub-Implementierung

**Betroffene Datei:** `Allocation.php - Zeile 372-379`

**Was exportiert werden soll:**
- Alle Zuteilungen in Tabellenform
- Filterbar und sortierbar
- Import in andere Systeme möglich

**Technologie:**
- ✅ **PhpSpreadsheet** ist bereits installiert (`vendor/phpoffice`)

**Nutzen:**
- ✅ Weiterverarbeitung in Excel
- ✅ Statistiken erstellen
- ✅ Datenanalyse

---

### 2.3 AG-Filter nach Schüler-Eignung

**Aktuell:** Auskommentiert, alle AGs werden angezeigt

**Betroffene Datei:** `Klassen.php - Zeile 92-97`

```php
// Aktuell:
$schueler['available_offers'] = $offers; // Temporär: Alle AGs verfügbar

// Soll:
foreach ($offers as $offer) {
    if ($this->clubModel->isAllowedForStudent($offer['club_id'], $schueler['id'])) {
        $schueler['available_offers'][] = $offer;
    }
}
```

**Was geprüft werden muss:**
- Jahrgangsstufe (min_grade, max_grade)
- Förderschwerpunkt (G/LE - allowed_types_gl)

**Nutzen:**
- ✅ Nur passende AGs anzeigen
- ✅ Verhindert falsche Wahlen
- ✅ Bessere UX

---

## 🟢 **NICE-TO-HAVE**

### 3.1 Statistics View

**Aktuell:** Controller-Methode existiert, View fehlt

**Betroffene Datei:** `Allocation.php - Zeile 386-398`

**Was fehlt:**
- View-Datei: `app/Views/allocation/statistics.php`

**Was angezeigt werden soll:**
- Gesamtübersicht aller AGs
- Auslastung pro AG
- Wartelisten
- Verteilung nach Jahrgängen
- Grafiken (optional)

---

### 3.2 Swap-Result Partials

**Aktuell:** Verwendet in `performSwap()`, View fehlt möglicherweise

**Betroffene Datei:** `Allocation.php - Zeile 293, 302, 312`

**Was fehlt:**
- View-Datei: `app/Views/allocation/partials/swap_result.php`

**Was angezeigt werden soll:**
- Success/Error Nachricht
- Aktualisierte Zuteilungen
- HTMX-kompatibles Partial

---

## 📊 Prioritäten-Matrix

| Feature | Priorität | Aufwand | Impact | Status |
|---------|-----------|---------|--------|--------|
| **Run-Tracking** | 🔴 Hoch | 3-4h | Hoch | ❌ Fehlt |
| **Schuljahr-Config** | 🔴 Hoch | 30min | Mittel | ❌ Fehlt |
| **PDF Export** | 🟡 Mittel | 2-3h | Hoch | ❌ Fehlt |
| **Excel Export** | 🟡 Mittel | 2h | Hoch | ❌ Fehlt |
| **AG-Filter** | 🟡 Mittel | 15min | Hoch | ⚠️ Auskommentiert |
| **Statistics View** | 🟢 Niedrig | 1h | Mittel | ❌ Fehlt |
| **Swap Partials** | 🟢 Niedrig | 30min | Niedrig | ❌ Fehlt |

**Gesamtaufwand:** Ca. 9-11 Stunden

---

## 🎯 Empfohlene Reihenfolge

### **Phase 1: Quick Wins** (1 Stunde)
1. ✅ Schuljahr-Config (30min)
2. ✅ AG-Filter aktivieren (15min)
3. ✅ Swap Partials erstellen (15min)

### **Phase 2: Core Features** (4-6 Stunden)
4. ✅ Run-Tracking System (3-4h)
5. ✅ PDF Export (2-3h)

### **Phase 3: Erweitert** (3-4 Stunden)
6. ✅ Excel Export (2h)
7. ✅ Statistics View (1h)
8. ✅ Testing & Bug Fixes (1h)

---

## ❓ Ihre Entscheidung

**Was möchten Sie angehen?**

A) **Quick Wins** - Die 3 schnellsten Features (1 Stunde)
B) **Run-Tracking** - Das wichtigste fehlende Feature
C) **Export-Funktionen** - PDF und Excel komplett
D) **Alles nacheinander** - Alle Features implementieren
E) **Custom** - Sie wählen einzelne Features aus

Was soll ich implementieren?

