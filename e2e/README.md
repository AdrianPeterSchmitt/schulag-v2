# 🎭 E2E Tests mit Playwright

End-to-End-Tests für SchulAG v2 mit Playwright.

## 📋 Übersicht

Die E2E-Tests decken folgende Bereiche ab:

### 1. **Authentication** (`auth.spec.ts`)
- ✅ Login-Seite anzeigen
- ✅ Erfolgreicher Login als Admin
- ✅ Login mit falschen Credentials fehlschlägt
- ✅ Logout funktioniert
- ✅ Nicht-authentifizierte User werden umgeleitet

### 2. **Navigation** (`navigation.spec.ts`)
- ✅ Navigation zu Verwaltung
- ✅ Navigation zu Klassen
- ✅ Navigation zu Losverfahren
- ✅ Navigation-Menü wird angezeigt

### 3. **Admin Dashboard** (`admin.spec.ts`)
- ✅ Dashboard mit Statistiken
- ✅ Schnellzugriff-Sektion
- ✅ Navigation zu Verwaltungsbereichen
- ✅ Losverfahren-Dashboard
- ✅ Losverfahren-Button Status
- ✅ Navigation zu Ergebnissen

---

## 🚀 Installation

```bash
# Node.js Dependencies installieren
npm install
```

---

## 🧪 Tests ausführen

### Alle Tests

```bash
npm test
```

### Tests mit sichtbarem Browser

```bash
npm run test:headed
```

### Tests debuggen

```bash
npm run test:debug
```

### Test-Report anzeigen

```bash
npm run test:report
```

---

## ⚙️ Konfiguration

### Base URL ändern

In `playwright.config.ts`:

```typescript
use: {
  baseURL: 'http://localhost/schulag-v2/public',
  // oder für Development-Server:
  // baseURL: 'http://localhost:8080',
}
```

### Browser auswählen

```bash
# Nur Chrome
npx playwright test --project=chromium

# Nur Firefox
npx playwright test --project=firefox

# Nur Safari
npx playwright test --project=webkit
```

---

## 📸 Screenshots & Videos

Bei fehlgeschlagenen Tests werden automatisch:
- **Screenshots** erstellt
- **Traces** aufgezeichnet (für Debugging)

Speicherort: `test-results/`

---

## 🎯 Test-Strategie

### Critical User Journeys

1. **Happy Path - Admin**
   - Login → Dashboard → Verwaltung → Logout

2. **Klassen-Verwaltung**
   - Login → Klassen → Klasse auswählen

3. **Losverfahren**
   - Login → Losverfahren → Status prüfen

### Erweiterte Tests (TODO)

- ❌ Klasse erstellen/bearbeiten/löschen
- ❌ Schüler hinzufügen/bearbeiten
- ❌ AG erstellen/bearbeiten
- ❌ AG-Wahlen eingeben
- ❌ Losverfahren durchführen
- ❌ Tausch durchführen
- ❌ Export-Funktionen

---

## 🔧 Debugging

### Test im Debug-Modus ausführen

```bash
npm run test:debug e2e/auth.spec.ts
```

### Playwright Inspector

```bash
npx playwright test --debug
```

### Traces anzeigen

```bash
npx playwright show-trace test-results/traces/trace.zip
```

---

## 📊 Coverage

E2E-Tests decken **kritische User-Journeys** ab:

- ✅ Authentication & Authorization
- ✅ Navigation zwischen Bereichen
- ✅ Dashboard-Anzeige
- ✅ Berechtigungsprüfungen

---

## 🚧 CI/CD Integration

Die E2E-Tests können in GitHub Actions integriert werden:

```yaml
- name: Install Playwright
  run: npx playwright install --with-deps

- name: Run E2E Tests
  run: npm test
```

---

## 📝 Best Practices

### DO ✅
- Page Object Pattern verwenden für wiederverwendbare Komponenten
- Aussagekräftige Test-Namen
- Vor jedem Test aufräumen (beforeEach)
- Warten auf sichtbare Elemente
- Screenshots bei Fehlern

### DON'T ❌
- Hardcoded Waits (`page.waitForTimeout`)
- XPath-Selektoren (fragil)
- Tests von anderen Tests abhängig machen
- Produktions-Daten in Tests verwenden

---

## 🔍 Troubleshooting

### Tests schlagen fehl

1. **Server läuft?**
   ```bash
   # XAMPP Apache starten oder
   php spark serve
   ```

2. **Richtige Base URL?**
   - Prüfe `playwright.config.ts`

3. **Browser installiert?**
   ```bash
   npx playwright install
   ```

### Timeout-Probleme

In `playwright.config.ts` erhöhen:

```typescript
use: {
  timeout: 30000, // 30 Sekunden
}
```

---

## 📖 Weitere Ressourcen

- [Playwright Dokumentation](https://playwright.dev/)
- [Best Practices](https://playwright.dev/docs/best-practices)
- [Debugging Guide](https://playwright.dev/docs/debug)

---

**Version:** 1.0.0  
**Letzte Aktualisierung:** 09.10.2025


