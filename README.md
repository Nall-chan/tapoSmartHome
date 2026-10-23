[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-Custom--NC--SA-green.svg)](#6-lizenz)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#3-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#3-spenden)  

# tapo SmartHome <!-- omit in toc -->

Einbindung der TP-Link tapo SmartHome Geräte

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
  - [Zur Library](#zur-library)
  - [Zur Integration von Geräten](#zur-integration-von-geräten)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Enthaltende Module](#4-enthaltende-module)
  - [1. Nicht Geräte Instanzen](#1-nicht-geräte-instanzen)
    - [tapo Discovery](#tapo-discovery)
    - [tapo Hub Configurator](#tapo-hub-configurator)
    - [tapo Hub IO](#tapo-hub-io-io-100-io-200)
  - [2. Netzwerk Geräte Instanzen](#2-netzwerk-geräte-instanzen)
    - [tapo Light](#tapo-light)
    - [tapo Light Color](#tapo-light-color)
    - [tapo Stripe Color](#tapo-stripe-color)
    - [tapo Socket](#tapo-socket)
    - [tapo Energy Socket](#tapo-energy-socket)
    - [tapo Multi Energy Socket](#tapo-multi-energy-socket)
    - [tapo Camera](#tapo-camera)
  - [3. Hub Geräte Instanzen](#3-hub-geräte-instanzen)
    - [tapo Hub Device](#tapo-hub-device)
- [5. Anhang](#5-anhang)
  - [1. GUID der Module](#1-guid-der-module)
  - [2. Changelog](#2-changelog)
  - [3. Spenden](#3-spenden)
- [6. Lizenz](#6-lizenz)

# 1. Funktionsumfang

 Komplett lokale Kommunikation mit den Geräten.  
 Die Geräte müssen aber in der TP-Link/tapo Cloud registriert sein, damit die lokale Anmeldung funktioniert!

> [!NOTE]
> TP-Link bietet in der Tapo-App die Funktion `Third-Party Compatibility` an (unter `Me` → `Third-Party Services`). Sie ist laut TP-Link standardmäßig deaktiviert und soll die Nutzung der Geräte mit Apps und Systemen anderer Anbieter ermöglichen. Lässt sich ein Gerät nicht lokal anmelden, sollte diese Einstellung geprüft werden. Ob sie für dieses Modul erforderlich ist, ist nicht bestätigt. TP-Link weist darauf hin, dass sie die Sicherheit verringern kann und keine offizielle Unterstützung für Drittanbieter besteht: [TP-Link FAQ 4416](https://www.tp-link.com/de/support/faq/4416/)

## Vorbemerkungen

### Zur Library

Die Library bindet tapo-Geräte über die lokale Schnittstelle der Geräte an. Unterstützt werden die in [Enthaltende Module](#4-enthaltende-module) aufgeführten Gerätetypen, weitere Geräte können ergänzt werden, wenn Daten (Debug-Mitschnitte) vorliegen.  
Geräte mit neuer Firmware, welche das Verschlüsselungsverfahren `TPAP` nutzen (z. B. neuere H100-Firmware), werden aktuell nicht unterstützt.  

### Zur Integration von Geräten

Die Geräte müssen in der TP-Link/tapo Cloud registriert sein. Als Zugangsdaten für die lokale Anmeldung werden der Benutzername (eMail-Adresse) und das Passwort der Cloud/App benötigt.  

# 2. Voraussetzungen

- IP-Symcon ab Version 9.0

# 3. Software-Installation

Über den 'Module-Store' in IPS das Modul 'tapo SmartHome' hinzufügen.  
![Module-Store](imgs/install.png)  

# 4. Enthaltende Module

Details zur Einrichtung sind direkt in der Dokumentation der jeweiligen Module beschrieben.  
Es wird empfohlen, die Einrichtung mit der Discovery-Instanz ([tapo Discovery](Tapo%20Discovery/README.md)) zu starten.  

## 1. Nicht Geräte Instanzen

### [tapo Discovery](Tapo%20Discovery/README.md)

- Auffinden von Geräten und Hubs im Netzwerk und Anlegen der Geräte bzw. Konfigurator-Instanz inklusive Gateway in IPS.  

### [tapo Hub Configurator](Tapo%20Hub%20Configurator/README.md)

- Einfaches Anlegen von Geräte-Instanzen welche mit einem Smart Hub verbunden sind.  
  
### tapo Hub IO ([IO 100](Tapo%20Hub%20IO%20100/README.md), [IO 200](Tapo%20Hub%20IO%20200/README.md))

 Smart Hub Instanz als IO Instanz zur Kommunikation mit den angelernten Funk-Geräten und Symcon.  
 Folgende Hubs werden unterstützt:  

- KH100 (Kasa Hub mit integrierter Sirene)
- H100 (Tapo Hub mit integrierter Sirene)
- H200 (Tapo Hub mit LAN-Verbindung)

## 2. Netzwerk Geräte Instanzen

### [tapo Light](Tapo%20Light/README.md)

Leuchtmittel:  

- L510 (E27, dimmbar)  
- L520 (E27, dimmbar)  
- L610 (GU10, dimmbar)

---  

### [tapo Light Color](Tapo%20Light%20Color/README.md)

Leuchtmittel / LED-Stripe:  

- L530 (E27, dimmbar, mehrfarbig)  
- L535 (E27, dimmbar, mehrfarbig)  
- L630 (GU10, dimmbar, mehrfarbig)  
- L900 (LED Stripe, dimmbar, mehrfarbig)

---  

### [tapo Stripe Color](Tapo%20Stripe%20Color/README.md)

Leuchtmittel / LED-Stripe:  

- L920 (LED Stripe, dimmbar, mehrfarbig, Segmentiert)
- L930 (LED Stripe, dimmbar, mehrfarbig, Segmentiert)

---  

### [tapo Socket](Tapo%20Socket/README.md)

Zwischenstecker:  

- P100 (mini)
- P105 (rund)

---  

### [tapo Energy Socket](Tapo%20Energy%20Socket/README.md)

Zwischenstecker oder Aktor mit Energiemessung:  

- P110 (mini)
- P115 (rund)
- S110 (up Aktor)

### [tapo Multi Energy Socket](Tapo%20Energy%20Sockets%20Multi/README.md)

Zwischenstecker oder Aktor mit mehreren Kanälen und Energiemessung:  

- S112 (up Aktor, Modus Schalter)
- S112 (up Aktor, Modus Rollladen)

---  

### [tapo Camera](Tapo%20Camera/README.md)

Überwachungskameras:

- C100
- C101
- C110
- C210
- C220
- C225
- C325WB
- C460
- C520WS
- C720
- TC40
- TC65
- TC70
- TC71 (Indoor, PTZ-Kamera) **getestet**

Weitere Geräte können im Modul ergänzt werden, leider liegen aktuell keine Daten für diese Geräte vor.
Hier melden für die integration von weiteren Geräten:  
**[Symcon Community](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865/)**

## 3. Hub Geräte Instanzen

### [tapo Hub Device](Tapo%20Hub%20Device/README.md)

aktuell umgesetzt und getestete Geräte:  

- KE100 (Heizkörperthermostat)  
- T110 (Kontaktsensor)  
- T310 (Temperatur- & Feuchtigkeitssensor)  
- T100 (Bewegungsmelder, ungetestet)  
- T300 (Wasserlecksensor, ungetestet)  

Alle restlichen Geräte können im Modul ergänzt werden, leider liegen aktuell keine Daten für diese Geräte vor.
Hier melden für die integration von weiteren Geräten:  
**[Symcon Community](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865/)**

# 5. Anhang

## 1. GUID der Module

| Modul | Typ | GUID |
| ----- | --- | ---- |
| tapo Camera | Device | `{16C67F80-D963-451A-BED3-68B2E7B12F6C}` |
| tapo Discovery | Discovery | `{DCD52B0C-3E06-4EFE-AA95-C54709AC87D3}` |
| tapo Energy Socket | Device | `{B18B6CAA-AB46-495D-9A7A-85FA3A83113A}` |
| tapo Multi Energy Sockets | Device | `{BC84530B-1A6A-4614-8CCB-C60019EEFFF1}` |
| tapo Hub Configurator | Konfigurator | `{CA1E7005-E5D1-455C-95DF-5ECE8DC50654}` |
| tapo Hub Device | Device | `{DBBC5150-EF75-487B-9407-27C11DDEF6B4}` |
| tapo Hub IO 100 | IO | `{1EDD1EB2-6885-4D87-BA00-9328D74A85C4}` |
| tapo Hub IO 200 | IO | `{132F21C7-1DC0-4DCA-8DB3-6CD41FAB536D}` |
| tapo Light Color | Device | `{3C59DCC3-4441-4E1C-A59C-9F8D26CE2E82}` |
| tapo Light | Device | `{1B9D73D6-853D-4E2E-9755-2273FD7A6123}` |
| tapo Socket | Device | `{AAD6F48D-C23F-4C59-8049-A9746DEB699B}` |
| tapo Multi Sockets | Device | `{C923F554-4621-446E-B0D2-1422F2EB84B5}` |
| tapo Stripe Color | Device | `{DF8D96FD-9BC7-4A98-B9E2-C8B2FF92B892}` |

## 2. Changelog

**Version 2.23:**

> [!IMPORTANT]
> Lehnt ein Gerät die Anmeldung ab (z. B. falsches oder geändertes Kennwort), versucht das Modul es nach 60 Sekunden noch einmal und meldet danach nicht mehr automatisch an. Die Instanz zeigt dann den neuen Status `Anmeldung abgelehnt`. Nach dem Korrigieren der Zugangsdaten die Konfiguration übernehmen, dann wird wieder angemeldet.
> Debug-Mitschnitte aus älteren Versionen können Passwörter (Discovery), Hashes und Tokens enthalten und sollten nicht weitergegeben werden.

**Neue Funktionen und Aktionen**

- tapo Hub Device: Bewegungsmelder T100 und Wasserlecksensor T300 werden unterstützt (nach Daten anderer Projekte, noch nicht an Geräten getestet).

**Neue Statusvariablen**

- tapo Hub Device (T100): Bewegung. (T300): Wasserleck und Alarm.

**Geändertes Verhalten**

- Anmeldung: Bei abgelehnter Anmeldung wird nicht mehr im Sekundentakt neu angemeldet, damit sich Geräte nicht selbst sperren. Neuer Status `Anmeldung abgelehnt` (202).
- tapo Hub IO 200: Der Zustand aller angelernten Geräte wird mit einer Anfrage statt einer Anfrage je Gerät gelesen, die Fähigkeiten des Hubs (Sirene, LED, SD-Karte) höchstens alle 30 Sekunden. Kontakt- und Bewegungsmelder lassen sich dadurch mit einem kurzen Leseintervall (z. B. 2 Sekunden) zeitnah abfragen.
- Passwörter, Hashes und Tokens werden nicht mehr im Debug ausgegeben.

**Fehlerbehebungen**

- tapo Discovery: Bei mehreren Netzwerkadaptern (z. B. zusätzlich virtuelle Adapter) wurden keine Geräte gefunden.
- tapo Discovery: Das Passwort erschien im Debug im Klartext.
- tapo Hub IO 200: `TAPOSH_StartSiren` lieferte immer `false`.
- Instanzen konnten beim Neuladen des Moduls mit einem Fehler abbrechen.
**Version 2.22:**  

**Neue Funktionen und Aktionen**  

- tapo Hub IO 200: Sirene des Hubs kann per PHP-Funktion `TAPOSH_StartSiren` gestartet werden.  

**Fehlerbehebungen**  

- tapo Camera: Diverse Fehler behoben.  

**Version 2.21:**  

- tapo Hub IO 200: Beim Abfragen des Status wurde die falsche Anfrage an den Hub gesendet.  

**Version 2.20:**  

- Kamera und Hub 200 Modul ergänzt.  
- Unterstützung für HTTPS+AES (Protocol 3) Geräte hinzugefügt.  
- Dokumentation für tapo Camera Module ergänzt.  

**Version 2.10:**  

- Symcon 9.0 ist Voraussetzung.  
- UP-Aktoren S110 und S112 werden unterstützt.  

**Version 2.02:**  

- Bessere Fehlermeldung wenn versucht wird ein inaktives Gerät zu bedienen.  

**Version 2.00:**  

- Unterstützung für IP-Symcon 8.1 hinzugefügt.  
- Diverse kleinere Anpassungen und Verbesserungen.
- Dokumentation aktualisiert.
- Umstellung von Profile auf Darstellungen.
- Hub Konfigurator war nicht mehr funktionsfähig, da Filterung der Instanzen nicht korrekt war.

**Version 1.70:**  

- tapo Stripe Color ergänzt.  

**Version 1.65:**  

- Smart Hubs werden unterstützt.  
- Geräte von Smart Hubs ergänzt.  
- Diverse Netzwerk Geräte ergänzt.  

**Version 1.61**:  

- P300 Steckdosenleiste ergänzt.  
- Refactoring der 1.60 um weiterer Geräte und den Smart Hub zu integrieren  
  
**Version 1.60:**  

- Discovery Modul zum einfachen auffinden von Geräten im Netzwerk ergänzt  
- tapo Light Modul ergänzt  
- Refactoring der 1.50 um weiterer Geräte und den Smart Hub zu integrieren  

**Version 1.50:**  

- Refactoring der 1.40  

**Version 1.40:**  

- Refactoring der 1.30  
- Neu Verbinden überarbeitet  
- Leseintervall wurde nicht gesetzt, wenn Gerät offline war, somit auch nie ein automatischer Reconnect wenn Gerät online ging  
- War der Gerätename leer und `Instanz automatisch umbenennen` aktiv, so wurde der Name der Instanz gelöscht und es war ein `Unbenanntes Objekt`  
- Cookie sollte sich jetzt automatisch verlängern und nicht mehr statisch sein (Errorcode 9999)

**Version 1.30:**  

- Neue Verschlüsselung wird unterstützt

**Version 1.20:**  

- Laufzeit wurde falsch berechnet und nicht als UTC abgelegt  
- Session Timeout wird abgefangen und ein automatischer reconnect wird versucht
- Fehlerbehandlung verbessert  
  
**Version 1.10:**  

- Energiemessung von P110 ergänzt  
  
**Version 1.00:**  

- Release Version für Symcon 6.1  

## 3. Spenden

  Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](https://paypal.me/Nall4chan)  

[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)  

# 6. Lizenz

IPS-Modul:  
[Custom NC-SA](LICENSE)  

Fremdquellen:  

- [phpseclib](https://github.com/phpseclib/phpseclib) (Verschlüsselung), enthalten unter `libs/phpseclib`, MIT-Lizenz, Copyright (c) 2011-2019 TerraFrost und weitere Mitwirkende (siehe `libs/phpseclib/LICENSE`).  
