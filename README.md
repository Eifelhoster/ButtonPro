# Eifelhoster Buttons Pro

**Version:** 3.1.0  
**Autor:** Michael Krämer · [eifelhoster.de](https://eifelhoster.de)  
**Lizenz:** GPL-2.0+

---

## Beschreibung

**Eifelhoster Buttons Pro** fügt einen grafisch gestalteten Button-Editor in den WordPress Classic Editor (TinyMCE) **und in Elementor** ein.  
Über einen Klick auf die Toolbar-Schaltfläche im Classic Editor öffnet sich ein komfortabler Dialog, in dem alle Eigenschaften des Buttons konfiguriert werden können.  
In Elementor steht das Widget **eh-button-Pro** unter der Rubrik **Eifelhoster** zur Verfügung.  
Die Standardwerte für alle Einstellungen können unter **Einstellungen → ButtonPro** festgelegt werden.

---

## Funktionen

### Button-Optionen
| Kategorie | Optionen |
|-----------|---------|
| **Text & Schrift** | Schriftart, -größe, Fett, Kursiv, Innenabstand, **Gesamtbreite (Standard: 300 px)** |
| **Farben & Hover** | Hintergrundfarbe (normal/hover), Textfarbe (normal/hover), Grow-Faktor bei Hover |
| **Symbol (Icon)** | Dashicon (alle WP-Dashicons durchsuchbar) oder Mediendatei; Größe, Abstand, Position (vor/hinter dem Text), **Symbolfarbe (HEX-Farbwähler)** |
| **Rahmen & Schatten** | Stärke, Stil, Farbe, Radius; Schatten (X/Y/Blur/Spread); **Schattenfarbe (HEX-Farbwähler)** |
| **Link & Ziel** | URL · E-Mail (Adresse + Betreff + Text) · Mediendatei · **Inhalt (Seiten, Beiträge, CPTs)**; Öffnen im gleichen oder neuen Tab |

### Admin-Seite
Unter **Einstellungen → ButtonPro** können für alle o.g. Einstellungen Standardwerte vorgegeben werden.

### Elementor-Widget
Das Widget **eh-button-Pro** steht in der Elementor-Seitenleiste unter der Rubrik **Eifelhoster** zur Verfügung und bietet alle Button-Optionen als native Elementor-Controls.

---

## Installation

1. Repo als ZIP herunterladen (oder das fertige Plugin-ZIP verwenden).  
2. In WordPress unter **Plugins → Installieren → ZIP hochladen** installieren.  
3. Plugin aktivieren.  
4. Standardwerte unter **Einstellungen → ButtonPro** anpassen.  
5. **Classic Editor:** Im Beitrags-/Seiten-Editor den Button **⬛ Button** in der TinyMCE-Toolbar anklicken.  
6. **Elementor:** Im Elementor-Editor das Widget **eh-button-Pro** aus der Rubrik **Eifelhoster** per Drag & Drop einfügen.

---

## Shortcode

Der Plugin erzeugt folgenden Shortcode (alle Attribute optional – fehlende Werte werden aus den Standardeinstellungen übernommen):

```
[eifelhoster_button
  text="Jetzt klicken"
  link_type="url"
  url="https://beispiel.de"
  target="_blank"
  font_family="Arial"
  font_size="16"
  font_bold="1"
  font_italic="0"
  bg_color="#007bff"
  bg_hover_color="#0056b3"
  text_color="#ffffff"
  text_hover_color="#ffffff"
  hover_grow="1.05"
  padding_v="10"
  padding_h="20"
  button_width="300"
  icon_type="dashicon"
  icon="arrow-right-alt"
  icon_size="20"
  icon_spacing="8"
  icon_position="before"
  icon_color="#ffffff"
  border_width="0"
  border_style="solid"
  border_color="#000000"
  border_radius="4"
  shadow_enabled="1"
  shadow_x="0"
  shadow_y="2"
  shadow_blur="4"
  shadow_spread="0"
  shadow_color="#000000"
]
```

### E-Mail-Link

```
[eifelhoster_button
  text="E-Mail senden"
  link_type="email"
  email="info@beispiel.de"
  email_subject="Anfrage"
  email_body="Hallo,"
]
```

### Mediendatei-Link

```
[eifelhoster_button
  text="PDF herunterladen"
  link_type="media"
  media_url="https://beispiel.de/wp-content/uploads/dokument.pdf"
  target="_blank"
]
```

### Inhalt-Link (Seite / Beitrag / CPT)

```
[eifelhoster_button
  text="Zur Kontaktseite"
  link_type="content"
  content_id="42"
]
```

---

## Dateistruktur

```
eifelhoster-buttons-pro/               ← Plugin-Verzeichnis
├── eifelhoster-buttons-pro.php        ← Haupt-Plugin-Datei
├── includes/
│   ├── class-ebp-helpers.php          ← Hilfsfunktionen & Dashicons-Liste
│   ├── class-ebp-admin.php            ← Admin-Seite (Einstellungen → ButtonPro)
│   ├── class-ebp-shortcode.php        ← Shortcode-Renderer
│   ├── class-ebp-editor.php           ← TinyMCE-Integration & Dialog-HTML
│   ├── class-ebp-elementor.php        ← Elementor-Integration (Kategorie + Widget-Loader)
│   └── class-ebp-elementor-widget.php ← Elementor-Widget „eh-button-Pro"
├── assets/
│   ├── css/
│   │   ├── ebp-admin.css              ← Admin- & Dialog-Styles
│   │   └── ebp-frontend.css           ← Frontend-Styles
│   └── js/
│       ├── ebp-tinymce-plugin.js      ← TinyMCE-Plugin
│       ├── ebp-dialog.js              ← Dialog-UI-JavaScript
│       └── ebp-admin.js               ← Admin-Seite-JavaScript
└── README.md
```

---

*Eifelhoster Buttons Pro v3.1.0 – © 2024 Michael Krämer · eifelhoster.de*
