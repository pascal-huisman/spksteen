# KWF — Docker ontwikkelomgeving

Volledige PHP ontwikkelomgeving met optionele **Claude Code AI-sandbox** als non-root gebruiker.

---

## Services overzicht

| Service | Container | Poort | Beschrijving |
|---|---|---|---|
| `nginx` | `kwf_nginx` | 8002 | Webserver |
| `app` | `kwf_app` | intern | PHP 8.4-FPM applicatieserver |
| `mysql` | `kwf_mysql` | 8003 | MySQL 8 database |
| `cron` | `kwf_cron` | — | Periodieke taken (elke 60s) |
| `queue` | `kwf_queue` | — | Queue worker (elke 5s) |
| `claude` | `kwf_claude` | — | Claude Code AI-sandbox (on-demand) |

Je applicatie bereik je via: [http://localhost:8002](http://localhost:8002)  
MySQL bereik je via: `localhost:8003`

---

## Vereisten

- Docker Desktop ≥ 4.x
- PhpStorm (aanbevolen)
- Anthropic account voor Claude Code → [console.anthropic.com](https://console.anthropic.com)

---

## Installatie

### 1. Omgevingsvariabelen instellen

```bash
cp .env.example .env
```

Vul minimaal deze waarden in je `.env`:

```env
# Database
DB_ROOT_PASSWORD=secret
DB_DATABASE=kwf
DB_USERNAME=kwfuser
DB_PASSWORD=kwfpassword

# Claude Code (optioneel — alleen nodig als je Claude wilt gebruiken)
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxxxxxxxx
```

### 2. Images bouwen

```bash
# PHP app image + Claude image bouwen
docker compose build
docker compose build claude
```

### 3. Ontwikkelomgeving starten

```bash
docker compose up -d
```

Dit start: `nginx`, `app`, `mysql`, `cron`, `queue`.  
Claude start **niet automatisch** mee — zie hieronder.

---

## Claude Code gebruiken

Claude is geconfigureerd met een `profile` zodat hij **niet meegaat met `docker compose up`**.  
Je start hem alleen wanneer je hem nodig hebt.

### Claude starten vanuit PhpStorm terminal

```bash
docker compose run --rm claude
```

Je zit nu in de container. Verifieer dat je niet als root draait:

```bash
whoami    # → claude
id        # → uid=1001(claude) gid=1001(claude)
```

Start Claude Code:

```bash
claude
```


bij error:
Claude configuration file not found at: /home/claude/.claude.json
A backup file exists at: /home/claude/.claude/backups/.claude.json.backup.1774502102493
You can manually restore it by running: cp "/home/claude/.claude/backups/.claude.json.backup.1774502102493" "/home/claude/.claude.json"
....

claude /login

### Wat kan Claude zien?

Claude heeft toegang tot exact dezelfde bestanden als je `app` container:

- ✅ `/var/www/html` — jouw volledige projectmap
- ✅ `/var/www/html/vendor` — composer dependencies (zelfde volume)
- ✅ Kan verbinden met `mysql` via het `kwf_network`
- ❌ Geen toegang tot je hostsysteem buiten het project
- ❌ Geen root-rechten
- ❌ Geen toegang tot de Docker socket

### Claude beëindigen

```bash
/exit
# of Ctrl+C, daarna:
exit
```

---

## Beveiliging

Claude draait als gebruiker `claude` met uid 1001 — **niet als root**.

| Risico | Beschermd? | Hoe |
|---|---|---|
| Schrijven buiten `/var/www/html` | ✅ | Docker volume isolatie |
| Toegang tot SSH-sleutels | ✅ | Niet gemount |
| Toegang tot `~/.aws` credentials | ✅ | Niet gemount |
| Docker socket misbruik | ✅ | Socket nooit gemount |
| Escalatie naar root in container | ✅ | Geen sudo rechten |

> ⚠️ **Belangrijk:** Voeg nooit `/var/run/docker.sock` toe aan de Claude service.  
> Via de Docker socket kan een agent bevoorrechte containers opstarten en je hostsysteem overnemen.

---

## Projectstructuur

```
kwf-project/
├── .devcontainer/
│   └── Dockerfile          # Claude Code non-root container
├── docker/
│   ├── nginx/
│   │   └── default.conf    # Nginx configuratie
│   ├── mysql/
│   │   └── my.cnf          # MySQL configuratie
│   └── php/
│       └── php.ini         # PHP configuratie
├── cli/
│   ├── screen-messages.php # Cron taak
│   └── queue-worker.php    # Queue worker
├── public/                 # Web root
├── src/                    # PHP broncode
├── vendor/                 # Composer (via Docker volume)
├── Dockerfile              # PHP 8.4-FPM image
├── docker-compose.yml      # Alle services
├── .env                    # Jouw secrets (niet in git!)
└── .env.example            # Template
```

---

## Handige commando's

```bash
# Alles starten
docker compose up -d

# Logs bekijken
docker compose logs -f app
docker compose logs -f queue

# In de app container gaan (als www-data)
docker compose exec app bash

# In de Claude container gaan
docker compose run --rm claude

# Composer update uitvoeren (via app container)
docker compose exec app composer update

# Alles stoppen (data blijft bewaard)
docker compose down

# Alles stoppen inclusief volumes (RESET — data weg!)
docker compose down -v
```

---

## Claude sessie persistent houden

Claude slaat inloggegevens en instellingen op in `~/.claude` op je host.  
Dit is gemount via:

```yaml
- ~/.claude:/home/claude/.claude
```

Na `docker compose down` hoef je **niet opnieuw in te loggen**.

---

## Veelvoorkomende problemen

### MySQL start niet op

Controleer of het healthcheck-wachtwoord overeenkomt met `DB_ROOT_PASSWORD` in je `.env`.

### Claude meldt "ANTHROPIC_API_KEY not set"

```bash
grep ANTHROPIC_API_KEY .env
```

Zorg dat de key in `.env` staat, niet alleen in `.env.example`.

### Vendor map leeg na `docker compose up`

De `vendor_data` volume wordt gedeeld tussen `app`, `cron`, `queue` en `claude`.  
Voer eenmalig uit:

```bash
docker compose exec app composer install
```

### Permissieproblemen op `storage/`

```bash
docker compose exec app chown -R www-data:www-data /var/www/html/storage
docker compose exec app chmod -R 775 /var/www/html/storage
```
