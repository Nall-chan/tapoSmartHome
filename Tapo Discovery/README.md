[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-Custom--NC--SA-green.svg)](#10-lizenz)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  

# tapo Discovery  <!-- omit in toc -->

## Inhaltsverzeichnis <!-- omit in toc -->

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
- [5. Statusvariablen](#5-statusvariablen)
- [6. Visualisierung](#6-visualisierung)
- [7. PHP-Befehlsreferenz](#7-php-befehlsreferenz)
- [8. Aktionen](#8-aktionen)
- [9. Anhang](#9-anhang)
  - [1. Changelog](#1-changelog)
  - [2. Spenden](#2-spenden)
- [10. Lizenz](#10-lizenz)

## 1. Funktionsumfang

- Auffinden von tapo SmartHome Geräten im Netzwerk.  
- Einfaches Anlegen von dem benötigten Geräte Instanzen, oder den Konfiguratoren mit Hub IO Instanzen.  

## 2. Voraussetzungen

- Symcon ab Version 9.0

## 3. Software-Installation

- Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  
  
## 4. Einrichten der Instanzen in IP-Symcon

Eine einfache Einrichtung ist über diese Instanz möglich.  
Bei der installation aus dem Store wird das anlegen der Instanz automatisch angeboten.  

Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter den Hersteller `TP-Link` zu finden.  
![Instanz hinzufügen](../imgs/module.png)  

Alternativ ist es auch in der Liste alle Discovery-Module aufgeführt.  
![Instanz hinzufügen](../imgs/module_discovery.png)  

Die Suche im Netzwerk nutzt einen Broadcast auf Port 20002.

> [!NOTE]
> Ein Broadcast wird nicht in andere Subnetze weitergeleitet. Geräte und Hubs in einem anderen Subnetz (VLAN) werden daher nicht gefunden, unabhängig vom Gerätetyp. Solche Instanzen sind manuell anzulegen.

> [!NOTE]
> Geräte mit neuerer Firmware, die das Verschlüsselungsverfahren `TPAP` nutzen, werden in der Liste rot markiert und mit einem Hinweis angezeigt. Sie werden aktuell nicht unterstützt und lassen sich nicht anlegen.

Damit Symcon mit den Geräten kommunizieren können, müssen diese in der TP-Link Cloud angemeldet und registriert sein.  
Die entsprechenden Cloud-Zugangsdaten sind in der Discovery-Instanz einzutragen, damit die Instanzen gleich mit den korrekten Daten erstellt werden.  

### Konfigurationsseite <!-- omit in toc -->

Über das selektieren eines Eintrages in der Tabelle und betätigen des dazugehörigen `Erstellen` Button,  
wird automatisch eine [Geräte-Instanz](../README.md#2-netzwerk-geräte-instanzen) erzeugt, sofern es sich **nicht** um einen Smart Hub handelt.  
Bei einem Smart Hub wird eine [Konfigurator-Instanz](../Tapo%20Hub%20Configurator/README.md) und eine Hub IO Instanz ([IO 100](../Tapo%20Hub%20IO%20100/README.md) bzw. [IO 200](../Tapo%20Hub%20IO%20200/README.md)) erzeugt.  

![Discovery](../imgs/conf_discovery.png)  
**Benutzername und Passwort sind die Cloud/App Zugangsdaten!**  
Die Instanz hat keine Eigenschaften, die Zugangsdaten werden intern gespeichert und beim Erstellen der Instanzen übernommen.  

## 5. Statusvariablen

Dieses Modul erstellt keine Statusvariablen.  

## 6. Visualisierung

Diese Instanz besitzt keine Statusvariablen und keine eigene Visualisierung.  

## 7. PHP-Befehlsreferenz

Dieses Modul besitzt keine Instanz-Funktionen.

## 8. Aktionen

**Grundsätzlich können alle bedienbaren Statusvariablen als Ziel einer [`Aktion`](https://www.symcon.de/service/dokumentation/konzepte/automationen/ablaufplaene/aktionen/) mit `Auf Wert schalten` angesteuert werden, so dass hier keine speziellen Aktionen benutzt werden müssen.**

Es gibt keine speziellen Aktionen für dieses Modul.  

## 9. Anhang

### 1. Changelog

[Changelog der Library](../README.md#2-changelog)

### 2. Spenden

  Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](https://paypal.me/Nall4chan)  

[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)  

## 10. Lizenz

  IPS-Modul:  
  [Custom NC-SA](../LICENSE)  
  