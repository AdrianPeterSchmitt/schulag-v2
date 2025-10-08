# 🚀 GitHub Repository Setup - Anleitung

## ✅ Was bereits erledigt ist:

- ✅ Git-Repository initialisiert
- ✅ .gitignore erstellt
- ✅ README.md erstellt
- ✅ Initial Commit gemacht (136 Dateien, 12.415+ Zeilen)

---

## 📋 Nächste Schritte auf GitHub:

### **Schritt 1: GitHub-Repository erstellen**

1. **Gehen Sie zu GitHub:**
   - Öffnen Sie: https://github.com/new

2. **Repository-Einstellungen:**
   - **Repository Name:** `schulag-v2`
   - **Description:** `🎓 SchulAG v2 - Moderne Web-Anwendung zur Verwaltung von Arbeitsgemeinschaften mit intelligentem Losverfahren (CodeIgniter 4 + HTMX + Tailwind CSS)`
   - **Visibility:** 
     - ✅ **Public** (empfohlen für Open Source)
     - ⚪ Private (wenn nur für Sie)
   
3. **WICHTIG - Nichts auswählen:**
   - ❌ **NICHT** "Add a README file" ankreuzen
   - ❌ **NICHT** ".gitignore" auswählen
   - ❌ **NICHT** "Choose a license" auswählen
   
   *(Wir haben bereits alles lokal!)*

4. **Klicken Sie auf:** "Create repository"

---

### **Schritt 2: Repository mit GitHub verbinden**

Nach dem Erstellen zeigt GitHub Ihnen Anweisungen. Verwenden Sie diese Befehle:

#### **Option A: HTTPS (empfohlen für Anfänger)**

Öffnen Sie PowerShell in `C:\xampp\htdocs\schulag-v2` und führen Sie aus:

```powershell
# Remote hinzufügen (ersetzen Sie IHR-USERNAME!)
git remote add origin https://github.com/IHR-USERNAME/schulag-v2.git

# Branch umbenennen zu main (falls noch master)
git branch -M main

# Ersten Push
git push -u origin main
```

**Authentifizierung:**
- Beim ersten Push werden Sie nach Username und Passwort gefragt
- **Wichtig:** Verwenden Sie ein **Personal Access Token** als Passwort!
  - GitHub akzeptiert keine normalen Passwörter mehr
  - Token erstellen: https://github.com/settings/tokens
  - Scopes: `repo` (alle Checkboxen unter repo)

#### **Option B: SSH (für Fortgeschrittene)**

```powershell
# Remote hinzufügen
git remote add origin git@github.com:IHR-USERNAME/schulag-v2.git

# Branch umbenennen zu main
git branch -M main

# Ersten Push
git push -u origin main
```

**Voraussetzung:** SSH-Key muss in GitHub hinterlegt sein

---

### **Schritt 3: Personal Access Token erstellen (falls HTTPS)**

1. **GitHub Settings öffnen:**
   - Klicken Sie auf Ihr Profil-Bild (oben rechts)
   - → Settings
   - → Developer settings (ganz unten links)
   - → Personal access tokens
   - → Tokens (classic)

2. **Neues Token erstellen:**
   - Klicken Sie: "Generate new token"
   - Note: `schulag-v2-local-dev`
   - Expiration: `90 days` (oder länger)
   - Scopes:
     - ✅ `repo` (alle Checkboxen)

3. **Token kopieren:**
   - ⚠️ **WICHTIG:** Speichern Sie das Token sofort!
   - Sie sehen es nur einmal!

4. **Token als Passwort verwenden:**
   - Bei `git push` nach Passwort gefragt → Token eingeben

---

### **Schritt 4: Push durchführen**

Führen Sie in PowerShell aus (im Projektverzeichnis):

```powershell
cd C:\xampp\htdocs\schulag-v2

# Remote hinzufügen (ersetzen Sie IHR-USERNAME!)
git remote add origin https://github.com/IHR-USERNAME/schulag-v2.git

# Branch umbenennen
git branch -M main

# Push
git push -u origin main
```

**Erwartete Ausgabe:**
```
Enumerating objects: 156, done.
Counting objects: 100% (156/156), done.
Delta compression using up to 8 threads
Compressing objects: 100% (145/145), done.
Writing objects: 100% (156/156), 250.45 KiB | 8.35 MiB/s, done.
Total 156 (delta 15), reused 0 (delta 0), pack-reused 0
remote: Resolving deltas: 100% (15/15), done.
To https://github.com/IHR-USERNAME/schulag-v2.git
 * [new branch]      main -> main
Branch 'main' set up to track remote branch 'main' from 'origin'.
```

---

### **Schritt 5: Verifizierung**

1. **Öffnen Sie Ihr GitHub-Repository:**
   - https://github.com/IHR-USERNAME/schulag-v2

