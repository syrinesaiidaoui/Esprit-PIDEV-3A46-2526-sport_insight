# 🔔 Contract Expiration Alerts - Setup Guide

## 📌 Overview

This system automatically monitors contract expiration dates and sends SMS alerts to team contacts when contracts expire or are about to expire.

## 🎯 Features

✅ **Automated SMS alerts** when contracts expire  
✅ **Advance warnings** (7 days before expiration)  
✅ **Dry-run mode** for testing without sending SMS  
✅ **Detailed logging** for monitoring and debugging  
✅ **Cron automation** for hands-free operation  
✅ **Windows Task Scheduler** support  
✅ **Docker-compatible** setup  

## 🚀 Quick Start

### 1. Ensure prerequisites
- ✅ Twilio is configured (see main `.env.local`)
- ✅ Team models have `telephone` field populated
- ✅ MySQL/database accessible

### 2. Test the command

**Dry-run mode (no SMS sent):**
```bash
php bin/console app:contract:expiration --dry-run
```

**With 7-day warning:**
```bash
php bin/console app:contract:expiration --days-ahead=7 --dry-run
```

**Send actual SMS (when ready):**
```bash
php bin/console app:contract:expiration
```

### 3. Setup automated execution

#### Option A: Linux/Mac - Cron

Use the provided setup script:
```bash
bash scripts/contract-expiration-cron-setup.sh /path/to/project
```

Or manually add to crontab:
```bash
crontab -e
```

Add these lines:
```bash
# Check expired contracts daily at 08:00
0 8 * * * cd /path/to/project && php bin/console app:contract:expiration >> /var/log/contract-expiration.log 2>&1

# Check contracts expiring in 7 days daily at 09:00
0 9 * * * cd /path/to/project && php bin/console app:contract:expiration --days-ahead=7 >> /var/log/contract-expiration.log 2>&1
```

#### Option B: Windows - Task Scheduler

Run the PowerShell setup script with admin rights:
```powershell
.\scripts\contract-expiration-cron-setup.ps1 -ProjectPath "C:\path\to\project" -PhpPath "C:\xampp\php\php.exe"
```

Or manually:
1. Open **Task Scheduler**
2. Create a **Basic Task** with:
   - **Trigger:** Daily at 08:00
   - **Action:** Run `C:\xampp\php\php.exe bin/console app:contract:expiration`
   - **Working Directory:** `C:\path\to\project`

#### Option C: Docker

Add to your `docker-compose.yml`:
```yaml
cron:
  image: php:8.2-alpine
  working_dir: /app
  volumes:
    - .:/app
  command: |
    sh -c "
    apk add --no-cache supercrond
    echo '0 8 * * * php /app/bin/console app:contract:expiration' | crond -f
    "
```

## 📋 Command Options

### Basic Usage
```bash
php bin/console app:contract:expiration
```

### Options

| Option | Description | Example |
|--------|-------------|---------|
| `--dry-run` | Test mode: show what would be sent without sending | `--dry-run` |
| `--days-ahead=N` | Check contracts expiring within N days | `--days-ahead=7` |

### Examples

```bash
# Check today's expired contracts and send SMS
php bin/console app:contract:expiration

# Check contracts expiring in 7 days and send SMS
php bin/console app:contract:expiration --days-ahead=7

# Test without sending SMS
php bin/console app:contract:expiration --dry-run

# Test 7-day warning without sending SMS
php bin/console app:contract:expiration --days-ahead=7 --dry-run

# Run with verbose output
php bin/console app:contract:expiration -v

# Run with very verbose output (debug)
php bin/console app:contract:expiration -vv
```

## 📊 Typical Alert Schedule

| Time | Task | Action |
|------|------|--------|
| **08:00** | Check Expired | Sends SMS for immediately expired contracts |
| **09:00** | Check Coming | Sends SMS for contracts expiring within 7 days |

**Frequency:** Daily (Monday-Sunday)

## 📝 SMS Message Format

### Expired Contract
```
Alerte: Contrat #123 avec NomSponsor a expiré le 22/02/2026. Veuillez contacter l'administrateur.
```

### Coming Expiration (7-day warning)
```
Alerte: Contrat #125 avec NomSponsor expire le 01/03/2026. Veuillez contacter l'administrateur.
```

## 🔍 Troubleshooting

### Command not found
```bash
php bin/console list | grep expiration
```
If not shown, clear cache:
```bash
php bin/console cache:clear
```

### No contracts found
1. Check database has contracts
2. Verify `dateFin` field is populated
3. Check that `statut != 'Expiré'` (already expired marked contracts are skipped)

### SMS not sending
1. Test with `--dry-run` first
2. Verify team phone numbers are filled in (E.164 format: +216XXXXXXXX)
3. Check `.env.local` has Twilio credentials
4. Review logs: `tail -f var/log/dev.log`

### Phone number validation errors
- Phone must be in E.164 format: `+[country code][number]`
- Example: `+21696123456` (Tunisia)
- Not accepted: `+216 96 123 456` (spaces), `0696123456` (missing +country code)

## 📋 Database Fields Required

### Equipe (Team)
- `telephone` (VARCHAR, e.g., "+216XXXXXXXX")
- `email` (optional, VARCHAR)

### ContratSponsor (Contract)
- `dateFin` (DATE)
- `statut` (VARCHAR, should not be "Expiré")
- relationship to `sponsor` and `equipe`

### Sponsor (Sponsor)
- `nom` (name for SMS message)

## 🔒 Security Notes

- Twilio credentials in `.env.local` (never committed)
- Phone numbers in logs should be protected
- Only SMS to registered team contacts
- Consider log rotation to manage disk space

## 📡 Monitoring & Logs

### View real-time logs
```bash
tail -f var/log/dev.log | grep -i sms
```

### Count SMS sent today
```bash
grep "SMS sent successfully" var/log/dev.log | wc -l
```

### Archive old logs
```bash
gzip var/log/dev.log.* # on Linux
```

## 🆘 Support

For issues:
1. Test with `--dry-run` mode
2. Run with `-vv` for debug output
3. Check logs in `var/log/`
4. Verify `.env.local` configuration

## ✅ Setup Checklist

- [ ] Twilio SID in `.env.local`
- [ ] Twilio Auth Token in `.env.local`
- [ ] Twilio Phone in `.env.local`
- [ ] Test SMS with `/admin/contrat/test-sms`
- [ ] Team phone numbers filled in database
- [ ] Tested `--dry-run` command
- [ ] Tested actual SMS sending
- [ ] Cron/Task Scheduler configured
- [ ] Logs directory writable
- [ ] Monitoring setup (optional)

---

**For detailed documentation, see:** `CRON_SETUP.md`
