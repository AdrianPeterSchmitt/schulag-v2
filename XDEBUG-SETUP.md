# 🐛 Xdebug Installation für Code-Coverage

## PHP-Info

- **PHP Version:** 8.2.12
- **Thread Safety:** Enabled (ZTS)
- **Architecture:** x64
- **Compiler:** Visual C++ 2019

---

## 📥 Download

### Option 1: Automatisch (empfohlen)

Besuche: https://xdebug.org/wizard

1. Führe aus: `php -i > phpinfo.txt`
2. Kopiere den Inhalt von `phpinfo.txt`
3. Füge ihn in den Wizard ein
4. Folge den Anweisungen

### Option 2: Manuell

**Für PHP 8.2 TS x64:**

1. Download von: https://xdebug.org/download
2. Datei: `php_xdebug-3.3.2-8.2-vs16-x86_64.dll` (oder neuer)
3. Speichere als: `C:\xampp\php\ext\php_xdebug.dll`

---

## ⚙️ Konfiguration

### 1. php.ini bearbeiten

Füge am Ende von `C:\xampp\php\php.ini` hinzu:

```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage,debug,develop
xdebug.start_with_request=trigger
xdebug.client_host=localhost
xdebug.client_port=9003
```

### 2. Apache neu starten

```bash
# XAMPP Control Panel:
# Apache -> Stop -> Start
```

### 3. Prüfen

```bash
php -v
# Sollte zeigen:
# PHP 8.2.12 (cli) ...
# with Xdebug v3.3.x ...
```

---

## 🧪 Code-Coverage ausführen

### HTML-Report

```bash
vendor/bin/phpunit --coverage-html build/coverage
```

Öffne dann: `build/coverage/index.html`

### Text-Output

```bash
vendor/bin/phpunit --coverage-text
```

### Filter einzelne Dateien

```bash
vendor/bin/phpunit --coverage-html build/coverage --filter SimpleModelsTest
```

---

## 📊 Coverage-Reports

### Was wird gemessen?

- **Line Coverage:** Welche Zeilen wurden ausgeführt
- **Function Coverage:** Welche Funktionen wurden getestet
- **Class Coverage:** Welche Klassen wurden getestet
- **Branch Coverage:** Welche Verzweigungen wurden getestet

### Ziele

| Kategorie | Ziel | Aktuell |
|-----------|------|---------|
| Models | 80%+ | ⏳ TBD |
| Controllers | 70%+ | ⏳ TBD |
| Services | 90%+ | ⏳ TBD |
| Filters | 95%+ | ⏳ TBD |

---

## 🚀 Alternative: PCOV (schneller)

PCOV ist leichter und schneller als Xdebug für Coverage.

### Installation

```bash
# Über PECL (wenn verfügbar)
pecl install pcov

# Oder DLL herunterladen von:
# https://windows.php.net/downloads/pecl/releases/pcov/
```

### Config in php.ini

```ini
[pcov]
extension=pcov
pcov.enabled=1
pcov.directory=.
```

---

## 🔧 Troubleshooting

### Xdebug lädt nicht

```bash
# Prüfen
php -v

# Sollte zeigen:
# with Xdebug v3.x
```

Wenn nicht:
1. Pfad zu `php_xdebug.dll` prüfen
2. `zend_extension` (nicht `extension`)
3. Apache neu starten

### Coverage-Report leer

```bash
# Prüfe Xdebug-Mode
php -i | grep xdebug.mode

# Sollte sein: coverage
```

### Zu langsam

PCOV verwenden statt Xdebug (nur für Coverage, nicht Debugging)

---

## 📖 Ressourcen

- [Xdebug Download](https://xdebug.org/download)
- [Xdebug Wizard](https://xdebug.org/wizard)
- [PCOV](https://github.com/krakjoe/pcov)
- [PHPUnit Coverage](https://phpunit.de/manual/current/en/code-coverage-analysis.html)

---

## 🎯 Nächste Schritte

1. Xdebug installieren (siehe oben)
2. Apache neu starten
3. Coverage-Report generieren:
   ```bash
   vendor/bin/phpunit --coverage-html build/coverage
   ```
4. Report öffnen: `build/coverage/index.html`
5. Coverage verbessern wo nötig

---

**Version:** 1.0.0  
**Letzte Aktualisierung:** 09.10.2025

