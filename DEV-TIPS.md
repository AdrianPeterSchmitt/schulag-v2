# SchulAG v2 – Praxis-Tipps (Alpine.js, HTMX, CodeIgniter 4)

Diese Sammlung fasst die wiederkehrenden Lösungen aus den letzten Fixes zusammen. Ziel: typische Integrationsfallen schnell erkennen und beheben.

## 1) Modals mit Alpine.js zuverlässig anzeigen

- Modal-Komponenten immer innerhalb des `x-data`-Scopes platzieren.
- Für dynamisch erzeugte Inhalte statt `x-show` besser `template x-if` nutzen (Elemente werden vollständig gemountet):

```html
<div x-data="{ showModal: false }">
  <button @click="showModal = true">Öffnen</button>

  <template x-if="showModal">
    <div class="fixed inset-0 ..." @click.self="showModal = false" x-init="htmx.process($el)">
      <!-- Inhalt -->
    </div>
  </template>
</div>
```

- Nach HTMX-Swaps Alpine erneut für den Zielknoten initialisieren (global):

```js
document.body.addEventListener('htmx:afterSwap', (event) => {
  const target = event.detail.target || event.target;
  if (target && window.Alpine && typeof Alpine.initTree === 'function') {
    Alpine.mutateDom(() => Alpine.initTree(target));
  }
});
```

## 2) Alpine + HTMX: sichere Datenübergabe

- Kein Inline-JSON in Event-Handlern rendern (Escaping-/Syntax-Probleme). Besser `data-*` Attribute nutzen:

```html
<button
  data-schueler-id="<?= esc($s['id']) ?>"
  data-schueler-name="<?= esc($s['name']) ?>"
  @click="$store.editModal.open({ id: +$el.dataset.schuelerId, name: $el.dataset.schuelerName })">
  Bearbeiten
</button>
```

## 3) HTMX + CodeIgniter 4: CSRF korrekt behandeln

- CSRF-Token als Meta-Tag bereitstellen:

```php
<meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
```

- Global allen HTMX-Requests den Token-Header setzen:

```js
document.body.addEventListener('htmx:configRequest', (event) => {
  event.detail.headers['X-Requested-With'] = 'XMLHttpRequest';
  const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
  if (meta?.content) event.detail.headers['<?= csrf_header() ?>'] = meta.content;
});
```

- Optional, um flakige Tokens zu vermeiden: `app/Config/Security.php`

```php
public bool $regenerate = false; // Token bleibt stabil während der Session
```

- POST: `csrf_field()` im Formular verwenden.
- PUT/DELETE: Token per Header (siehe oben) senden.
- WICHTIG: Bei PUT in CI4 die Daten via `getRawInput()` lesen, nicht `getPost()`:

```php
$raw = $this->request->getRawInput();
$data = [
  'name'   => $raw['name']   ?? '',
  'typ_gl' => $raw['typ_gl'] ?? '',
];

// Validierung: Daten explizit als zweiten Parameter übergeben!
$rules = ['name' => 'required|min_length[3]', 'typ_gl' => 'required|in_list[G,LE]'];
if (!$this->validate($rules, $data)) {
    return $this->response->setStatusCode(422)->setJSON([
        'success' => false,
        'errors' => $this->validator->getErrors()
    ]);
}
```

## 4) Update/PUT mit dynamischer URL

- URL im Formular setzen, sobald Daten vorhanden sind (und anschließend HTMX neu verarbeiten):

```html
<form x-init="$nextTick(() => {
  const id = $store.editModal.schueler?.id;
  if (id) { $el.setAttribute('hx-put', '<?= base_url('admin/klassen/'.$klasse['id'].'/schueler/') ?>' + id); htmx.process($el); }
})">
  <!-- Felder -->
</form>
```

## 5) Löschen mit schönem Confirm-Modal (statt Browser-Dialog)

- Globaler Confirm-Store (in `layouts/main.php`):

```js
Alpine.store('confirm', {
  show: false, message: '', onConfirm: null,
  open(msg, ok) { this.message = msg; this.onConfirm = ok; this.show = true; },
  confirm() { this.onConfirm?.(); this.close(); },
  close() { this.show = false; this.message = ''; this.onConfirm = null; }
});
```

- Aufruf am Button:

```html
<button type="button"
  @click="$store.confirm.open(`Schüler '<?= esc($s['name']) ?>' wirklich löschen?`,
    () => { htmx.ajax('DELETE', '<?= base_url('admin/klassen/'.$klasse['id'].'/schueler/'.$s['id']) ?>', { target: '#schueler-list', swap: 'innerHTML' }) })">
  Löschen
</button>
```

## 6) Debugging-Empfehlungen

- Globale Logs für Alpine/HTMX-Events (bereits vorhanden).
- Bei „Request läuft, aber nichts passiert“ zuerst prüfen:
  - Network: Pfad, Methode, Statuscode, Payload
  - Konsole: CSRF-403, Syntaxfehler, Alpine-Scopes
  - Nach HTMX-Swaps: wurde `Alpine.initTree()` für den Zielknoten aufgerufen?

## 7) Checkliste bei Problemen

