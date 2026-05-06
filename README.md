# Dith – Spekstenenbeelden.nl

Website voor beeldend kunstenaar Dith, Maassluis.

## Projectstructuur

```
speksteen/
├── Dockerfile
├── docker-compose.yml
├── README.md
└── public/
    ├── index.html          ← Portfolio (homepagina)
    ├── informatie.html     ← Over speksteen
    ├── contact.html        ← Contactformulier
    ├── .htaccess
    ├── css/
    │   └── style.css
    ├── js/
    │   └── main.js
    └── images/             ← Voeg hier je foto's toe (zie hieronder)
```

## Snel starten

```bash
docker compose up -d
```

De site is dan bereikbaar op http://localhost:8080

## Foto's toevoegen

Maak een map `public/images/` aan en voeg de foto's toe met deze namen
(of pas de HTML aan naar jouw bestandsnamen):

| Bestand             | Beeld                             |
|---------------------|-----------------------------------|
| groen-golf.jpg      | Groen liggend speksteen           |
| duif-wit.jpg        | Witte albast duif                 |
| donker-vlam.jpg     | Donker groen vlam/oogvorm         |
| torso-spek.jpg      | Speksteen torso op sokkel         |
| albast-figuur.jpg   | Slanke witte albast figuur        |
| koppel-grijs.jpg    | Grijs speksteen koppel            |
| figuur-roze.jpg     | Roze figuur met ruw deel          |
| vogel-roze.jpg      | Roze speksteen vogel op staaf     |
| masker.jpg          | Donker groen masker               |
| zwart-mes.jpg       | Zwart puntig speksteen            |
| albast-cirkel.jpg   | Witte albast cirkel met figuur    |

**Tip:** Gebruik WebP formaat voor snellere laadtijden. Afmeting: 1200×900px is ideaal.

## Nginx Proxy Manager (jouw VPS setup)

In `docker-compose.yml` verwijder je de `ports:` sectie en voeg je toe:

```yaml
networks:
  - proxy

networks:
  proxy:
    external: true
```

Maak dan in Nginx Proxy Manager een Proxy Host aan:
- **Domain**: spekstenenbeelden.nl
- **Forward Hostname**: speksteen_web
- **Forward Port**: 80
- **SSL**: Letsencrypt inschakelen

## Contactformulier backend (optioneel)

Het formulier toont nu alleen een succesbericht (demo).
Voor echte e-mails: maak `public/contact.php` en verwijs het form ernaar.
Zorg dat `php-mail` of een SMTP library (bijv. PHPMailer) beschikbaar is.

```php
// contact.php voorbeeld
<?php
if ($_POST) {
  $naam    = htmlspecialchars($_POST['naam']);
  $email   = htmlspecialchars($_POST['email']);
  $bericht = htmlspecialchars($_POST['bericht']);
  mail('dith@spekstenenbeelden.nl', 'Nieuw bericht', "$naam\n\n$bericht", "Reply-To: $email");
  echo json_encode(['ok' => true]);
}
```
