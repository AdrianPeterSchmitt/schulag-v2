# 🎓 SchulAG v2

Eine moderne Web-Anwendung zur Verwaltung von Arbeitsgemeinschaften (AGs) an Schulen mit intelligentem Losverfahren zur gerechten Zuteilung.

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.3-red)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8)
![HTMX](https://img.shields.io/badge/HTMX-2.0-blue)
![Status](https://img.shields.io/badge/Status-85%25%20Production%20Ready-green)

---

## ✨ Features

### ✅ Vollständig implementiert:

- **🔐 Authentication & Authorization**
  - Login/Logout mit Session-Management
  - Rollen-basierte Zugriffskontrolle (Admin, Lehrer, Koordinator)
  - CSRF-Protection

- **👥 Verwaltung**
  - Klassen-Verwaltung (CRUD)
  - Schüler-Verwaltung (CRUD)
  - AG-Verwaltung (Backend komplett)

- **📝 AG-Wahleingabe**
  - Lehrer können für ihre Klassen AG-Wünsche eingeben
  - 3 Prioritäten pro Schüler (1., 2., 3. Wunsch)
  - Option "Nimmt nicht teil"
  - HTMX für dynamische Updates

- **🎲 Losverfahren**
  - Intelligenter 3-stufiger Algorithmus
  - Prioritäten-basierte Zuteilung
  - Rest-Warteliste für nicht zugeteilte Schüler
  - Kapazitätsprüfung vor Durchführung

- **📊 Dashboard & Statistiken**
  - Admin-Dashboard mit Übersicht
  - Losverfahren-Dashboard mit Status
  - Klassen-Completion-Status
  - AG-Auslastung

- **🎨 Moderne UI**
  - Responsive Design (Mobile-First)
  - Tailwind CSS für moderne Optik
  - HTMX für AJAX ohne JavaScript-Framework
  - Alpine.js für UI-Interaktionen

### ⏳ In Entwicklung:

- Export-Funktionen (PDF/Excel)
- AG-Verwaltung UI (Backend fertig)
- Tausch-Verwaltung UI
- Tailwind Production-Build

---

## 🚀 Installation

### Voraussetzungen:

- **PHP:** >= 7.4 (empfohlen: 8.0+)
- **MySQL:** >= 5.7 oder MariaDB >= 10.3
- **Apache/Nginx:** mit mod_rewrite
- **Composer:** für Dependency-Management

### Schritt-für-Schritt:

#### 1. Repository klonen

```bash
git clone https://github.com/IHR-USERNAME/schulag-v2.git
cd schulag-v2
```

#### 2. Dependencies installieren

```bash
composer install
```

#### 3. Umgebung konfigurieren

```bash
# .env Datei erstellen
cp env .env

# .env bearbeiten:
# - CI_ENVIRONMENT = development
# - app.baseURL = 'http://localhost/schulag-v2/public/'
# - Datenbank-Zugangsdaten eintragen
```

**Encryption Key generieren:**
```bash
php spark key:generate
```

#### 4. Datenbank einrichten

```bash
# Datenbank erstellen (z.B. schulag_v2)
# Dann Migrationen ausführen:
php spark migrate

# Optional: Testdaten laden
php spark db:seed TestDataSeeder
```

#### 5. Server starten

**XAMPP/LAMP:**
- Document Root auf `/public` setzen
- Zugriff via: `http://localhost/schulag-v2/public/`

**Built-in Server (Development):**
```bash
php spark serve
```
Zugriff via: `http://localhost:8080`

---

## 👤 Standard-Zugangsdaten

Nach dem Seeder:

```
Email: admin@schulag.test
Passwort: admin123
```

**⚠️ Wichtig:** Ändern Sie das Passwort nach dem ersten Login!

---

## 🏗️ Projekt-Struktur

```
schulag-v2/
├── app/
│   ├── Controllers/        # 4 Controller (Admin, Klassen, Allocation, Auth)
│   ├── Models/             # 8 Models
│   ├── Services/           # AllocationService (Losverfahren)
│   ├── Filters/            # Auth-Filter
│   ├── Database/
│   │   ├── Migrations/     # 8 Tabellen
│   │   └── Seeds/          # TestDataSeeder
│   └── Views/              # 15+ Views (Blade-ähnlich + HTMX)
├── public/                 # Document Root
│   └── index.php           # Entry Point
├── writable/               # Logs, Cache, Sessions
├── vendor/                 # Composer Dependencies
└── .env                    # Konfiguration (nicht in Git!)
```

---

## 🔧 Technologien

### Backend:
- **CodeIgniter 4.6.3** - Modernes PHP-Framework
- **PHP 7.4+** - Broad compatibility
- **MySQL/MariaDB** - Datenbank

### Frontend:
- **Tailwind CSS** - Utility-First CSS Framework
- **HTMX 2.0** - AJAX ohne JavaScript-Framework
- **Alpine.js** - Minimalistisches JavaScript-Framework

### Development:
- **Composer** - Dependency Management
- **PHPStan** - Static Analysis (Level 6)

---

## 📚 Dokumentation

- **Installation:** Siehe oben
- **Deployment:** Shared-Hosting-Anleitung in `/docs` (falls vorhanden)
- **API/Routes:** Siehe `app/Config/Routes.php`
- **Datenbank:** Siehe `app/Database/Migrations/`

---

## 🎯 Rollen & Berechtigungen

| Rolle | Berechtigung |
|-------|--------------|
| **Admin** | Voller Zugriff (Verwaltung, Klassen, Losverfahren) |
| **Lehrer** | AG-Wahleingabe für eigene Klassen |
| **Koordinator** | Losverfahren durchführen, Ergebnisse sehen |

---

## 🧪 Testing

### Browser-Tests:
```bash
# Manuelle Tests durchgeführt
# Siehe TEST-RESULTS.md (falls vorhanden)
```

### PHPStan (Code-Qualität):
```bash
composer analyse
```

---

## 📦 Deployment (Shared Hosting)

### Kurzanleitung:

1. **Dateien hochladen** (via FTP/SFTP)
2. **.env konfigurieren** (Production-Modus)
3. **Document Root auf `/public` setzen**
4. **Migrationen ausführen** (via Browser-Script oder SSH)
5. **Admin-User anlegen**

Detaillierte Anleitung: Siehe Dokumentation im Wiki/Docs-Ordner

---

## 🤝 Mitarbeit

Contributions sind willkommen! Bitte:

1. Fork das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/AmazingFeature`)
3. Commit deine Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Push zum Branch (`git push origin feature/AmazingFeature`)
5. Erstelle einen Pull Request

---

## 📝 Lizenz

Dieses Projekt steht unter der [MIT License](LICENSE).

---

## 🙏 Credits

- **Framework:** [CodeIgniter 4](https://codeigniter.com)
- **CSS:** [Tailwind CSS](https://tailwindcss.com)
- **HTMX:** [htmx.org](https://htmx.org)
- **Alpine.js:** [alpinejs.dev](https://alpinejs.dev)

---

## 📊 Status

**Aktueller Stand:** 85% Production-Ready

- ✅ Backend komplett (100%)
- ✅ Frontend (95%)
- ✅ Authentication & Security (90%)
- ✅ Kern-Features (85%)
- ⏳ Export-Funktionen (20%)
- ⏳ AG-Verwaltung UI (50%)

**Nächste Schritte:**
- Export-Funktionen (PDF/Excel) fertigstellen
- AG-Verwaltung UI vervollständigen
- Tailwind Production-Build

---

## 📧 Kontakt

Bei Fragen oder Problemen erstellen Sie bitte ein Issue im Repository.

---

**Version:** 2.0.0-beta  
**Letzte Aktualisierung:** Oktober 2025
