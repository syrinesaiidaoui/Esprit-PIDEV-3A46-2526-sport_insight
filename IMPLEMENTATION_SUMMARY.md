# 🎯 Summary - Contract Expiration Alerts Implementation

## ✅ What Was Created

### 1. **Symfony Command** 
📄 `src/Command/ContractExpirationCommand.php`
- Checks for expired or expiring-soon contracts
- Sends SMS alerts to team contacts
- Supports dry-run mode for testing
- Provides detailed console output

### 2. **Repository Methods**
📄 `src/Repository/ContratSponsorRepository.php`
- `findByExpirationDate($date)` - Find contracts expired on/before a date
- `findExpiringWithinDays($days)` - Find contracts expiring within N days

### 3. **Automation Scripts**
- 📄 `scripts/contract-expiration-cron-setup.sh` (for Linux/Mac)
- 📄 `scripts/contract-expiration-cron-setup.ps1` (for Windows)

### 4. **Documentation**
- 📄 `CONTRACT_EXPIRATION_SETUP.md` - Quick start guide
- 📄 `CRON_SETUP.md` - Detailed configuration guide

## 🚀 How to Use

### Step 1: Test the Command

**Dry-run mode (no SMS sent):**
```bash
php bin/console app:contract:expiration --dry-run
```

**Expected output:**
```
✅ No contracts found to process. (Contrats expired today or earlier)
```

### Step 2: Test with Actual Contracts

If you have expired contracts in the database:
```bash
php bin/console app:contract:expiration --dry-run -v
```

This will show:
```
✅ SMS to [phone] for Contract #[id] ([Sponsor Name])
Summary:
  • Dry-run mode: No actual SMS sent
  • SMS Sent: X
  • SMS Failed: Y
  • Skipped (no phone): Z
```

### Step 3: Setup Automated Execution

#### **For Linux/Mac:**
```bash
bash scripts/contract-expiration-cron-setup.sh /path/to/project
```

#### **For Windows (Run as Administrator):**
```powershell
.\scripts\contract-expiration-cron-setup.ps1 -ProjectPath "C:\path\to\project" -PhpPath "C:\xampp\php\php.exe"
```

#### **Or Manual Setup:**

**Linux crontab:**
```bash
crontab -e
```
Add:
```bash
0 8 * * * cd /path/to/project && php bin/console app:contract:expiration >> /var/log/contract-expiration.log 2>&1
0 9 * * * cd /path/to/project && php bin/console app:contract:expiration --days-ahead=7 >> /var/log/contract-expiration.log 2>&1
```

**Windows Task Scheduler:**
1. Open Task Scheduler
2. Create Basic Task
3. Set trigger to 08:00 daily
4. Set action to run: `C:\xampp\php\php.exe bin/console app:contract:expiration`
5. Set working directory: `C:\path\to\project`

## 📋 Command Reference

```bash
# Check expired contracts (today and earlier)
php bin/console app:contract:expiration

# Check contracts expiring within 7 days
php bin/console app:contract:expiration --days-ahead=7

# Test without sending SMS
php bin/console app:contract:expiration --dry-run

# Test 7-day warning without sending
php bin/console app:contract:expiration --days-ahead=7 --dry-run

# Run with verbose output
php bin/console app:contract:expiration -v

# Show help
php bin/console app:contract:expiration --help
```

## 🔄 Automated Schedule (After Setup)

| Time | What | Result |
|------|------|--------|
| **08:00 AM** | Check expired contracts | SMS sent to teams with expired contracts |
| **09:00 AM** | Check upcoming (7 days) | SMS sent to teams with expiring-soon contracts |

**Runs:** Every day (Monday-Sunday)

## 📊 What Gets Sent

### SMS to Team Contact

When a contract expires:
```
Alerte: Contrat #123 avec SponsorName a expiré le 22/02/2026. Veuillez contacter l'administrateur.
```

When a contract expires soon:
```
Alerte: Contrat #125 avec SponsorName expire le 01/03/2026. Veuillez contacter l'administrateur.
```

## 🔍 Monitoring & Logs

### View recent logs
```bash
tail -f var/log/dev.log | grep -i sms
```

### Count messages sent
```bash
grep "SMS sent successfully" var/log/dev.log | wc -l
```

### Check for errors
```bash
grep -i "error\|failed" var/log/dev.log | tail -20
```

## ⚠️ Prerequisites

Before using, ensure:
- ✅ Twilio is configured (`.env.local`)
- ✅ Test SMS works via `/admin/contrat/test-sms`
- ✅ Team phone numbers are filled in database (format: +216XXXXXXXX)
- ✅ Contracts have `dateFin` date set

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| Command not found | Run `php bin/console cache:clear` |
| No contracts found | Check database has contracts with `dateFin` |
| SMS not sending | Test with `--dry-run`, check phone format |
| Cron not running | Verify crontab: `crontab -l` or Task Scheduler |

## 📁 File Structure

```
project/
├── src/
│   ├── Command/
│   │   └── ContractExpirationCommand.php      ← Main command
│   └── Repository/
│       └── ContratSponsorRepository.php       ← Updated with 2 new methods
├── scripts/
│   ├── contract-expiration-cron-setup.sh      ← Linux/Mac setup
│   └── contract-expiration-cron-setup.ps1     ← Windows setup
├── CONTRACT_EXPIRATION_SETUP.md               ← Quick start
├── CRON_SETUP.md                              ← Detailed guide
└── var/
    └── log/
        └── dev.log                            ← Logs go here
```

## ✨ Key Features

✅ **Automated**: Runs on schedule without manual intervention  
✅ **Smart**: Only sends to teams with phone numbers  
✅ **Safe**: Dry-run mode for testing  
✅ **Logged**: All actions recorded for monitoring  
✅ **Flexible**: Customizable day ranges (7 days, 14 days, etc.)  
✅ **Cross-platform**: Works on Linux, Mac, Windows  

## 🎉 Next Steps

1. **Test:** Run `php bin/console app:contract:expiration --dry-run`
2. **Verify:** Check that output looks correct
3. **Setup Cron:** Use the provided setup script
4. **Monitor:** Check logs in `var/log/dev.log`
5. **Relax:** Let the system handle expiration alerts! 🤖

---

**Questions?** See `CONTRACT_EXPIRATION_SETUP.md` for detailed documentation.
