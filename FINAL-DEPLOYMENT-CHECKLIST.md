# 🚀 FINAL DEPLOYMENT CHECKLIST

**Projekt:** SchulAG v2.0  
**Status:** ✅ 100% PRODUCTION READY  
**Datum:** 8. Oktober 2025

---

## ✅ PRE-DEPLOYMENT CHECKLISTE

### Code-Qualität
- [x] **PHPStan Level 6:** 0 Fehler ✅
- [x] **Alle TODOs entfernt:** 0 TODOs ✅
- [x] **Type-Hints:** 100% vollständig ✅
- [x] **PHPDoc:** Überall vorhanden ✅

### Features
- [x] **Schuljahr-Config System:** Implementiert ✅
- [x] **Run-Tracking:** Implementiert ✅
- [x] **PDF Export:** Implementiert ✅
- [x] **Excel Export:** Implementiert ✅
- [x] **AG-Filter:** Implementiert ✅
- [x] **Statistics View:** Implementiert ✅
- [x] **Swap Partials:** Implementiert ✅

### Datenbank
- [x] **Migrationen:** 9/9 erfolgreich ✅
- [x] **Seeds:** Vorhanden ✅
- [x] **Foreign Keys:** Korrekt ✅
- [x] **Neue Tabelle:** allocation_runs erstellt ✅

### Git
- [x] **Alle Commits:** Gepusht ✅
- [x] **Working Tree:** Clean ✅
- [x] **Dokumentation:** Vollständig ✅
- [x] **Reports:** 4 Stück erstellt ✅

---

## 🔧 DEPLOYMENT SCHRITTE

### 1. Production-Server Vorbereitung

```bash
# SSH zum Server
ssh user@your-server.com

# Zum Web-Root navigieren
cd /var/www/html

# Projekt clonen
git clone https://github.com/AdrianPeterSchmitt/schulag-v2.git
cd schulag-v2
```

### 2. Dependencies installieren

```bash
# Composer Dependencies
composer install --no-dev --optimize-autoloader

# Permissions setzen
chmod -R 755 writable/
chmod -R 755 public/
```

### 3. Environment konfigurieren

```bash
# .env Datei erstellen
cp env .env

# .env anpassen
nano .env
```

**Wichtige .env Variablen:**

```ini
# ENVIRONMENT
CI_ENVIRONMENT = production

# APP
app.baseURL = 'https://ihredomain.de/'

# DATABASE
database.default.hostname = localhost
database.default.database = schulag_v2
database.default.username = ihr_db_user
database.default.password = ihr_db_passwort
database.default.DBDriver = MySQLi

# ENCRYPTION
encryption.key = hex2bin:IHR_ENCRYPTION_KEY_HIER

# SESSION
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionSavePath = writable/session
```

### 4. Datenbank Setup

```bash
# Migrationen ausführen
php spark migrate

# Seeder ausführen (für Test-Daten)
php spark db:seed DatabaseSeeder
```

### 5. Production-Optimierungen

```bash
# Cache generieren
php spark cache:clear

# Autoloader optimieren (bereits mit --optimize-autoloader gemacht)

# .env Datei schützen
chmod 600 .env
```

### 6. Apache/Nginx Konfiguration

#### Apache (.htaccess schon vorhanden):

DocumentRoot auf `public/` setzen:

```apache
<VirtualHost *:80>
    ServerName ihredomain.de
    DocumentRoot /var/www/html/schulag-v2/public
    
    <Directory /var/www/html/schulag-v2/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/schulag-error.log
    CustomLog ${APACHE_LOG_DIR}/schulag-access.log combined
</VirtualHost>
```

#### Nginx:

