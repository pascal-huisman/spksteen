# Foto's plaatsen

Zet hier je foto's neer. **De bestandsnaam maakt niet uit** — de website pikt ze automatisch op.

## Wat werkt

- Formaten: `.jpg` / `.jpeg` / `.png` / `.webp`
- Elke afbeelding in deze map verschijnt als een kaart in het portfolio
- Volgorde: alfabetisch op bestandsnaam (gebruik `01-`, `02-` prefix om zelf te sorteren)

## Aanbevolen formaat

- Minimaal 800×600 px, ideaal 1400×1000 px
- Liggend (landscape) past het beste in de portfolio-kaarten
- WebP is kleiner en sneller dan JPEG

## Automatische titel en materiaal

De PHP-pagina leidt de kaarttitel af van de bestandsnaam:

| Bestandsnaam          | Titel op site |
|-----------------------|---------------|
| `golf-groen.jpg`      | Golf Groen    |
| `01-torso.jpg`        | Torso         |
| `albast-vogel.webp`   | Albast Vogel  |

Materiaal wordt automatisch herkend als de bestandsnaam het woord bevat:
- `albast` → **Albast**
- `graniet` → **Graniet**
- `marmer` → **Marmer**
- anders → **Speksteen**

## Foto's op de VPS plaatsen

```bash
# Kopieer via scp (naam maakt niet uit):
scp mijn-foto.jpg user@vps:/pad/naar/speksteen/public/images/

# Of via SFTP met FileZilla / WinSCP
# Map: /pad/naar/speksteen/public/images/
```

Geen rebuild nodig — foto's zijn direct zichtbaar.
