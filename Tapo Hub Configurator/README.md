[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-Custom--NC--SA-green.svg)](#10-lizenz)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  

# tapo Hub Configurator  <!-- omit in toc -->

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

- Liest die am Hub angelernten Geräte aus (`get_child_device_list`).  
- Legt für die gefundenen Geräte [tapo Hub Device](../Tapo%20Hub%20Device/README.md)-Instanzen an und gleicht sie mit bereits vorhandenen Instanzen ab.  

## 2. Voraussetzungen

- IP-Symcon ab Version 9.0  
- Eine [tapo Hub IO 100](../Tapo%20Hub%20IO%20100/README.md)- oder [tapo Hub IO 200](../Tapo%20Hub%20IO%20200/README.md)-Instanz als übergeordnete Instanz. Diese wird von der [Discovery-Instanz](../Tapo%20Discovery/README.md) zusammen mit dem Konfigurator angelegt.  

## 3. Software-Installation

- Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  

## 4. Einrichten der Instanzen in IP-Symcon

Der Konfigurator wird bei der Einrichtung über die [Discovery-Instanz](../Tapo%20Discovery/README.md) automatisch mit angelegt.  
Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter dem Hersteller `TP-Link` zu finden.  

Der Konfigurator hat keine eigenen Eigenschaften. Die Tabelle zeigt die Geräte des Hubs:  

| Spalte    | Beschreibung                                       |
| --------- | -------------------------------------------------- |
| Geräte ID | ID, mit welcher der Hub das Gerät intern führt     |
| Name      | Name aus der App bzw. Name der vorhandenen Instanz |
| Typ       | Gerätetyp des angelernten Gerätes                  |
| Modell    | Modellbezeichnung (z. B. T310)                     |

Über die Schaltfläche `Erstellen` wird zu einem Gerät eine Instanz angelegt.  

## 5. Statusvariablen

Der Konfigurator besitzt keine Statusvariablen.  

## 6. Visualisierung

Diese Instanz besitzt keine Statusvariablen und keine eigene Visualisierung.  

## 7. PHP-Befehlsreferenz

Der Konfigurator besitzt keine öffentlichen PHP-Funktionen.  

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
