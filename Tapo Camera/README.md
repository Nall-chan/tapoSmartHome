[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)

# tapo Camera <!-- omit in toc -->

Einbindung von TP-Link Tapo Überwachungskameras

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Einrichtung](#3-einrichtung)
- [4. Unterstützte Geräte](#4-unterstützte-geräte)

## 1. Funktionsumfang

- Lokale Netzwerkkommunikation mit Tapo Kameras
- Abruf von Geräteinformationen
- Kompatibilität mit der tapo Discovery Instanz

**Hinweis**: Die Kameras müssen in der TP-Link/Tapo Cloud registriert sein, damit die lokale Anmeldung funktioniert!

## 2. Voraussetzungen

- IP-Symcon ab Version 9.0
- Tapo Kamera mit lokalen Zugangsdaten konfiguriert
- Lokale Netzwerkkommunikation muss aktiviert sein

## 3. Einrichtung

Die Einrichtung erfolgt automatisch über die **tapo Discovery Instanz**:

1. Öffne die tapo Discovery Instanz
2. Gib die Anmeldedaten (E-Mail und Passwort) der Tapo Cloud ein
3. Starte die Gerätesuche ("Aktualisieren" drücken)
4. Wähle die gewünschte Kamera aus der Liste
5. Klicke auf "Erstellen" um die Instanz zu erzeugen

Alternative manuelle Einrichtung:

1. Neue Instanz vom Typ "tapo Camera" anlegen
2. Folgende Eigenschaften konfigurieren:
   - **Host**: IP-Adresse oder Hostname der Kamera
   - **Benutzername**: E-Mail-Adresse (Tapo Cloud)
   - **Passwort**: Passwort (Tapo Cloud)
   - **Verschlüsselung**: KLAP (Standard) oder AES
   - **Protokoll**: HTTP oder HTTPS
   - **Öffnen**: Aktivieren um Verbindung zu starten

## 4. Unterstützte Geräte

### Indoor Kameras

- C100 (Indoor-Kamera Full HD)
- C101 (Indoor-Kamera Full HD)
- C110 (Indoor-Kamera Full HD)
- C210 (Indoor-Kamera mit Nachtsicht)
- C220 (Indoor-Kamera mit Nachtsicht)
- C225 (Indoor-Kamera mit Nachtsicht)

### Wand- & Außenkameras

- C325WB (Wandkamera)
- C460 (Outdoor-Kamera mit Nachtsicht)
- C520WS (Outdoor-Kamera mit Nachtsicht)
- C720 (Pan-Tilt-Kamera)

### 4K & Professional Kameras

- TC40 (Weitwinkel-Kamera)
- TC65 (4K-Kamera)
- TC70 (4K-Kamera)

Weitere Kameras können verwendet werden, falls diese mit der API kompatibel sind. Bei Fragen oder um weitere Geräte hinzuzufügen:  
**[Symcon Community](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865/)**

---

**Version**: 1.70  
**Lizenz**: [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
**Autor**: Michael Tröger