- Modal außerhalb des `x-data`? → in Scope verschieben, ggf. `x-if` verwenden.
- HTMX-Inhalt dynamisch erzeugt? → `htmx.process($el)` und `Alpine.initTree()` nachladen.
- PUT/DELETE 403? → CSRF-Header gesetzt? `regenerate=false`? `getRawInput()` genutzt?
- JSON in Attributen? → lieber `data-*` Attribute verwenden.

Diese Muster sind in der App bereits integriert und können als Referenz für neue Screens wiederverwendet werden.

---

## Inline-Fehleranzeige in Modals (Best Practice)

Statt nur Toast-Benachrichtigungen sollten Validierungsfehler **direkt beim betroffenen Feld** im Modal angezeigt werden:

```html
<div x-data="{ errors: {} }">
  <form @htmx:response-error="
      const resp = JSON.parse(event.detail.xhr.response);
      if (resp.errors) errors = resp.errors;
    "
    @htmx:after-request="if(event.detail.successful) { errors = {}; $el.reset(); }">
    
    <input name="klassenleitung" required
           :class="{ 'border-red-500': errors.klassenleitung }">
    <p x-show="errors.klassenleitung" x-text="errors.klassenleitung" 
       class="text-red-500 text-sm mt-1"></p>
  </form>
</div>
```

**Vorteile:**
- Modal bleibt offen
- Fehler direkt beim Feld sichtbar (UX++)
- Rot umrandete Felder + Fehlermeldung darunter
- Formulareingaben bleiben erhalten

## 12) Controller-Response bei HTMX/PUT (500-Fehler vermeiden)

In CI4-Controllern mit strengem Rückgabetyp (`ResponseInterface`) darf NICHT direkt ein String-View returned werden – sonst entsteht ein 500er („Return value must be of type ResponseInterface, string returned“).

Empfohlenes Muster:

```php
// FALSCH (liefert String):
// return view('admin/partials/klasse_content', ['klasse' => $klasse]);

// RICHTIG (liefert Response):
return $this->response->setBody(
    view('admin/partials/klasse_content', ['klasse' => $klasse])
);
```

Alternativ kann der Methodentyp entspannt werden (nur wenn nötig):

```php
public function updateSchueler(int $klasseId, int $schuelerId): \CodeIgniter\HTTP\ResponseInterface|string
```

Best Practice: Einheitlich `ResponseInterface` beibehalten und konsequent `setBody(view(...))` verwenden.

## 8) HTMX-Fehlerhandling (4xx/5xx abfangen)

```js
// Vor dem Swap prüfen, ob Fehlerstatus – dann Inhalt NICHT tauschen
document.body.addEventListener('htmx:beforeSwap', (event) => {
  const status = event.detail.xhr?.status || 0;
  if (status >= 400) {
    event.detail.shouldSwap = false; // Kein fehlerhafter HTML-Swap
    const text = event.detail.xhr?.responseText || 'Unbekannter Fehler';
    console.error('HTMX Error', status, text);
    // Optional: Toast mit erster Validierungsfehlermeldung (bei 422)
    if (status === 422) {
      try {
        const json = JSON.parse(text);
        const firstMsg = json?.errors ? Object.values(json.errors)[0] : null;
        if (firstMsg) showToast?.('Validierung', firstMsg, '⚠️');
      } catch (_) { /* ignore parse errors */ }
    }
  }
});
```

**WICHTIG**: Validierungsfehler im Controller mit Status **422** zurückgeben, nicht 400:

```php
if (!$this->validate($rules)) {
    return $this->response
        ->setStatusCode(422)
        ->setJSON([
            'success' => false,
            'errors' => $this->validator->getErrors()
        ]);
}
```

## 9) Formular-Reset & Fokus nach erfolgreichem Swap

```js
document.body.addEventListener('htmx:afterSwap', (event) => {
  // Beispiel: Nach Update der Schülerliste – Formular zurücksetzen & Fokus setzen
  if (event.detail.target?.id === 'schueler-list') {
    const form = document.querySelector('form[hx-post], form[hx-put]');
    if (form) form.reset();
    const firstInput = form?.querySelector('input, select, textarea');
    firstInput?.focus();
  }
});
```

## 10) Reusable Partials für Listen/Zeilen

- Server-seitig HTML in Partials auslagern (z. B. `app/Views/admin/partials/schueler_list.php`).
- Controller gibt nach HTMX-Requests immer das gleiche Partial zurück.
- Einheitliche Button-Attribute (Bearbeiten/Löschen) zentral im Partial pflegen.

Vorteil: Weniger Duplikate, konsistente Alpine-/HTMX-Integration.

## 11) CSRF-Token-Rotation (optional reaktivieren)

Wenn `Security::$regenerate = true` wieder aktiviert wird, muss der Client das neue Token aus jeder Response übernehmen.

```js
document.addEventListener('htmx:afterRequest', (event) => {
  const hdr = '<?= csrf_header() ?>';
  const newToken = event.detail.xhr?.getResponseHeader(hdr);
  if (newToken) {
    // Meta aktualisieren
    let meta = document.querySelector(`meta[name="${hdr}"]`);
    if (!meta) {
      meta = document.createElement('meta');
      meta.setAttribute('name', hdr);
      document.head.appendChild(meta);
    }
    meta.setAttribute('content', newToken);
  }
});

// Und HTMX sendet durch htmx:configRequest immer den aktuellen Meta-Token mit (siehe Abschnitt 3)
```


