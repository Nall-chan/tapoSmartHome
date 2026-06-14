[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)

# tapo Hub IO 100 <!-- omit in toc -->

Einbindung von TP-Link Tapo Smart Hubs KH100 und H100

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Einrichtung](#3-einrichtung)
- [4. Unterstützte Hub-Modelle](#4-unterstützte-hub-modelle)
- [5. Verbundene Geräte](#5-verbundene-geräte)

## 1. Funktionsumfang

- Lokale Netzwerkkommunikation mit Tapo Smart Hubs
- Gateway-Funktion für Wireless-Geräte (Thermostate, Sensoren, Schalter)
- Automatische Erkennung verbundener Hub-Geräte
- Zentrale Verwaltung aller angelernten Funk-Geräte
- Kompatibilität mit der tapo Discovery und tapo Configurator Instanz

**Hinweis**: Der Hub muss in der TP-Link/Tapo Cloud registriert sein, damit die lokale Anmeldung funktioniert!

## 2. Voraussetzungen

- IP-Symcon ab Version 9.0
- TP-Link Tapo Smart Hub (KH100 oder H100)
- Lokale Netzwerkkommunikation muss aktiviert sein
- Gültige Cloud-Anmeldedaten

## 3. Einrichtung

Die Einrichtung erfolgt automatisch über die **tapo Discovery Instanz**:

1. Öffne die tapo Discovery Instanz
2. Gib die Anmeldedaten (E-Mail und Passwort) der Tapo Cloud ein
3. Starte die Gerätesuche ("Aktualisieren" drücken)
4. Wähle den Hub aus der Liste
5. Klicke auf "Erstellen" um die Instanz zu erzeugen
6. Eine **tapo Configurator Instanz** wird automatisch miterstellt

### Manuelle Einrichtung

1. Neue Instanz vom Typ "tapo Hub IO 100" anlegen
2. Folgende Eigenschaften konfigurieren:
   - **Host**: IP-Adresse oder Hostname des Hubs
   - **Benutzername**: E-Mail-Adresse (Tapo Cloud)
   - **Passwort**: Passwort (Tapo Cloud)
   - **Verschlüsselung**: KLAP (Standard) oder AES
   - **Protokoll**: HTTP oder HTTPS
   - **Öffnen**: Aktivieren um Verbindung zu starten
3. Speichern und warten bis der Hub verbunden ist
4. Die tapo Configurator Instanz verwenden um Hub-Geräte hinzuzufügen

## 4. Unterstützte Hub-Modelle

- **KH100**: Kasa Smart Hub mit integrierter Sirene (ältere Generation)
- **H100**: Tapo Smart Hub mit integrierter Sirene (ältere Generation)

**Hinweis**: Für neuere Hubs mit direkter LAN-Verbindung verwenden Sie bitte **tapo Hub IO 200** (H200).

## 5. Verbundene Geräte

Der Hub kann mit folgenden Wireless-Geräten gekoppelt werden:

### Thermostat

- **KE100**: Heizkörperthermostat

### Sensoren

- **T100**: Bewegungsmelder
- **T110**: Intelligenter Kontaktsensor
- **T300**: Wasserlecksensor
- **T310**: Temperatur- & Feuchtigkeitssensor
- **T315**: Temperatur- & Feuchtigkeitssensor mit Display

### Schalter

- **S200**: Remote Button / Dimmschalter
- **S210**: Lichtschalter 1-fach
- **S220**: Lichtschalter 2-fach

### Weitere Geräte

Die Konfiguration der Hub-Geräte erfolgt über die **tapo Configurator Instanz**, die automatisch miterstellt wird.

---

**Version**: 1.70  
**Lizenz**: [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
**Autor**: Michael Tröger
