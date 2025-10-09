# Alpine.js Debugging Guide

## 🐛 Der AG-Modal Bug und wie wir ihn gelöst haben

### Problem
Das Modal zum Erstellen einer neuen AG öffnete sich nicht, obwohl der Button korrekt angeklickt wurde.

### Root Cause
**Alpine.js Scoping-Problem**: Das Modal (`<template x-if="showNewModal">`) befand sich **außerhalb** des `x-data` Scopes, in dem die Variable `showNewModal` definiert war.

```php
<!-- ❌ FALSCH: Modal außerhalb des x-data Scopes -->
<div class="fade-in" x-data="{ showNewModal: false }">
    <button @click="showNewModal = true">Neue AG</button>
</div>

<!-- Modal hier kann showNewModal nicht sehen! -->
<template x-if="showNewModal">
    <div class="modal">...</div>
</template>
```

### Lösung
Das Modal muss **innerhalb** des gleichen `x-data` Scopes liegen:

```php
<!-- ✅ RICHTIG: Modal innerhalb des x-data Scopes -->
<div class="fade-in" x-data="{ showNewModal: false }">
    <button @click="showNewModal = true">Neue AG</button>
    
    <!-- Weiterer Inhalt... -->
    
    <!-- Modal innerhalb des Scopes -->
    <template x-if="showNewModal">
        <div class="modal">...</div>
    </template>
</div>
```

## 🛠️ Tools zur automatischen Erkennung

### 1. Alpine.js Scope Checker

Ein Node.js-Tool, das Ihre View-Dateien auf Alpine.js Scope-Probleme überprüft:

```bash
npm run check:alpine
```

**Was es findet:**
- ✅ `x-if`/`x-show` Direktiven außerhalb ihres `x-data` Scopes
- ✅ `@click` Handler, die auf undefinierte Variablen zugreifen
- ✅ Verschachtelte `x-data` Scopes, die zu Konflikten führen können
- ✅ Anti-Pattern wie `<template x-if="true">`

**Verwendung:**
```bash
# Alle Views überprüfen
npm run check:alpine

# Ausgabe zeigt Fehler und Warnungen mit Datei und Zeilennummer
```

### 2. E2E Tests für Modals

Automatisierte Tests, die das Modal-Verhalten überprüfen:

```bash
# Nur AG-Modal Tests
npm run test:clubs

# Alle E2E Tests
npm test
```

**Was getestet wird:**
- ✅ Modal öffnet sich beim Button-Klick
- ✅ Modal schließt sich beim Abbrechen
- ✅ Alpine.js State ist korrekt gesetzt
- ✅ Keine JavaScript-Fehler in der Console
- ✅ Vollständiger Workflow (Formular ausfüllen, speichern)

## 📋 Checkliste für Alpine.js Modals

Wenn Sie ein neues Modal erstellen, überprüfen Sie:

1. **Scope**: Ist das Modal innerhalb des `x-data` Containers?
   ```php
   <div x-data="{ showModal: false }">
       <!-- Button -->
       <!-- Inhalt -->
       <template x-if="showModal">
           <!-- Modal hier! -->
       </template>
   </div>
   ```

2. **Variable**: Ist die Variable im `x-data` definiert?
   ```php
   x-data="{ showModal: false, editId: null }"
   ```

3. **Handler**: Verwendet der Button die richtige Variable?
   ```php
   @click="showModal = true"
   ```

4. **HTMX**: Wird das Modal nach HTMX-Updates korrekt verarbeitet?
   ```php
   x-init="htmx.process($el)"
   ```

5. **Server-Cache**: Nach Änderungen Apache neu starten!
   ```bash
   # XAMPP Control Panel: Apache STOP → START
   ```

## 🔍 Debug-Tipps

### Browser Console

Überprüfen Sie Alpine.js Daten in der Browser Console:

```javascript
// Finde Element mit x-data
const el = document.querySelector('[x-data*="showModal"]');

// Zeige Alpine.js Daten
Alpine.$data(el);
// Output: { showModal: false, editId: null }

// Ändere Wert manuell zum Testen
Alpine.$data(el).showModal = true;
```

### Debug Helper

Verwenden Sie die integrierten Debug-Funktionen:

```php
<!-- In Ihrer View -->
<div x-data="{ showModal: false }" 
     x-init="console.log('Initial State:', $data)">
     
    <button @click="showModal = true; console.log('Button clicked, showModal:', showModal)">
        Öffnen
    </button>
</div>
```

## 🚨 Häufige Fehler

### 1. Modal außerhalb des Scopes
```php
<!-- ❌ FALSCH -->
<div x-data="{ show: false }">
    <button @click="show = true">Open</button>
</div>
<template x-if="show"><!-- Modal --></template>
```

### 2. Falsche Variable
```php
<!-- ❌ FALSCH -->
<div x-data="{ showModal: false }">
    <button @click="show = true">Open</button> <!-- 'show' !== 'showModal' -->
</div>
```

### 3. Template ohne x-if
```php
<!-- ❌ FALSCH -->
<template x-if="true"><!-- Immer sichtbar, kein Toggle möglich --></template>

<!-- ✅ RICHTIG -->
<template x-if="showModal"><!-- Kann getoggled werden --></template>
```

### 4. Mehrere Modals im selben Scope
```php
<!-- ⚠️ VORSICHT -->
<div x-data="{ 
    showNewModal: false,  <!-- Für "Neu" Modal -->
    showEditModal: false, <!-- Für "Bearbeiten" Modal -->
    editId: null
}">
    <!-- Beide Modals innerhalb des Scopes -->
</div>
```

## 📊 Testing-Strategie

### Unit Tests (PHPUnit)
Tests für PHP-Logik, Models, Controller

### E2E Tests (Playwright)
Tests für User-Interaktionen, inkl. Alpine.js Modals

### Static Analysis (Alpine Scope Checker)
Automatische Code-Analyse für Scope-Probleme

### Kombination
```bash
# Vor jedem Commit
npm run check:alpine  # Static Analysis
npm test              # E2E Tests
composer test         # PHPUnit Tests
```

## 🎯 Best Practices

1. **Ein x-data Container pro Komponente**
   - Halten Sie Alpine.js Scopes fokussiert
   - Vermeiden Sie tief verschachtelte Scopes

2. **Aussagekräftige Namen**
   - `showNewModal`, `showEditModal` statt `show`, `visible`

3. **Dokumentation**
   - Kommentieren Sie komplexe Alpine.js Logik

4. **Testing**
   - Schreiben Sie E2E Tests für kritische Modals
   - Verwenden Sie den Scope Checker regelmäßig

5. **Server-Cache beachten**
   - Nach View-Änderungen immer Apache neu starten
   - In Produktion: Cache-Clearing implementieren

## 🔗 Weitere Ressourcen

- [Alpine.js Dokumentation](https://alpinejs.dev/)
- [Alpine.js x-data](https://alpinejs.dev/directives/data)
- [Alpine.js x-if](https://alpinejs.dev/directives/if)
- [HTMX + Alpine.js](https://htmx.org/essays/alpine-js-integration/)

