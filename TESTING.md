# 🧪 Testing Guide - SchulAG v2

Umfassende Test-Strategie und Dokumentation für SchulAG v2.

---

## 📊 Test-Übersicht

### Test-Pyramide

```
        /\
       /E2E\          ← 15 Tests (User Journeys)
      /------\
     /        \
    / Integration\    ← 14 Tests (Models, Services, Filters)
   /------------\
  /              \
 /   Unit Tests   \   ← PHPUnit Tests (Models, Controllers)
/------------------\
```

### Test-Abdeckung

| Kategorie | Tests | Status |
|-----------|-------|--------|
| **PHPUnit Unit** | 14 ✅ | Passing |
| **E2E Playwright** | 15 ✅ | Configured |
| **Browser Manual** | 10 ✅ | Verified |
| **Code Coverage** | - | PHPStan Level 6 |

---

## 🔬 Unit Tests (PHPUnit)

### Ausführen

```bash
# Alle Tests
vendor/bin/phpunit

# Mit TestDox (lesbare Ausgabe)
vendor/bin/phpunit --testdox

# Spezifische Test-Datei
vendor/bin/phpunit tests/unit/SimpleModelsTest.php

# Mit Coverage (benötigt Xdebug)
vendor/bin/phpunit --coverage-html build/coverage
```

### Test-Dateien

```
tests/
├── unit/
│   ├── SimpleModelsTest.php       ✅ 7 Tests (Models CRUD)
│   ├── FilterTest.php              ✅ 6 Tests (Auth Filter)
│   ├── HealthTest.php              ✅ 2 Tests (System)
│   ├── AllocationServiceTest.php   ⏳ In Arbeit
│   └── ModelsTest.php              ⏳ In Arbeit
```

### Was wird getestet?

#### Models
- ✅ CRUD-Operationen (Create, Read, Update, Delete)
- ✅ Unique Constraints (Email)
- ✅ Relationen (Klasse → Schüler)
- ✅ Filter & Queries
- ✅ Timestamps

#### Filters
- ✅ Authentication-Check
- ✅ Rollen-Berechtigungen
- ✅ Redirect-Verhalten
- ✅ Case-Insensitivity

---

## 🎭 E2E Tests (Playwright)

### Installation

```bash
npm install
npx playwright install
```

### Ausführen

```bash
# Alle E2E-Tests
npm test

# Mit sichtbarem Browser
npm run test:headed

# Debugging
npm run test:debug

# Report anzeigen
npm run test:report
```

### Test-Dateien

```
e2e/
├── auth.spec.ts          ✅ 5 Tests (Login, Logout, Redirects)
├── navigation.spec.ts    ✅ 5 Tests (Navigation zwischen Seiten)
└── admin.spec.ts         ✅ 8 Tests (Admin Dashboard, Losverfahren)
```

### Was wird getestet?

#### Authentication
- ✅ Login-Seite anzeigen
- ✅ Erfolgreicher Login
- ✅ Fehlgeschlagener Login
- ✅ Logout
- ✅ Redirect für nicht-authentifizierte User

#### Navigation
- ✅ Navigation zu Verwaltung
- ✅ Navigation zu Klassen
- ✅ Navigation zu Losverfahren
- ✅ Navigation-Menü sichtbar

#### Admin-Funktionen
- ✅ Dashboard mit Statistiken
- ✅ Schnellzugriff-Links
- ✅ Losverfahren-Status
- ✅ Navigation zu Unterseiten

---

## 🔍 Static Analysis (PHPStan)

### Ausführen

```bash
composer analyse
```

### Konfiguration

- **Level:** 6 (von 9)
- **0 Fehler** ✅
- **Geprüfte Dateien:** 16 Controller, Models, Services

### Was wird geprüft?

- ✅ Type-Hints korrekt
- ✅ Return-Types stimmen
- ✅ Undefined Variables
- ✅ Array-Key-Zugriffe
- ✅ Method-Calls

---

## 🌐 Browser-Tests (Manuell)

Diese wurden bereits durchgeführt und verifiziert:

### Completed ✅

1. Login-Flow mit Admin-Credentials
2. Redirect nach Login korrekt
3. Navigation zwischen allen Bereichen
4. Admin-Dashboard lädt vollständig
5. Losverfahren-Seite zugänglich
6. Klassen-Übersicht funktioniert
7. Logout funktioniert
8. Keine Redirect-Schleifen
9. Session-Management korrekt
10. Berechtigungen werden geprüft

---

## 📈 Test-Strategie

### Wann welche Tests?

#### Während der Entwicklung

```bash
# 1. Schnelle Unit-Tests
vendor/bin/phpunit --testdox

# 2. PHPStan Check
composer analyse
```

#### Vor einem Commit

