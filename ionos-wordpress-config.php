<?php
/**
 * Configuration spécifique pour IONOS WordPress
 */

// Configuration email pentru IONOS
function ionos_mail_config() {
    return array(
        'smtp_host' => 'smtp.ionos.fr',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'smtp_auth' => true,
        'smtp_username' => 'info@procleantapis.fr',
        'smtp_password' => 'votre-mot-de-passe'
    );
}

// Configurare WordPress mail pentru IONOS
function configure_ionos_wp_mail($phpmailer) {
    $config = ionos_mail_config();
    
    $phpmailer->isSMTP();
    $phpmailer->Host = $config['smtp_host'];
    $phpmailer->SMTPAuth = $config['smtp_auth'];
    $phpmailer->Port = $config['smtp_port'];
    $phpmailer->Username = $config['smtp_username'];
    $phpmailer->Password = $config['smtp_password'];
    $phpmailer->SMTPSecure = $config['smtp_secure'];
    $phpmailer->From = $config['smtp_username'];
    $phpmailer->FromName = get_bloginfo('name');
}
// Nu suprascrie dacă WP Mail SMTP este activ
if (!function_exists('wp_mail_smtp')) {
    add_action('phpmailer_init', 'configure_ionos_wp_mail');
}

// Handler pentru formularul de covoare adaptat pentru IONOS
function handle_carpet_form_submission_ionos() {
    // Verifică nonce pentru securitate
    if (!wp_verify_nonce($_POST['nonce'], 'carpet_form_nonce')) {
        wp_die('Acces interzis');
    }
    
    // Sanitizează datele
    $data = array(
        'nom' => sanitize_text_field($_POST['nom']),
        'email' => sanitize_email($_POST['courriel']),
        'telephone' => sanitize_text_field($_POST['telephone']),
        'date' => sanitize_text_field($_POST['date']),
        'rue' => sanitize_text_field($_POST['rue']),
        'ville' => sanitize_text_field($_POST['ville']),
        'code_postal' => sanitize_text_field($_POST['code-postal']),
        'message' => sanitize_textarea_field($_POST['message']),
        'carpet_selected' => sanitize_text_field($_POST['carpet_selected']),
        'surface' => floatval($_POST['surface']),
        'steam_cleaner' => sanitize_text_field($_POST['steam_cleaner']),
        'delivery_type' => sanitize_text_field($_POST['delivery_type']),
        'delivery_cost' => floatval($_POST['delivery_cost'])
    );
    
    // Validare
    if (empty($data['nom']) || empty($data['email'])) {
        wp_send_json_error('Le nom et l\'email sont obligatoires');
    }
    
    if (!is_email($data['email'])) {
        wp_send_json_error('Email invalide');
    }
    
    // Email de destinație (configurați cu email-ul dvs. IONOS)
    $to = 'info@procleantapis.fr';
    
    // Construire email
    $subject = 'Nouvelle demande de devis - ' . $data['nom'];
    
    $message = "Nouvelle demande de devis:\n\n";
    $message .= "Nom: " . $data['nom'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Téléphone: " . $data['telephone'] . "\n";
    $message .= "Date: " . $data['date'] . "\n";
    $message .= "Adresse: " . $data['rue'] . ", " . $data['ville'] . " " . $data['code_postal'] . "\n\n";
    
    if (!empty($data['carpet_selected'])) {
        $message .= "Service sélectionné: " . $data['carpet_selected'] . "\n";
        $message .= "Surface: " . $data['surface'] . " m²\n";
        $message .= "Nettoyage vapeur: " . $data['steam_cleaner'] . "\n";
        $message .= "Type de livraison: " . $data['delivery_type'] . "\n";
        $message .= "Coût livraison: " . $data['delivery_cost'] . " €\n\n";
    }
    
    $message .= "Message:\n" . $data['message'] . "\n";
    
    // Headers
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        // From rămâne cel configurat în SMTP (IONOS); setăm doar Reply-To către utilizator
        'Reply-To: ' . $data['nom'] . ' <' . $data['email'] . '>'
    );
    
    // Trimite email
    $sent = wp_mail($to, $subject, $message, $headers);
    
    if ($sent) {
        // Log pentru debugging (opțional)
        error_log('Carpet form submitted: ' . $data['email']);
        
        wp_send_json_success('Votre demande a été envoyée avec succès');
    } else {
        error_log('Failed to send carpet form email: ' . $data['email']);
        wp_send_json_error('Erreur lors de l\'envoi de l\'email');
    }
}

// Înregistrează handler-ul
// Evită înregistrarea dublă dacă pluginul propriu gestionează deja handlerul
if (!class_exists('Proclean_Devis_Form_Plugin')) {
    add_action('wp_ajax_submit_carpet_form', 'handle_carpet_form_submission_ionos');
    add_action('wp_ajax_nopriv_submit_carpet_form', 'handle_carpet_form_submission_ionos');
}

// Configurare specifică pentru IONOS hosting
function ionos_specific_settings() {
    // Mărește limita de timp pentru execuție
    @ini_set('max_execution_time', 300);
    
    // Configurare pentru upload de fișiere mari
    @ini_set('upload_max_filesize', '10M');
    @ini_set('post_max_size', '10M');
    
    // Disable WordPress debug pentru production
    if (!defined('WP_DEBUG')) {
        define('WP_DEBUG', false);
    }
}
add_action('init', 'ionos_specific_settings');
?>