2. **Prüfen Sie:**
   - ✅ README.md wird angezeigt
   - ✅ Alle Ordner vorhanden (app, public, vendor, etc.)
   - ✅ .gitignore funktioniert (keine .env, keine logs, etc.)
   - ✅ 136 Dateien committed

---

## 🎉 **Fertig! Ihr Projekt ist auf GitHub!**

### **Nützliche Git-Befehle für die Zukunft:**

#### **Status prüfen:**
```powershell
git status
```

#### **Änderungen committen:**
```powershell
# Alle geänderten Dateien hinzufügen
git add .

# Commit erstellen
git commit -m "Beschreibung der Änderung"

# Zu GitHub pushen
git push
```

#### **Änderungen von GitHub holen:**
```powershell
git pull
```

#### **Branch erstellen (für Features):**
```powershell
# Neuen Branch erstellen
git checkout -b feature/mein-feature

# Änderungen machen, committen...
git add .
git commit -m "Feature XYZ hinzugefügt"

# Branch pushen
git push -u origin feature/mein-feature
```

---

## 📊 **Repository-Statistiken:**

Nach dem Push zeigt GitHub:

- **136 Commits:** 1 (Initial Commit)
- **Dateien:** 136
- **Zeilen Code:** 12.415+
- **Languages:**
  - PHP: ~85%
  - HTML/Views: ~10%
  - JavaScript: ~2%
  - CSS: ~1%
  - Other: ~2%

---

## 🔒 **Sicherheitshinweise:**

### **Was ist NICHT im Repository:**

✅ `.env` - Umgebungs-Konfiguration (sensible Daten!)  
✅ `vendor/` - Composer Dependencies (werden neu installiert)  
✅ `writable/cache/*` - Cache-Dateien  
✅ `writable/logs/*` - Log-Dateien  
✅ `writable/session/*` - Session-Dateien  
✅ `composer.lock` - Wird ignoriert für Flexibilität  
✅ Temporäre Dateien (*.backup, *.bak, check-*.php, etc.)

### **Was IST im Repository:**

✅ Kompletter Code (Controllers, Models, Views, Services)  
✅ Migrationen & Seeder  
✅ Konfigurationsdateien (ohne sensible Daten)  
✅ `env` - Template für .env  
✅ `composer.json` - Dependency-Liste  
✅ `README.md` - Dokumentation  
✅ `LICENSE` - MIT Lizenz

---

## 🌟 **Repository verbessern (Optional):**

### **GitHub Features aktivieren:**

1. **Issues aktivieren:**
   - Settings → Features → ✅ Issues

2. **GitHub Actions (CI/CD):**
   - Erstellen Sie `.github/workflows/ci.yml` für automatische Tests

3. **Branch Protection:**
   - Settings → Branches → Add rule für `main`
   - ✅ Require pull request reviews before merging

4. **Topics hinzufügen:**
   - Auf Hauptseite → "Add topics"
   - Topics: `php`, `codeigniter`, `htmx`, `tailwindcss`, `education`, `school-management`, `lottery`, `codeigniter4`

5. **About bearbeiten:**
   - Website: URL Ihrer Deployment-Instanz (falls vorhanden)
   - Description: "🎓 SchulAG v2 - Moderne Web-Anwendung zur Verwaltung von Arbeitsgemeinschaften mit intelligentem Losverfahren"

---

## 📝 **Nächste Schritte nach GitHub:**

1. ✅ **Repository erstellt** → Anderen zeigen, Backup sicher
2. ⏳ **Continuous Deployment** → Automatisches Deployment bei Push
3. ⏳ **Issue Tracking** → Bugs und Feature-Requests verwalten
4. ⏳ **Pull Requests** → Zusammenarbeit mit anderen Entwicklern
5. ⏳ **Releases** → Versionen taggen (v1.0.0, v2.0.0, etc.)

---

## 🆘 **Troubleshooting:**

### **Problem: "Permission denied (publickey)"**

**Lösung:** Verwenden Sie HTTPS statt SSH:
```powershell
git remote remove origin
git remote add origin https://github.com/IHR-USERNAME/schulag-v2.git
git push -u origin main
```

### **Problem: "Authentication failed"**

**Lösung:** Verwenden Sie Personal Access Token statt Passwort
- Siehe Schritt 3 oben

### **Problem: "fatal: 'origin' already exists"**

**Lösung:** Remote entfernen und neu hinzufügen:
```powershell
git remote remove origin
git remote add origin https://github.com/IHR-USERNAME/schulag-v2.git
```

### **Problem: "Updates were rejected because the remote contains work"**

**Lösung:** Pull zuerst, dann push:
```powershell
git pull origin main --rebase
git push -u origin main
```

---

## 🎊 **Viel Erfolg mit Ihrem GitHub-Repository!**

Ihr Code ist jetzt sicher in der Cloud gespeichert! ☁️

---

**Erstellt:** 08.10.2025  
**Projekt:** SchulAG v2  
**Version:** 2.0.0-beta