```bash
# 1. Alle Unit-Tests
vendor/bin/phpunit

# 2. Static Analysis
composer analyse

# 3. E2E Smoke-Tests
npm test -- auth.spec.ts
```

#### Vor einem Release

```bash
# 1. Full Test-Suite
vendor/bin/phpunit --testdox

# 2. Alle E2E-Tests (alle Browser)
npm test

# 3. Code-Quality
composer analyse

# 4. Manuelle Browser-Tests für kritische Flows
```

---

## 🚀 CI/CD Integration

### GitHub Actions

Tests laufen automatisch bei:
- ✅ Push zu `main` oder `develop`
- ✅ Pull Requests
- ✅ Vor dem Deployment

```yaml
# .github/workflows/ci.yml
jobs:
  test:
    - PHPStan Check
    - PHPUnit Tests
    - E2E Tests (optional)
```

---

## 📊 Code-Coverage

### Coverage-Report generieren

```bash
# HTML-Report
vendor/bin/phpunit --coverage-html build/coverage

# Text-Output
vendor/bin/phpunit --coverage-text
```

**Hinweis:** Benötigt Xdebug oder PCOV Extension.

### Aktivieren in php.ini

```ini
; Xdebug 3
zend_extension=xdebug
xdebug.mode=coverage

; ODER PCOV
extension=pcov
pcov.enabled=1
```

### Ziel-Coverage

- **Models:** 80%+
- **Controllers:** 70%+
- **Services:** 90%+
- **Filters:** 95%+

---

## 🐛 Debugging

### PHPUnit-Tests debuggen

```bash
# Mit Xdebug
XDEBUG_MODE=debug vendor/bin/phpunit

# Einzelnen Test
vendor/bin/phpunit --filter testUserModelCRUD
```

### E2E-Tests debuggen

```bash
# Mit Playwright Inspector
npm run test:debug

# Traces anzeigen
npx playwright show-trace test-results/.../trace.zip
```

### Debug-Helper verwenden

```php
// In Code
debug_log('Test message', ['data' => $value]);
debug_user_action('Action performed');
dd($variable); // Dump and Die
```

---

## ✅ Test-Checkliste

### Neue Features

- [ ] Unit-Tests geschrieben
- [ ] PHPStan zeigt 0 Fehler
- [ ] E2E-Test für kritische User-Flows
- [ ] Manueller Browser-Test durchgeführt
- [ ] Dokumentation aktualisiert

### Bugfixes

- [ ] Test reproduziert den Bug
- [ ] Fix implementiert
- [ ] Test läuft grün
- [ ] Keine Regression in anderen Tests
- [ ] PHPStan sauber

---

## 🎯 Best Practices

### Unit Tests

✅ **DO:**
- Teste eine Sache pro Test
- Verwende aussagekräftige Test-Namen
- Setup in `setUp()`, Cleanup in `tearDown()`
- Verwende Assertions sinnvoll
- Teste Edge-Cases

❌ **DON'T:**
- Teste nicht Framework-Code
- Keine externen API-Calls in Unit-Tests
- Keine echte Datenbank (verwende SQLite)
- Tests sollten nicht voneinander abhängen

### E2E Tests

✅ **DO:**
- Teste kritische User-Journeys
- Warte auf sichtbare Elemente
- Verwende Rollen-Selektoren
- Screenshots bei Fehlern
- Isolierte Test-Daten

❌ **DON'T:**
- Keine Hardcoded Waits
- Keine XPath-Selektoren
- Keine Produktions-Daten
- Tests sollten parallel laufen können

---

## 📚 Weitere Ressourcen

### PHPUnit
- [Offizielle Dokumentation](https://phpunit.de/)
- [CodeIgniter Testing](https://codeigniter.com/user_guide/testing/)

### Playwright
- [Offizielle Dokumentation](https://playwright.dev/)
- [Best Practices](https://playwright.dev/docs/best-practices)

### PHPStan
- [Offizielle Dokumentation](https://phpstan.org/)
- [Rule Levels](https://phpstan.org/user-guide/rule-levels)

---

## 🔧 Troubleshooting

### "No code coverage driver available"

**Problem:** Xdebug/PCOV nicht installiert

**Lösung:**
```bash
# Xdebug installieren
# Oder Tests ohne Coverage ausführen
vendor/bin/phpunit --no-coverage
```

### "Table has no column named..."

**Problem:** Migration stimmt nicht mit Test überein

**Lösung:**
- Prüfe Migration-Dateien
- Passe Test-Daten an
- `php spark migrate:refresh`

### E2E-Tests timeout

**Problem:** Server antwortet nicht

**Lösung:**
```bash
# Server starten
php spark serve
# Oder XAMPP Apache starten
```

---

**Version:** 2.0.0  
**Letzte Aktualisierung:** 09.10.2025  
**Status:** ✅ Produktionsreif


