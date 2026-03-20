# Eifelhoster Buttons Pro

**Version:** 4.2.0  
**Autor:** Michael Krämer · [eifelhoster.de](https://eifelhoster.de)  
**Lizenz:** GPL-2.0+

---

## Beschreibung

**Eifelhoster Buttons Pro** fügt einen grafisch gestalteten Button-Editor in den WordPress Classic Editor (TinyMCE) ein.  
Über einen Klick auf die Toolbar-Schaltfläche öffnet sich ein komfortabler Dialog, in dem alle Eigenschaften des Buttons konfiguriert werden können.  
Die Standardwerte für alle Einstellungen können unter **Einstellungen → ButtonPro** festgelegt werden.

---

## Funktionen

### Button-Optionen
| Kategorie | Optionen |
|-----------|---------|
| **Text & Schrift** | Schriftart, -größe, Fett, Kursiv, Innenabstand |
| **Farben & Hover** | Hintergrundfarbe (normal/hover), Textfarbe (normal/hover), Grow-Faktor bei Hover |
| **Symbol (Icon)** | Dashicon (alle WP-Dashicons durchsuchbar) oder Mediendatei; Größe, Abstand, Position (vor/hinter dem Text) |
| **Rahmen & Schatten** | Stärke, Stil, Farbe, Radius; Schatten (X/Y/Blur/Spread/Farbe) |
| **Link & Ziel** | URL · E-Mail (Adresse + Betreff + Text) · Mediendatei; Öffnen im gleichen oder neuen Tab |

### Admin-Seite
Unter **Einstellungen → ButtonPro** können für alle o.g. Einstellungen Standardwerte vorgegeben werden.

---

## Installation

1. Repo als ZIP herunterladen (oder das fertige Plugin-ZIP verwenden).  
2. In WordPress unter **Plugins → Installieren → ZIP hochladen** installieren.  
3. Plugin aktivieren.  
4. Standardwerte unter **Einstellungen → ButtonPro** anpassen.  
5. Im Beitrags-/Seiten-Editor den Button **⬛ Button** in der TinyMCE-Toolbar anklicken.

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
  icon_type="dashicon"
  icon="arrow-right-alt"
  icon_size="20"
  icon_spacing="8"
  icon_position="before"
  border_width="0"
  border_style="solid"
  border_color="#000000"
  border_radius="4"
  shadow_enabled="1"
  shadow_x="0"
  shadow_y="2"
  shadow_blur="4"
  shadow_spread="0"
  shadow_color="rgba(0,0,0,0.3)"
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

---

## Dateistruktur

```
eifelhoster-buttons-pro/          ← Plugin-Verzeichnis
├── eifelhoster-buttons-pro.php   ← Haupt-Plugin-Datei
├── includes/
│   ├── class-ebp-helpers.php     ← Hilfsfunktionen & Dashicons-Liste
│   ├── class-ebp-admin.php       ← Admin-Seite (Einstellungen → ButtonPro)
│   ├── class-ebp-shortcode.php   ← Shortcode-Renderer
│   └── class-ebp-editor.php      ← TinyMCE-Integration & Dialog-HTML
├── assets/
│   ├── css/
│   │   ├── ebp-admin.css         ← Admin- & Dialog-Styles
│   │   └── ebp-frontend.css      ← Frontend-Styles
│   └── js/
│       ├── ebp-tinymce-plugin.js ← TinyMCE-Plugin
│       ├── ebp-dialog.js         ← Dialog-UI-JavaScript
│       └── ebp-admin.js          ← Admin-Seite-JavaScript
└── README.md
```

---

*Eifelhoster Buttons Pro v4.2.0 – © 2026 Michael Krämer · eifelhoster.de*
