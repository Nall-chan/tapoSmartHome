[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-1.70-blue.svg)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Version](https://img.shields.io/badge/Symcon%20Version-6.1%20%3E-green.svg)](https://www.symcon.de/service/dokumentation/installation/migrationen/v60-v61-q1-2022/)  
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![Spenden](https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_SM.gif)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  
# tapo Smart Multi Sockets<!-- omit in toc -->

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
- [5. Statusvariablen und Profile](#5-statusvariablen-und-profile)
  - [Statusvariablen](#statusvariablen)
  - [Profile](#profile)
- [6. PHP-Befehlsreferenz](#6-php-befehlsreferenz)
- [7. Aktionen](#7-aktionen)
- [8. Anhang](#8-anhang)
  - [1. Changelog](#1-changelog)
  - [2. Spenden](#2-spenden)
- [9. Lizenz](#9-lizenz)


## 1. Funktionsumfang

 - Instanz für Smarte WiFi Verlängerung
 
## 2. Voraussetzungen

- IP-Symcon ab Version 6.1 

## 3. Software-Installation

* Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  
  
## 4. Einrichten der Instanzen in IP-Symcon

Eine einfache Einrichtung ist die [Discovery-Instanz](../Tapo%20Discovery/README.md) möglich.  

Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter den Hersteller `TP-Link` zu finden.  
![Instanz hinzufügen](../imgs/module.png)  

Damit Symcon mit den Geräten kommunizieren können, müssen diese in der TP-Link Cloud angemeldet und registriert sein.  
Die entsprechenden Cloud-Zugangsdaten, die MAC-Adresse und das genutzte Protokoll werden beim anlegen durch die [Discovery-Instanz](../Tapo%20Discovery/README.md) automatisch eingetragen.

 ### Konfigurationsseite <!-- omit in toc -->

![Config](../imgs/conf_device.png)  

**Benutzername und Passwort sind die Cloud/App Zugangsdaten!**  

| Name       | Text                           | Beschreibung                                                           |
| ---------- | ------------------------------ | ---------------------------------------------------------------------- |
| Open       | Aktiv                          | Verbindung zu Gerät herstellen                                         |
| Host       | Host                           | Adresse des Gerätes                                                    |
| Mac        | MAC Adresse                    | MAC Adresse des Gerätes (benötigt die Discovery-Instanz zur Zuordnung) |
| Protocol   | Protokoll                      | Genutztes Kommunikationsprotokoll (AES oder KLAP)                      |
| Username   | Benutzername                   | Benutzername für die Anmeldung (TP-Cloud Benutzername: eMail-Adresse)  |
| Password   | Passwort                       | Passwort für die Anmeldung (TP-Cloud Passwort)                         |
| Interval   | Leseintervall                  | Intervall der Abfrage von Status und Energiewerten (in Sekunden)       |
| AutoRename | Instanz automatisch umbenennen | Instanz erhält den Namen, welcher in der App vergeben wurde            |

## 5. Statusvariablen und Profile

Die Statusvariablen werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

### Statusvariablen
| Ident                | Name                                   | Typ     | Profil              |
| -------------------- | -------------------------------------- | ------- | ------------------- |
| Pos_1_device_on      | Smarte Steckdose 1 - Status            | boolean | ~Switch             |
| Pos_2_device_on      | Smarte Steckdose 2 - Status            | boolean | ~Switch             |
| Pos_3_device_on      | Smarte Steckdose 3 - Status            | boolean | ~Switch             |
| Pos_1_on_time_string | Smarte Steckdose 1 - On time           | string  |
| Pos_2_on_time_string | Smarte Steckdose 2 - On time           | string  |
| Pos_3_on_time_string | Smarte Steckdose 3 - On time           | string  |
| Pos_1_on_time        | Smarte Steckdose 1 - On time (seconds) | integer | Tapo.RuntimeSeconds |
| Pos_2_on_time        | Smarte Steckdose 2 - On time (seconds) | integer | Tapo.RuntimeSeconds |
| Pos_3_on_time        | Smarte Steckdose 3 - On time (seconds) | integer | Tapo.RuntimeSeconds |
| rssi                 | Rssi                                   | integer |

### Profile

| Name                | Typ     | genutzt von                                |
| ------------------- | ------- | ------------------------------------------ |
| Tapo.RuntimeSeconds | integer | Pos_1_on_tim, Pos_2_on_time, Pos_3_on_time |

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

<a href="https://www.paypal.com/donate?hosted_button_id=G2SLW2MEMQZH2" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>

[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share) 


## 9. Lizenz

  IPS-Modul:  
  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
  