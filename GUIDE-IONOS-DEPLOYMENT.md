# Guide de Déploiement IONOS.fr

## 🚀 Méthodes de déploiement

### Option 1: Upload FTP Direct (Recommandé pour débutants)

#### A. Accès FTP IONOS
1. Connectez-vous à votre espace client IONOS
2. Allez dans "Hébergement" → "Gestion FTP"
3. Notez les informations:
   - Serveur: `ftp.votre-domaine.com`
   - Utilisateur: `votre-nom-utilisateur`
   - Mot de passe: `votre-mot-de-passe`

#### B. Upload avec FileZilla
```
1. Téléchargez FileZilla Client
2. Connectez-vous avec vos identifiants FTP
3. Naviguez vers le dossier /html/ ou /public_html/
4. Uploadez tous les fichiers du formular:
   - index.html
   - style.css
   - script.js
   - submit-form.php
   - images/ (dossier complet)
```

#### C. Configuration finale
1. Modifiez `submit-form.php`:
   ```php
   $to_email = 'votre-email@votre-domaine.com';
   ```
2. Testez: `https://votre-domaine.com/formular/`

### Option 2: WordPress sur IONOS

#### A. Installation WordPress
1. Dans l'espace client IONOS: "Applications" → "WordPress"
2. Installation automatique
3. Notez les identifiants d'administration

#### B. Upload des fichiers
```
wp-content/themes/your-theme/
├── js/script.js
├── css/style.css
├── images/ (tous les fichiers)
├── carpet-form-template.php
└── functions.php (ajoutez le code ionos-wordpress-config.php)
```

#### C. Utilisation
1. Créez une nouvelle page
2. Ajoutez le shortcode: `[carpet_form]`
3. Publiez la page

### Option 3: GitHub + Déploiement Automatique

#### A. Setup Repository GitHub
```bash
git init
git add .
git commit -m "Initial commit - Carpet Form"
git remote add origin https://github.com/username/carpet-form.git
git push -u origin main
```

#### B. Configuration GitHub Actions
1. Dans votre repo GitHub: Settings → Secrets
2. Ajoutez:
   - `IONOS_FTP_SERVER`: ftp.votre-domaine.com
   - `IONOS_FTP_USERNAME`: votre-nom-utilisateur
   - `IONOS_FTP_PASSWORD`: votre-mot-de-passe

#### C. Déploiement automatique
- Chaque push sur `main` déploie automatiquement
- Fichier de config: `.github/workflows/deploy.yml`

## 📧 Configuration Email IONOS

### Pour sites statiques (PHP):
```php
// Dans submit-form.php
$to_email = 'contact@votre-domaine.com';

// Headers recommandés pour IONOS
$headers = array(
    'From: Formulaire Tapis <noreply@votre-domaine.com>',
    'Reply-To: ' . $data['email'],
    'Content-Type: text/plain; charset=UTF-8'
);
```

### Pour WordPress:
```php
// SMTP Configuration IONOS
'smtp_host' => 'smtp.ionos.fr',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'votre-email@votre-domaine.com',
'smtp_password' => 'votre-mot-de-passe'
```

## 🔧 Configuration Spécifique IONOS

### .htaccess recommandé:
```apache
# Dans le dossier du formulaire
RewriteEngine On

# Sécurité de base
<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

# Cache pour les images
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
```

### PHP.ini recommandé (si possible):
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

## 🧪 Tests après déploiement

### Checklist de vérification:
- [ ] Le formulaire s'affiche correctement
- [ ] Les tabs fonctionnent
- [ ] Le calcul des prix fonctionne
- [ ] Les images se chargent
- [ ] La soumission envoie un email
- [ ] Le design est responsive

### Tests email:
1. Soumettez le formulaire avec vos données
2. Vérifiez votre boîte email (et spam)
3. Testez la fonction "Répondre"

## 🐛 Dépannage IONOS

### Problème: Email ne fonctionne pas
```php
// Ajoutez pour debugging dans submit-form.php
ini_set('log_errors', 1);
ini_set('error_log', 'php-errors.log');
error_log('Test email sending...');
```

### Problème: Upload FTP échoue
- Vérifiez les permissions du dossier (755)
- Mode de transfert: Binaire pour images, ASCII pour code
- Vérifiez l'espace disque disponible

### Problème: Site lent
- Optimisez les images (compression)
- Activez la mise en cache
- Vérifiez les logs d'erreur

## 📞 Support

### IONOS Support:
- Chat en ligne: disponible 24/7
- Téléphone: disponible selon votre plan
- Centre d'aide: aide.ionos.fr

### Documentation utile:
- [Guide FTP IONOS](https://aide.ionos.fr/hebergement-web)
- [Configuration WordPress](https://aide.ionos.fr/applications-web/wordpress)
- [Configuration Email](https://aide.ionos.fr/email)

## 🎯 URLs finales

Après déploiement, votre formulaire sera accessible à:
- **Site statique**: `https://votre-domaine.com/formular/`
- **WordPress**: `https://votre-domaine.com/devis-nettoyage/`
- **Sous-domaine**: `https://formular.votre-domaine.com/`