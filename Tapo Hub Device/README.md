[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-Custom--NC--SA-green.svg)](#10-lizenz)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  

# tapo Hub Device  <!-- omit in toc -->

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
- [5. Statusvariablen](#5-statusvariablen)
- [6. Visualisierung](#6-visualisierung)
  - [Kachel Visualisierung](#kachel-visualisierung)
  - [WebFront Visualisierung](#webfront-visualisierung)
- [7. PHP-Befehlsreferenz](#7-php-befehlsreferenz)
- [8. Aktionen](#8-aktionen)
- [9. Anhang](#9-anhang)
  - [1. Changelog](#1-changelog)
  - [2. Spenden](#2-spenden)
- [10. Lizenz](#10-lizenz)

## 1. Funktionsumfang

Instanz für Geräte, welche über einen Funk-Hub (H100, KH100, H200) angebunden sind.  
Die Statusvariablen richten sich nach der Kategorie, die der Hub für das Gerät meldet:  

- `trv`: Heizkörperthermostat (KE100)  
- `temp-hmdt-sensor`: Temperatur- und Feuchtigkeitssensor (T310)  
- `contact-sensor`: Kontaktsensor (T110)  
- `motion-sensor`: Bewegungsmelder (T100)  
- `water-leak-sensor`: Wasserlecksensor (T300)  

> [!NOTE]  
> Bewegungsmelder T100 und Wasserlecksensor T300 sind noch an keinem Gerät getestet. Rückmeldungen mit einem Debug-Mitschnitt sind willkommen.  

> [!NOTE]  
> Andere Kategorien (z. B. Taster S200) werden noch nicht unterstützt. Für sie werden keine Statusvariablen angelegt.  

## 2. Voraussetzungen

- IP-Symcon ab Version 9.0  
- Eine [tapo Hub IO 100](../Tapo%20Hub%20IO%20100/README.md)- oder [tapo Hub IO 200](../Tapo%20Hub%20IO%20200/README.md)-Instanz als übergeordnete Instanz.  

## 3. Software-Installation

- Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  

## 4. Einrichten der Instanzen in IP-Symcon

Eine einfache Einrichtung ist über den [tapo Hub Configurator](../Tapo%20Hub%20Configurator/README.md) möglich.  
Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter dem Hersteller `TP-Link` zu finden.  

| Eigenschaft                    | Name       | Typ     | Standardwert | Beschreibung                                                                       |
| ------------------------------ | ---------- | ------- | ------------ | ---------------------------------------------------------------------------------- |
| Geräte ID                      | DeviceId   | string  | leer         | ID, mit welcher der Hub das Gerät führt (siehe Spalte `Geräte ID` im Konfigurator) |
| Instanz automatisch umbenennen | AutoRename | boolean | false        | Instanz erhält den Namen, welcher in der App vergeben wurde                        |

Beispiel (die Geräte ID ist ein Platzhalter):  

```php
IPS_SetProperty(12345, 'DeviceId', 'XXXXXXXXXXXXXXXX');
IPS_SetProperty(12345, 'AutoRename', false);
IPS_ApplyChanges(12345);
```

**Instanz automatisch umbenennen (`AutoRename`):** Die Funktion ist standardmäßig ausgeschaltet. Ist sie aktiv, erhält die Instanz bei der Abfrage des Gerätes den Namen, der in der tapo-App vergeben wurde, sofern er sich vom Namen der Instanz unterscheidet. Ein selbst vergebener Instanzname wird dabei überschrieben, Statusvariablen werden nicht umbenannt.  

## 5. Statusvariablen

Die Statusvariablen werden automatisch angelegt, sobald der Hub Daten des Gerätes liefert. Das Löschen einzelner kann zu Fehlfunktionen führen.  

| Ident               | Name                 | Kategorie             | Bedienbar |
| ------------------- | -------------------- | --------------------- | --------- |
| status              | Online status        | alle                  | nein      |
| battery_percentage  | Battery              | alle                  | nein      |
| at_low_battery      | Low battery          | alle                  | nein      |
| rssi                | Rssi                 | alle                  | nein      |
| current_temp        | Current temperature  | trv, temp-hmdt-sensor | nein      |
| current_humidity    | Current humidity     | temp-hmdt-sensor      | nein      |
| target_temp         | Setpoint temperature | trv                   | ja        |
| frost_protection_on | Frost protection     | trv                   | ja        |
| child_protection    | Child Protection     | trv                   | ja        |
| trv_states          | State                | trv                   | nein      |
| open                | State                | contact-sensor        | nein      |
| detected            | Motion               | motion-sensor (ungetestet) | nein |
| water_leak_status   | Water leak           | water-leak-sensor (ungetestet) | nein |
| in_alarm            | Alarm                | water-leak-sensor (ungetestet) | nein |

> [!NOTE]  
> Die Idents stammen aus dem Quelltext, die Namen sind die Originalnamen im Code und werden in der Console übersetzt angezeigt. Variablentypen und Bedienbarkeit sind noch nicht für alle Kategorien gegen eine Live-Instanz geprüft.  

## 6. Visualisierung

### Kachel Visualisierung

Die Statusvariablen der Instanz werden in der Kachel-Visualisierung als Liste dargestellt. Bedienbare Variablen können direkt in der Kachel geschaltet werden.  

### WebFront Visualisierung

Die Statusvariablen können im klassischen WebFront wie jede andere Variable angezeigt und bedient werden.  
Teilweise werden Font-Awesome-Symbole verwendet, diese zeigt das klassische WebFront nicht an.  

## 7. PHP-Befehlsreferenz

Das Modul besitzt keine öffentlichen PHP-Funktionen.  

## 8. Aktionen

**Grundsätzlich können alle bedienbaren Statusvariablen als Ziel einer [`Aktion`](https://www.symcon.de/service/dokumentation/konzepte/automationen/ablaufplaene/aktionen/) mit `Auf Wert schalten` angesteuert werden, so dass hier keine speziellen Aktionen benutzt werden müssen.**  

## 9. Anhang

### 1. Changelog

Siehe [Changelog der Library](../README.md#2-changelog).  

### 2. Spenden

Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

[![PayPal.Me](https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif)](https://paypal.me/Nall4chan)  
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)  

## 10. Lizenz

[Custom NC-SA](../LICENSE)  
