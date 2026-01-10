[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v80-v81-q3-2025/)  
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
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
- [6. PHP-Befehlsreferenz](#6-php-befehlsreferenz)
- [7. Aktionen](#7-aktionen)
- [8. Anhang](#8-anhang)
  - [1. Changelog](#1-changelog)
  - [2. Spenden](#2-spenden)
- [9. Lizenz](#9-lizenz)

## 1. Funktionsumfang

- Auffinden von tapo SmartHome Geräten im Netzwerk.  
- Einfaches Anlegen von dem benötigten Geräte Instanzen, oder den Konfiguratoren mit Gateway Instanzen.  

## 2. Voraussetzungen

- IP-Symcon ab Version 8.1

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

Damit Symcon mit den Geräten kommunizieren können, müssen diese in der TP-Link Cloud angemeldet und registriert sein.  
Die entsprechenden Cloud-Zugangsdaten sind in der Discovery-Instanz einzutragen, damit die Instanzen gleich mit den korrekten Daten erstellt werden.  

### Konfigurationsseite <!-- omit in toc -->

Über das selektieren eines Eintrages in der Tabelle und betätigen des dazugehörigen `Erstellen` Button,  
wird automatisch eine [Geräte-Instanz](../README.md#2-geräte-instanzen) erzeugt, sofern es sich **nicht** um einen Smart Hub handelt.  
Bei einem Smart Hub wird eine [Konfigurator-Instanz](../Tapo%20Configurator/README.md) und eine, [Gateway-Instanz](../Tapo%20Gateway/README.md) erzeugt.  

![Discovery](../imgs/conf_discovery.png)  
**Benutzername und Passwort sind die Cloud/App Zugangsdaten!**  

## 5. Statusvariablen

Dieses Modul erstellt keine Statusvariablen.  

## 6. PHP-Befehlsreferenz

Dieses Modul besitzt keine Instanz-Funktionen.

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
  