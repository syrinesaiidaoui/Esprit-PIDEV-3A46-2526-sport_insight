# Gestion des Alertes d'Expiration de Contrats

## 📋 Vue d'ensemble

Cette guide explique comment configurer l'automatisation des alertes d'expiration de contrats de sponsoring via SMS.

## 🔧 Commande Symfony

### Commande disponible
```bash
php bin/console app:contract:expiration
```

### Options disponibles

#### `--dry-run`
Active le mode test. Affiche les SMS qui seraient envoyés sans les envoyer réellement.
```bash
php bin/console app:contract:expiration --dry-run
```

#### `--days-ahead=N`
Vérifie les contrats expirant dans les N jours à venir (au lieu des contrats déjà expirés).
```bash
php bin/console app:contract:expiration --days-ahead=7
```

### Exemples d'utilisation

**Tester sans envoyer d'SMS :**
```bash
php bin/console app:contract:expiration --dry-run
```

**Tester avec un préavis de 7 jours :**
```bash
php bin/console app:contract:expiration --days-ahead=7 --dry-run
```

**Envoyer les SMS pour les contrats expirés :**
```bash
php bin/console app:contract:expiration
```

**Envoyer les SMS pour les contrats expirant dans 7 jours :**
```bash
php bin/console app:contract:expiration --days-ahead=7
```

## ⏰ Configuration Cron (Automatisation)

### Linux/Mac - Crontab

1. Ouvrez le crontab :
```bash
crontab -e
```

2. Ajoutez une ou plusieurs lignes selon vos besoins :

**Chaque jour à 8h (contrats déjà expirés) :**
```bash
0 8 * * * cd /path/to/project && php bin/console app:contract:expiration >> /var/log/contract-expiration.log 2>&1
```

**Chaque jour à 9h (contrats expiring dans 7 jours) :**
```bash
0 9 * * * cd /path/to/project && php bin/console app:contract:expiration --days-ahead=7 >> /var/log/contract-expiration.log 2>&1
```

**Deux fois par jour (8h et 17h) :**
```bash
0 8,17 * * * cd /path/to/project && php bin/console app:contract:expiration >> /var/log/contract-expiration.log 2>&1
```

### Windows - Task Scheduler

1. Ouvrez **Task Scheduler** (Planificateur de tâches)

2. Créez une nouvelle tâche planifiée :
   - **Name :** `ContractExpiration_Daily`
   - **Trigger :** Daily at 08:00
   - **Action :** Run a program
     - Program : `C:\xampp\php\php.exe`
     - Arguments : `bin/console app:contract:expiration`
     - Start in : `C:\path\to\project`

3. Configurez les notifications (optionnel) :
   - Log output to file : `C:\logs\contract-expiration.log`

### Docker/Docker Compose

Ajoutez un service cron dans votre `docker-compose.yml` :

```yaml
cron:
  image: php:8.1-alpine
  working_dir: /app
  volumes:
    - .:/app
  command: |
    sh -c "
    apk add --no-cache supercrond
    echo '0 8 * * * php /app/bin/console app:contract:expiration' | crond -f
    "
```

## 📝 Format des messages SMS

### Contrats expirés
```
Alerte: Contrat #123 avec NomSponsor a expiré le 22/02/2026. Veuillez contacter l'administrateur.
```

### Contrats expirant bientôt (via --days-ahead)
```
Alerte: Contrat #125 avec NomSponsor expire le 01/03/2026. Veuillez contacter l'administrateur.
```

## 🔍 Dépannage

### La commande ne trouve pas les contrats
- Vérifiez que le champ `dateFin` des contrats est rempli
- Assurez-vous que `statut != 'Expiré'` (les contrats déjà marqués comme expirés ne sont pas renvoyés)

### Les SMS ne sont pas envoyés
- Testez d'abord en mode dry-run : `--dry-run`
- Vérifiez que les équipes ont un numéro de téléphone enregistré
- Vérifiez votre clé Twilio dans `.env.local`
- Consultez les logs : `var/log/dev.log`

### Erreur "No phone number for contract"
- Allez dans l'édition de l'équipe
- Ajoutez un numéro de téléphone valide (format E.164 : +216XXXXXXXX)

## 📊 Monitoring

### Voir les logs
```bash
tail -f var/log/dev.log | grep "SMS"
```

### Compter les SMS envoyés aujourd'hui
```bash
grep "SMS sent successfully" var/log/dev.log | wc -l
```

## 🔐 Sécurité

- Les clés Twilio sont stockées dans `.env.local` (jamais versionné)
- Les logs contiennent les numéros de téléphone (à sécuriser)
- Les SMS sont envoyés uniquement aux équipes avec contrat expirant

## ✅ Checklist de configuration

- [ ] Twilio SID configuré dans `.env.local`
- [ ] Twilio Auth Token configuré dans `.env.local`
- [ ] Numéro de téléphone Twilio configuré dans `.env.local`
- [ ] Les équipes ont un numéro de téléphone enregistré
- [ ] Testé en mode `--dry-run`
- [ ] Testé l'envoi réel du SMS
- [ ] Cron configuré sur le serveur
- [ ] Logs redirigés vers un fichier
- [ ] Monitoring des logs en place
