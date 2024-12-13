+++
title = 'Home Lab Rack'
date = 2024-11-08T17:52:01+01:00
draft = false
author = "Mika"
comments = true
categories = ["Homelab", "Hardware"]
tags = ["Server", "Netzwerk", "Virtualisierung", "Storage", "Hardware"]
+++

## Introduction

As an enthusiastic tech geek and IT enthusiast, I have decided to set up my own home lab. This will not only serve as a playground for new technologies, but also as a test and development environment for my professional projects.

After looking intensively at various configurations and hardware components, here is my final choice for a 22U server rack.

## Hardware components

### Netzwerk-Equipment

This section lists the network components that serve as the backbone for my Home Lab. These include a powerful PoE switch, a redundant backup switch and an OPNsense firewall.

| Komponente         | Modell                                   | Höheneinheiten (HE) |
| ------------------ | ---------------------------------------- | ------------------- |
| Patch Panel        | Digitus 24 Port CAT 6                    | 1U                  |
| Hauptswitch        | UniFi Switch PRO 24 PoE (USW-PRO-24-POE) | 1U                  |
| Redundanter Switch | UniFi Switch 24 (USW-24)                 | 1U                  |
| Firewall 1         | UbiQuiti Gateway UniFi Dream Machine     | 1U                  |
| Firewall           | Mikrotik Router RB5009GUG + S + IN       | 1U                  |
| Wifi               | NM-AVM-001                               | 1.33U               |

### Server systems

At the heart of my home lab are powerful servers from HPE and DELL. In addition to a virtualization host and a container server, I have also opted for a dedicated AI server. A Synology NAS serves as a data tomb.

| Komponente             | Modell                       | Höheneinheiten (HE) |
| ---------------------- | ---------------------------- | ------------------- |
| Virtualisierungsserver | HPE ProLiant DL380 Gen10     | 2U                  |
| Container-Server       | DELL EMC PowerEdge R630      | 2U                  |
| KI-Server              | HPE ProLiant DL120 Gen9      | 1U                  |
| Storage Server         | Synology DiskStation DS1522+ | 2U                  |

### Management & Monitoring

To manage and monitor the lab, I integrated high-quality components such as a KVM switch, a monitoring panel and a temperature sensor. An intelligent PDU is also used.

| Komponente        | Modell                       | Höheneinheiten (HE) |
| ----------------- | ---------------------------- | ------------------- |
| KVM Switch        | PiKVM V4 Plus                | 1U                  |
| Temperatur-Sensor | APC NetBotz Room Monitor 355 | 1U                  |
| PDU               | Legrand 19R Rack PDU         | 1U                  |

### Infrastructure

I opted for redundant UPS systems to provide a reliable power supply for the servers and network components. A dedicated fan unit provides the necessary cooling.

| Komponente           | Modell                             | Höheneinheiten (HE) |
| -------------------- | ---------------------------------- | ------------------- |
| Hauptstromversorgung | APC AP9567                         | 1U                  |
| raspi                | UM-SBC-215                         | 1.33U               |
| Lüftereinheit        | AC Infinity CLOUDPLATE T1 AIRPLATE | 1U                  |

### Rack & Accessories

Finally, I selected a high-quality 22U server rack from Rittal, including rail systems and cable management.

## Space requirement in the rack

The following table shows the planned components with their respective space requirements in the 22U rack:

| Komponente             | Modell                                   | Höheneinheiten (HE) |
| ---------------------- | ---------------------------------------- | ------------------- |
| Patch Panel            | Digitus 24 Port CAT 6                    | 1U                  |
| Hauptswitch            | UniFi Switch PRO 24 PoE (USW-PRO-24-POE) | 1U                  |
| Redundanter Switch     | UniFi Switch 24 (USW-24)                 | 1U                  |
| Firewall 1             | UbiQuiti Gateway UniFi Dream Machine     | 1U                  |
| Firewall               | Mikrotik Router RB5009GUG + S + IN       | 1U                  |
| KVM Switch             | PiKVM V4 Plus                            | 1U                  |
| Temperatur-Sensor      | APC NetBotz Room Monitor 355             | 1U                  |
| PDU                    | Legrand 19R Rack PDU                     | 1U                  |
| Hauptstromversorgung   | APC AP9567                               | 1U                  |
| raspi                  | UM-SBC-215                               | 1.33U               |
| Lüftereinheit          | AC Infinity CLOUDPLATE T1 AIRPLATE       | 1U                  |
| Virtualisierungsserver | HPE ProLiant DL380 Gen10                 | 2U                  |
| Container-Server       | DELL EMC PowerEdge R630                  | 2U                  |
| KI-Server              | HPE ProLiant DL120 Gen9                  | 1U                  |
| Storage Server         | Synology DiskStation DS1522+             | 2U                  |
| Wifi                   | NM-AVM-001                               | 1.33U               |
| Rack                   | 22U                                      | 22U                 |

Total occupied space 18,66U
