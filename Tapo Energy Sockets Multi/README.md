[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  

# tapo Smart Multi Energy Sockets<!-- omit in toc -->

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
- [5. Statusvariablen](#5-statusvariablen)
- [6. PHP-Befehlsreferenz](#6-php-befehlsreferenz)
- [7. Aktionen](#7-aktionen)
- [8. Anhang](#8-anhang)
  - [1. Changelog](#1-changelog)
  - [2. Spenden](#2-spenden)
- [9. Lizenz](#9-lizenz)

## 1. Funktionsumfang

- Instanz für Smarte WiFi Module mit mehreren Kanälen und Energiemessung

## 2. Voraussetzungen

- Symcon ab Version 9.0

## 3. Software-Installation

- Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  
  
## 4. Einrichten der Instanzen in IP-Symcon

Eine einfache Einrichtung ist die [Discovery-Instanz](../Tapo%20Discovery/README.md) möglich.  

Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter den Hersteller `TP-Link` zu finden.  
![Instanz hinzufügen](../imgs/module.png)  

Damit Symcon mit den Geräten kommunizieren können, müssen diese in der TP-Link Cloud angemeldet und registriert sein.  
Die entsprechenden Cloud-Zugangsdaten, die MAC-Adresse und das genutzte Protokoll werden beim anlegen durch die [Discovery-Instanz](../Tapo%20Discovery/README.md) automatisch eingetragen.

### Konfigurationsseite <!-- omit in toc -->

![Config](../imgs/conf_device.png)  

**Benutzername und Passwort sind die Cloud/App Zugangsdaten!**  

| Name        | Text                           | Beschreibung                                                           |
| ----------- | ------------------------------ | ---------------------------------------------------------------------- |
| Open        | Aktiv                          | Verbindung zu Gerät herstellen                                         |
| Host        | Host                           | Adresse des Gerätes                                                    |
| Mac         | MAC Adresse                    | MAC Adresse des Gerätes (benötigt die Discovery-Instanz zur Zuordnung) |
| Protocol    | Protokoll                      | http:// oder https://                                                  |
| EncryptType | Verschlüsselungstyp            | Genutztes Kommunikationsprotokoll (AES oder KLAP)                      |
| Username    | Benutzername                   | Benutzername für die Anmeldung (TP-Cloud Benutzername: eMail-Adresse)  |
| Password    | Passwort                       | Passwort für die Anmeldung (TP-Cloud Passwort)                         |
| Interval    | Leseintervall                  | Intervall der Abfrage von Status und Energiewerten (in Sekunden)       |
| AutoRename  | Instanz automatisch umbenennen | Instanz erhält den Namen, welcher in der App vergeben wurde            |

## 5. Statusvariablen

Die Statusvariablen werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.
Je nach Modus Rollladenaktor oder Schaltaktor können unterschiedliche Statusvariablen angelegt werden.

| Ident                   | Name                                | Typ     |
| ----------------------- | ----------------------------------- | ------- |
| Pos_1_device_on         | Kanal 1 - Status                    | boolean |
| Pos_2_device_on         | Kanal 2 - Status                    | boolean |
| Pos_1_on_time_string    | Kanal 1 - On time                   | string  |
| Pos_2_on_time_string    | Kanal 2 - On time                   | string  |
| Pos_1_on_time           | Kanal 1 - On time (seconds)         | integer |
| Pos_2_on_time           | Kanal 2 - On time (seconds)         | integer |
| Pos_3_target_pos        | KAnal 3 - Zielposition (0-100)      | integer |
| Pos_3_motor_status      | Kanal 3 - Motorstatus               | string  |
| Pos_1_overheat_status   | Kanal 1 - Überhitzt                 | boolean |
| Pos_2_overheat_status   | Kanal 2 - Überhitzt                 | boolean |
| Pos_3_overheat_status   | Kanal 3 - Überhitzt                 | boolean |
| Pos_1_today_runtime_raw | Kanal 1 - Laufzeit Heute  (Minuten) | integer |
| Pos_2_today_runtime_raw | Kanal 2 - Laufzeit Heute  (Minuten) | integer |
| Pos_3_today_runtime_raw | Kanal 3 - Laufzeit Heute  (Minuten) | integer |
| Pos_1_today_energy      | Kanal 1 - Energie Heute             | float   |
| Pos_2_today_energy      | Kanal 2 - Energie Heute             | float   |
| Pos_3_today_energy      | Kanal 3 - Energie Heute             | float   |
| Pos_1_month_runtime_raw | Kanal 1 - Laufzeit Monat (Minuten)  | integer |
| Pos_2_month_runtime_raw | Kanal 2 - Laufzeit Monat (Minuten)  | integer |
| Pos_3_month_runtime_raw | Kanal 3 - Laufzeit Monat (Minuten)  | integer |
| Pos_1_month_energy      | Kanal 1 - Energie Monat             | float   |
| Pos_2_month_energy      | Kanal 2 - Energie Monat             | float   |
| Pos_3_month_energy      | Kanal 3 - Energie Monat             | float   |
| rssi                    | Rssi                                | integer |

## 6. PHP-Befehlsreferenz

``` php
boolean TAPOSH_SwitchMode(integer $InstanzID, bool $State);
```

---  

``` php
boolean TAPOSH_SwitchModeEx(integer $InstanzID, bool $State, integer $Delay);
```

---  

``` php
boolean TAPOSH_RequestState(integer $InstanzID);
```

---  

``` php
array|false TAPOSH_GetDeviceInfo(integer $InstanzID);
```

## 7. Aktionen

Es gibt keine speziellen Aktionen für dieses Modul.  

## 8. Anhang

### 1. Changelog

[Changelog der Library](../README.md#1-changelog)

### 2. Spenden

  Die Library ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](https://paypal.me/Nall4chan)  

[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)  

## 9. Lizenz

  IPS-Modul:  
  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
  