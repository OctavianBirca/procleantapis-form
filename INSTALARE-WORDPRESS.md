# Integrarea Formularului de Covoare în WordPress

## Pași pentru instalare:

### 1. **Copiați fișierele în tema WordPress**
```
wp-content/themes/your-theme/
├── js/
│   └── script.js (formularul dvs.)
├── css/
│   └── style.css (formularul dvs.)
├── images/
│   └── (toate imaginile dvs.)
├── carpet-form-template.php
└── functions.php (adăugați codul din wordpress-functions.php)
```

### 2. **Adăugați codul în functions.php**
Copiați întregul conținut din `wordpress-functions.php` în fișierul `functions.php` al temei dvs.

### 3. **Utilizarea shortcode**
Pentru a afișa formularul pe orice pagină sau post, folosiți:
```
[carpet_form]
```

### 4. **Configurare email**
Modificați în `functions.php`:
```php
$to = 'your-email@domain.com'; // Înlocuiți cu email-ul dvs.
```

### 5. **Testare**
1. Publicați o pagină cu shortcode-ul `[carpet_form]`
2. Completați formularul și trimiteți
3. Verificați email-ul pentru confirmarea primirii

## Funcționalități incluse:

✅ **Trimitere prin AJAX** - fără refresh de pagină
✅ **Validare date** - protecție împotriva spam-ului
✅ **Securitate WordPress** - folosește nonce și sanitizare
✅ **Email automat** - trimite email-ul la administrator
✅ **Salvare în baza de date** - (opțional) păstrează istoricul
✅ **Responsive design** - funcționează pe toate dispozitivele

## Personalizări posibile:

### A. **Modificarea email-ului de destinație:**
```php
$to = 'devis@votre-entreprise.com';
```

### B. **Adăugarea mai multor destinatari:**
```php
$to = array('admin@site.com', 'comercial@site.com');
```

### C. **Personalizarea mesajului:**
Modificați variabila `$email_body` în funcția `handle_carpet_form_submission`.

### D. **Redirectare după trimitere:**
```javascript
// În script.js, înlocuiți:
window.location.reload();
// Cu:
window.location.href = '/merci-pour-votre-demande/';
```

## Depanare:

### Problema: Formularul nu se trimite
- Verificați că jQuery este încărcat
- Verificați console-ul browser-ului pentru erori JavaScript
- Asigurați-vă că `wp_ajax` hook-urile sunt adăugate corect

### Problema: Nu primesc email-uri
- Verificați că funcția `wp_mail()` funcționează pe server
- Testați cu un plugin SMTP (ex: WP Mail SMTP)
- Verificați folderul de spam

### Problema: Erori de permisiuni
- Verificați că fișierele au permisiunile corecte (644 pentru fișiere, 755 pentru directoare)

## Îmbunătățiri ulterioare:

1. **Adăugarea de notificări** pentru administrator în dashboard
2. **Export CSV** pentru submissions
3. **Integrare cu CRM** (ex: Mailchimp, ActiveCampaign)
4. **Calculul automat al prețurilor** și trimiterea devisului
5. **Sistem de tracking** pentru status-ul cererii

Pentru suport suplimentar, contactați dezvoltatorul.