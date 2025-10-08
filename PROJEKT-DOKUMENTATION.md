# 📚 SchulAG v2 - Projekt-Dokumentation

**Version:** 2.0  
**Framework:** CodeIgniter 4.6.3  
**Status:** ✅ PRODUKTIONSREIF  
**Letzte Aktualisierung:** 08.10.2025

---

## 📖 **INHALTSVERZEICHNIS**

1. [Übersicht](#übersicht)
2. [Installation](#installation)
3. [Konfiguration](#konfiguration)
4. [Features](#features)
5. [Deployment](#deployment)
6. [Wartung](#wartung)
7. [Technologie-Stack](#technologie-stack)

---

## 🎯 **ÜBERSICHT**

SchulAG v2 ist ein umfassendes Verwaltungssystem für die AG-Zuteilung an Schulen. Das System ermöglicht:

- **Klassen-Verwaltung:** Klassen und Schüler verwalten
- **AG-Verwaltung:** Arbeitsgemeinschaften konfigurieren
- **Wahlsystem:** Schüler wählen ihre Wunsch-AGs
- **Automatisches Losverfahren:** Gerechte Zuteilung per Algorithmus
- **Manuelle Tauschfunktion:** Nachträgliche Anpassungen
- **Statistiken & Export:** PDF/Excel-Reports

---

## 🚀 **INSTALLATION**

### **Voraussetzungen:**
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Apache mit mod_rewrite
- Composer

### **Schritt 1: Repository klonen**
```bash
git clone https://github.com/AdrianPeterSchmitt/schulag-v2.git
cd schulag-v2
```

### **Schritt 2: Dependencies installieren**
```bash
composer install
```

### **Schritt 3: Datenbank erstellen**
```sql
CREATE DATABASE schulag CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Schritt 4: Umgebung konfigurieren**
```bash
cp env .env
```

Bearbeite `.env`:
```ini
app.baseURL = 'http://localhost/schulag-v2/public/'
database.default.hostname = localhost
database.default.database = schulag
database.default.username = root
database.default.password = 
```

### **Schritt 5: Migrations ausführen**
```bash
php spark migrate
```

### **Schritt 6: Test-Daten laden (optional)**
```bash
php spark db:seed TestDataSeeder
```

### **Schritt 7: Apache konfigurieren**

Bearbeite `public/.htaccess`:
```apache
RewriteBase /schulag-v2/public/
```

---

## ⚙️ **KONFIGURATION**

### **Schuljahr-Einstellungen**
**Datei:** `app/Config/SchulAG.php`

```php
// Aktuelles Schuljahr (manuell)
public string $currentSchoolyear = '2024/2025';

// Automatische Berechnung (optional)
public bool $autoCalculateSchoolyear = false;

// Schuljahr-Zeitraum
public string $schoolyearStartDate = '08-01'; // 1. August
public string $schoolyearEndDate = '07-31';   // 31. Juli
```

### **AG-Regeln**
```php
// Minimum Teilnehmerzahl
public int $minTeilnehmer = 6;

// Maximum für Einzelbetreuung
public int $maxTeilnehmerEinzel = 11;

// Minimum für zweite Lehrkraft
public int $minTeilnehmerZweiteLehrkraft = 12;
```

### **Wahlsystem**
```php
// Anzahl Wahlprioritäten
public int $numberOfChoices = 3;
```

---

## 🎨 **FEATURES**

### **1. Verwaltung (Admin)**
- ✅ **Klassen:** Anlegen, Bearbeiten, Löschen
- ✅ **Schüler:** Pro Klasse verwalten
- ✅ **AGs:** Arbeitsgemeinschaften konfigurieren
- ✅ **Dashboard:** Übersicht aller Daten

### **2. Lehrer-Bereich**
- ✅ **Klassenauswahl:** Eigene Klassen verwalten
- ✅ **Schüler-Wahlen:** Wahlen einsehen
- ✅ **AG-Filter:** Nur erlaubte AGs für Schüler

### **3. Losverfahren (Koordinator)**
- ✅ **Automatische Zuteilung:** Algorithmus-basiert
- ✅ **Kapazitäts-Check:** Validierung vor Start
- ✅ **Ergebnisse:** Übersichtliche Darstellung
- ✅ **Tauschfunktion:** Manuelle Anpassungen
- ✅ **Statistiken:** Detaillierte Auswertungen

### **4. Export & Reports**
- ✅ **PDF-Export:** Zuteilungslisten
- ✅ **Excel-Export:** Tabellarische Übersicht
- ✅ **Statistiken:** Grafische Auswertungen

---

## 🛠️ **TECHNOLOGIE-STACK**

### **Backend:**
- **CodeIgniter 4.6.3** - PHP Full-Stack Framework
- **MySQL/MariaDB** - Datenbank
- **PHPStan Level 6** - Static Analysis

### **Frontend:**
- **Tailwind CSS** - Utility-First CSS Framework
- **HTMX 1.9.10** - Modern JavaScript-less Interactivity
- **Alpine.js 3.x** - Minimal JavaScript Framework

### **Libraries:**
- **Dompdf** - PDF-Generierung
- **PhpSpreadsheet** - Excel-Export
- **Faker** - Test-Daten-Generierung (Dev)

---

## 🚢 **DEPLOYMENT**

### **Produktiv-Server:**

1. **Code deployen**
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
```

2. **Umgebung auf Production setzen**
```ini
# In .env
CI_ENVIRONMENT = production
```

3. **Migrations ausführen**
```bash
php spark migrate
```

4. **Permissions setzen**
```bash
chmod -R 755 writable/
```

5. **Apache Virtual Host**
```apache
<VirtualHost *:80>
    ServerName schulag.example.com
    DocumentRoot /var/www/schulag-v2/public
    
    <Directory /var/www/schulag-v2/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 🔧 **WARTUNG**

### **Schuljahr wechseln**
1. Öffne `app/Config/SchulAG.php`
2. Ändere `$currentSchoolyear` (z.B. `'2025/2026'`)
3. Optional: Alte Daten archivieren

### **Neue AGs anlegen**
1. Login als Admin
2. Navigation: Admin → AGs verwalten
3. Button "Neue AG" → Formular ausfüllen

### **Losverfahren durchführen**
1. Login als Koordinator
2. Navigation: Losverfahren
3. Sicherstellen: Alle Klassen haben Wahlen abgegeben
4. Button "Losverfahren starten"

### **Backup erstellen**
```bash
# Datenbank
mysqldump -u root schulag > backup_$(date +%Y%m%d).sql

# Dateien
tar -czf backup_files_$(date +%Y%m%d).tar.gz writable/ .env
```

---

## 👥 **STANDARD-BENUTZER**

### **Admin (Development)**
- **Email:** admin@schulag.test
- **Passwort:** password123
- **Rolle:** ADMIN

### **Lehrer (Development)**
- **Email:** lehrer@schulag.test
- **Passwort:** password123
- **Rolle:** TEACHER

⚠️ **WICHTIG:** Passwörter vor Produktiv-Einsatz ändern!

---

## 📊 **DATENBANK-STRUKTUR**

### **Haupttabellen:**
- `users` - Benutzer (Admin, Lehrer, Koordinatoren)
- `klassen` - Schulklassen
- `schueler` - Schüler
- `clubs` - Arbeitsgemeinschaften
- `club_offers` - AG-Angebote pro Schuljahr
- `choices` - Schüler-Wahlen
- `allocations` - Zuteilungen
- `manual_swaps` - Manuelle Tausche
- `allocation_runs` - Losverfahren-Historie
- `ci_sessions` - Session-Daten

---

## 🔐 **SICHERHEIT**

### **Implementierte Maßnahmen:**
- ✅ CSRF-Protection (CodeIgniter)
- ✅ Password-Hashing (bcrypt)
- ✅ Session-Verwaltung (Database)
- ✅ SQL-Injection-Schutz (Query Builder)
- ✅ XSS-Protection (esc() Helper)
- ✅ Role-Based Access Control

---

## 🧪 **TESTING**

### **PHPStan (Static Analysis)**
```bash
php vendor/bin/phpstan analyse
```

### **PHPUnit (Unit Tests)**
```bash
php vendor/bin/phpunit
```

### **Browser-Tests**
Siehe: `VOLLSTAENDIGER-BROWSER-TEST-BERICHT.md`

---

## 📝 **CHANGELOG**

### **Version 2.0 (08.10.2025)**
- ✅ Vollständige Neuimplementierung in CodeIgniter 4
- ✅ Moderne UI mit Tailwind CSS
- ✅ HTMX-Integration für dynamische Updates
- ✅ Alpine.js-Modals
- ✅ Database-Sessions
- ✅ PHPStan Level 6 Compliance
- ✅ Schuljahr-Konfiguration
- ✅ AllocationRun-Tracking
- ✅ PDF/Excel-Export

---

## 🤝 **SUPPORT**

### **Dokumentation:**
- `README.md` - Projekt-Übersicht
- `DEPLOYMENT-GUIDE.md` - Deployment-Anleitung
- `GITHUB-SETUP.md` - GitHub-Integration

### **Reports:**
- `FINALE-FEHLER-BEHEBUNG-BERICHT.md` - Letzte Fixes
- `VOLLSTAENDIGER-BROWSER-TEST-BERICHT.md` - Test-Ergebnisse

---

## 📦 **PROJEKT-STRUKTUR**

```
schulag-v2/
├── app/
│   ├── Controllers/      # MVC Controller
│   ├── Models/          # Datenbank-Models
│   ├── Services/        # Business-Logic
│   ├── Views/           # Templates
│   ├── Config/          # Konfiguration
│   ├── Filters/         # Auth-Filter
│   └── Helpers/         # Helper-Funktionen
├── public/              # Web-Root
│   └── index.php        # Front-Controller
├── writable/            # Logs, Cache, Sessions
├── tests/               # Unit-Tests
├── vendor/              # Composer Dependencies
├── .env                 # Umgebungsvariablen
├── composer.json        # PHP Dependencies
├── phpstan.neon         # Static Analysis Config
└── spark                # CLI-Tool
```

---

## 🎓 **VERWENDETE KONZEPTE**

### **Design Patterns:**
- **MVC** (Model-View-Controller)
- **Repository Pattern** (Models)
- **Service Layer** (AllocationService)
- **Dependency Injection**

### **Best Practices:**
- Type-Hints (PHP 8.1+)
- PHPDoc-Kommentare
- SOLID-Prinzipien
- DRY (Don't Repeat Yourself)
- Separation of Concerns

---

## 📞 **KONTAKT**

**Repository:** https://github.com/AdrianPeterSchmitt/schulag-v2  
**Framework:** https://codeigniter.com/  
**Lizenz:** MIT

---

**🎉 VIEL ERFOLG MIT SCHULAG V2!** 🚀