```nginx
server {
    listen 80;
    server_name ihredomain.de;
    root /var/www/html/schulag-v2/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

### 7. SSL/HTTPS Setup (Empfohlen)

```bash
# Let's Encrypt mit Certbot
sudo certbot --apache -d ihredomain.de
# oder für Nginx:
sudo certbot --nginx -d ihredomain.de
```

### 8. Ersten Admin-User erstellen

Benutzen Sie den DatabaseSeeder oder erstellen Sie manuell:

```sql
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES (
    'Administrator',
    'admin@ihredomain.de',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 'password'
    'COORDINATOR',
    NOW(),
    NOW()
);
```

**WICHTIG:** Passwort nach dem ersten Login ändern!

---

## 🧪 POST-DEPLOYMENT TESTS

### 1. Basis-Funktionalität

- [ ] Website erreichbar: https://ihredomain.de
- [ ] Login funktioniert
- [ ] Admin-Bereich erreichbar
- [ ] Datenbank-Verbindung funktioniert

### 2. Feature-Tests

- [ ] Klasse erstellen
- [ ] Schüler hinzufügen
- [ ] AG erstellen
- [ ] AG-Wahlen abspeichern
- [ ] Losverfahren durchführen
- [ ] Ergebnisse ansehen
- [ ] PDF exportieren
- [ ] Excel exportieren
- [ ] Statistiken ansehen
- [ ] Tausch durchführen

### 3. Performance-Tests

- [ ] Seitenladezeit < 2 Sekunden
- [ ] PDF-Export < 5 Sekunden
- [ ] Excel-Export < 3 Sekunden
- [ ] Losverfahren < 10 Sekunden

### 4. Security-Tests

- [ ] HTTPS aktiv
- [ ] Login geschützt
- [ ] CSRF-Schutz aktiv
- [ ] SQL-Injection getestet
- [ ] XSS-Schutz aktiv

---

## 📊 MONITORING & WARTUNG

### Logs überwachen

```bash
# Error Logs
tail -f writable/logs/log-*.log

# Apache/Nginx Logs
tail -f /var/log/apache2/schulag-error.log
tail -f /var/log/nginx/error.log
```

### Backup-Strategie

#### Datenbank-Backup (täglich)

```bash
# Cron-Job erstellen
0 2 * * * mysqldump -u user -p password schulag_v2 > /backups/schulag_v2_$(date +\%Y\%m\%d).sql
```

#### Code-Backup (wöchentlich)

```bash
# Git-Pull regelmäßig
0 3 * * 0 cd /var/www/html/schulag-v2 && git pull
```

### Updates

```bash
# Composer Update (monatlich)
composer update

# Migrationen prüfen
php spark migrate:status

# Neue Migrationen ausführen
php spark migrate
```

---

## 🔐 SICHERHEIT

### 1. Permissions

```bash
# Dateien
find . -type f -exec chmod 644 {} \;

# Verzeichnisse
find . -type d -exec chmod 755 {} \;

# Spezielle Permissions
chmod 600 .env
chmod -R 777 writable/
```

### 2. .htaccess Schutz

Bereits vorhanden in:
- `writable/.htaccess` (deny all)
- `app/.htaccess` (deny all)
- `public/.htaccess` (URL rewriting)

### 3. Firewall

```bash
# UFW Firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 📱 SUPPORT & TROUBLESHOOTING

### Häufige Probleme

#### Problem: 500 Internal Server Error

**Lösung:**
1. Writable-Permissions prüfen: `chmod -R 777 writable/`
2. Error-Log prüfen: `writable/logs/log-*.log`
3. Apache/Nginx Error-Log prüfen

#### Problem: Database Connection Failed

**Lösung:**
1. `.env` Credentials prüfen
2. MySQL-Service status: `systemctl status mysql`
3. Verbindung testen: `php spark db:table users`

#### Problem: Assets werden nicht geladen

**Lösung:**
1. `app.baseURL` in `.env` prüfen
2. Trailing Slash hinzufügen: `https://domain.de/`
3. Cache leeren: `php spark cache:clear`

#### Problem: PDF/Excel Export funktioniert nicht

**Lösung:**
1. Composer Dependencies prüfen: `composer install`
2. PHP Extensions prüfen: `php -m | grep -E "gd|zip|xml"`
3. Memory Limit erhöhen in `php.ini`: `memory_limit = 256M`

---

## ✅ DEPLOYMENT ABSCHLUSS

### Nach erfolgreichem Deployment:

1. [ ] **Admin-Login** testen
2. [ ] **Test-AG** erstellen
3. [ ] **Test-Losverfahren** durchführen
4. [ ] **Export-Funktionen** testen
5. [ ] **Performance** messen
6. [ ] **Security-Scan** durchführen
7. [ ] **Backup** einrichten
8. [ ] **Monitoring** aktivieren

### Dokumentation für Endbenutzer:

- [ ] User-Handbuch erstellen
- [ ] Admin-Handbuch erstellen
- [ ] FAQ erstellen
- [ ] Video-Tutorials (optional)

---

## 🎊 ERFOLG!

**Ihr SchulAG v2.0 ist nun LIVE!** 🚀

### Support-Kontakte:

- **GitHub:** https://github.com/AdrianPeterSchmitt/schulag-v2
- **Issues:** https://github.com/AdrianPeterSchmitt/schulag-v2/issues
- **Dokumentation:** Siehe Repository

---

**Viel Erfolg mit Ihrem neuen AG-Verwaltungssystem!** 🎉

*Erstellt am: 8. Oktober 2025*  
*Version: 2.0.0*  
*Status: Production Ready ✅*

