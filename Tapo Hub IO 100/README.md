[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)](https://community.symcon.de/t/modul-tp-link-tapo-smarthome/131865)
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FtapoSmartHome%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v81-v90-q1-2026/)  
[![License](https://img.shields.io/badge/License-Custom--NC--SA-green.svg)](#10-lizenz)
[![Check Style](https://github.com/Nall-chan/tapoSmartHome/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)
[![Run Tests](https://github.com/Nall-chan/tapoSmartHome/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/tapo-SmartHome/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#2-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#2-spenden)  

# tapo Hub IO 100  <!-- omit in toc -->

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

- Anbindung der Tapo Hubs KH100 und H100 (mit integrierter Sirene) über das lokale Netzwerk.  
- Gateway für die am Hub angelernten Funk-Geräte. Die Geräte werden über den [tapo Hub Configurator](../Tapo%20Hub%20Configurator/README.md) als [tapo Hub Device](../Tapo%20Hub%20Device/README.md)-Instanzen angelegt.  

**Hinweis**: Der Hub muss in der TP-Link/Tapo Cloud registriert sein, damit die lokale Anmeldung funktioniert!  

Für den Hub H200 ist das Modul [tapo Hub IO 200](../Tapo%20Hub%20IO%20200/README.md) zu verwenden.  

## 2. Voraussetzungen

- IP-Symcon ab Version 9.0  
- Tapo Hub KH100 oder H100 im lokalen Netzwerk, in der TP-Link/Tapo Cloud angemeldet  

## 3. Software-Installation

- Dieses Modul ist Bestandteil der [tapo SmartHome-Library](../README.md#3-software-installation).  

## 4. Einrichten der Instanzen in IP-Symcon

Eine einfache Einrichtung ist über die [Discovery-Instanz](../Tapo%20Discovery/README.md) möglich. Diese legt zum Hub auch den [tapo Hub Configurator](../Tapo%20Hub%20Configurator/README.md) an.  
Bei der manuellen Einrichtung ist das Modul im Dialog `Instanz hinzufügen` unter dem Hersteller `TP-Link` zu finden.  

**Benutzername und Passwort sind die Cloud/App Zugangsdaten!**  

| Eigenschaft                    | Name        | Typ     | Standardwert | Beschreibung                                                                 |
| ------------------------------ | ----------- | ------- | ------------ | ---------------------------------------------------------------------------- |
| Aktiv                          | Open        | boolean | false        | Verbindung zum Hub herstellen                                                |
| TLS                            | Protocol    | string  | `http://`    | `http://` oder `https://` (die Discovery-Instanz trägt den passenden Wert ein) |
| Host                           | Host        | string  | leer         | IP-Adresse oder Name des Hubs                                                |
| MAC Adresse                    | Mac         | string  | leer         | MAC Adresse des Hubs (wird von der Discovery-Instanz zur Zuordnung benötigt) |
| Benutzername                   | Username    | string  | leer         | Benutzername für die Anmeldung (TP-Cloud Benutzername: eMail-Adresse)        |
| Passwort                       | Password    | string  | leer         | Passwort für die Anmeldung (TP-Cloud Passwort)                               |
| Verschlüsselungstyp            | EncryptType | string  | `KLAP`       | `KLAP` oder `AES`, wird von der Discovery-Instanz passend zum Gerät eingetragen |
| Leseintervall                  | Interval    | integer | 5            | Intervall der Abfrage in Sekunden, `0` deaktiviert die zyklische Abfrage     |
| Instanz automatisch umbenennen | AutoRename  | boolean | false        | Instanz erhält den Namen, welcher in der App vergeben wurde                  |

Beispiel:  

```php
IPS_SetProperty(12345, 'Host', '192.168.0.10');
IPS_SetProperty(12345, 'Open', true);
IPS_ApplyChanges(12345);
```

**Instanz automatisch umbenennen (`AutoRename`):** Die Funktion ist standardmäßig ausgeschaltet. Ist sie aktiv, erhält die Instanz bei der Abfrage des Gerätes den Namen, der in der tapo-App vergeben wurde, sofern er sich vom Namen der Instanz unterscheidet. Ein selbst vergebener Instanzname wird dabei überschrieben, Statusvariablen werden nicht umbenannt.  

## 5. Statusvariablen

Die Statusvariablen werden automatisch angelegt und richten sich nach den Fähigkeiten des Hubs. Das Löschen einzelner kann zu Fehlfunktionen führen.  

> [!NOTE]  
> Die Statusvariablen des Hub IO 100 sind noch nicht an einem echten KH100/H100 geprüft (siehe `STATUS.md`). Das Modul arbeitet wie [tapo Hub IO 200](../Tapo%20Hub%20IO%20200/README.md#5-statusvariablen), nur ohne die Funktion `StartSiren`.  

## 6. Visualisierung

### Kachel Visualisierung

Die Statusvariablen der Instanz werden in der Kachel-Visualisierung als Liste dargestellt. Bedienbare Variablen können direkt in der Kachel geschaltet werden.  

### WebFront Visualisierung

Die Statusvariablen können im klassischen WebFront wie jede andere Variable angezeigt und bedient werden.  
Teilweise werden Font-Awesome-Symbole verwendet, diese zeigt das klassische WebFront nicht an.  

## 7. PHP-Befehlsreferenz

```php
bool TAPOSH_RequestState(integer $InstanzID);
```

Liest den Status des Hubs und der angelernten Geräte und aktualisiert die Statusvariablen. Rückgabewert `true` bei Erfolg.  

Beispiel:  

```php
TAPOSH_RequestState(12345);
```

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
