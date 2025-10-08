# ⚠️ KRITISCH: MySQL muss gestartet werden!

## Das aktuelle Problem:

```
Unable to connect to the database.
Es konnte keine Verbindung hergestellt werden, da der Zielcomputer die Verbindung verweigerte
```

**MySQL ist nicht gestartet oder läuft nicht!**

---

## LÖSUNG:

### **1. XAMPP Control Panel öffnen**

### **2. MySQL starten:**
- Klicken Sie auf **"Start"** neben MySQL
- Warten Sie, bis es grün wird und "Running" anzeigt

### **3. Prüfen Sie:**
- Apache: **Running** (grün)
- MySQL: **Running** (grün)

---

## Was wurde bereits gefixt:

✅ `.env` Datei erstellt (war vorher `env` ohne Punkt)  
✅ Session-Konfiguration aktiviert (DatabaseHandler)  
✅ Datenbank-Konfiguration aktiviert:
```ini
database.default.hostname = localhost
database.default.database = schulag
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

---

## Nach MySQL-Start:

1. **Browser neu laden**: `http://localhost/schulag-v2/public/login`
2. **Login testen**: `admin@schulag.test` / `admin123`

---

**Bitte starten Sie MySQL in XAMPP und testen Sie erneut!**

