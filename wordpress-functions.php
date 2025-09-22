<?php
/**
 * Funcții pentru formularul de covoare în WordPress
 */

// Înregistrează script-urile și stilurile
function carpet_form_enqueue_scripts() {
    wp_enqueue_script('carpet-form-js', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('carpet-form-css', get_template_directory_uri() . '/css/style.css', array(), '1.0');
    
    // Localizează script-ul pentru AJAX
    wp_localize_script('carpet-form-js', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('carpet_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'carpet_form_enqueue_scripts');

// Handler pentru AJAX - utilizatori logați
add_action('wp_ajax_submit_carpet_form', 'handle_carpet_form_submission');
// Handler pentru AJAX - utilizatori nelogați
add_action('wp_ajax_nopriv_submit_carpet_form', 'handle_carpet_form_submission');

function handle_carpet_form_submission() {
    // Verifică nonce pentru securitate
    if (!wp_verify_nonce($_POST['nonce'], 'carpet_form_nonce')) {
        wp_die('Acces interzis');
    }
    
    // Sanitizează datele
    $nom = sanitize_text_field($_POST['nom']);
    $email = sanitize_email($_POST['courriel']);
    $telephone = sanitize_text_field($_POST['telephone']);
    $date = sanitize_text_field($_POST['date']);
    $rue = sanitize_text_field($_POST['rue']);
    $ville = sanitize_text_field($_POST['ville']);
    $code_postal = sanitize_text_field($_POST['code-postal']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Date despre servicii
    $carpet_selected = sanitize_text_field($_POST['carpet_selected']);
    $surface = floatval($_POST['surface']);
    $steam_cleaner = sanitize_text_field($_POST['steam_cleaner']);
    $delivery_type = sanitize_text_field($_POST['delivery_type']);
    $delivery_cost = floatval($_POST['delivery_cost']);
    
    // Validare de bază
    if (empty($nom) || empty($email)) {
        wp_send_json_error('Numele și email-ul sunt obligatorii');
    }
    
    if (!is_email($email)) {
        wp_send_json_error('Email invalid');
    }
    
    // Construiește email-ul
    $to = get_option('admin_email'); // sau email-ul dvs. specific
    $subject = 'Nouă cerere de devis - ' . $nom;
    
    $email_body = "Nouă cerere de devis de la:\n\n";
    $email_body .= "Nume: $nom\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Telefon: $telephone\n";
    $email_body .= "Data: $date\n";
    $email_body .= "Adresa: $rue, $ville $code_postal\n\n";
    
    if (!empty($carpet_selected)) {
        $email_body .= "Serviciu selectat: $carpet_selected\n";
        $email_body .= "Suprafață: $surface m²\n";
        $email_body .= "Nettoyage vapeur: $steam_cleaner\n";
        $email_body .= "Tip livrare: $delivery_type\n";
        $email_body .= "Cost livrare: $delivery_cost €\n\n";
    }
    
    $email_body .= "Mesaj:\n$message\n";
    
    // Headers pentru email
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $nom . ' <' . $email . '>',
        'Reply-To: ' . $email
    );
    
    // Trimite email-ul
    $sent = wp_mail($to, $subject, $email_body, $headers);
    
    if ($sent) {
        // Opțional: salvează în baza de date
        save_carpet_form_submission($nom, $email, $telephone, $date, $rue, $ville, $code_postal, $message, $carpet_selected, $surface, $steam_cleaner, $delivery_type, $delivery_cost);
        
        wp_send_json_success('Formularul a fost trimis cu succes');
    } else {
        wp_send_json_error('Eroare la trimiterea email-ului');
    }
}

// Funcție pentru salvarea în baza de date (opțional)
function save_carpet_form_submission($nom, $email, $telephone, $date, $rue, $ville, $code_postal, $message, $carpet_selected, $surface, $steam_cleaner, $delivery_type, $delivery_cost) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'carpet_form_submissions';
    
    $wpdb->insert(
        $table_name,
        array(
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date' => $date,
            'rue' => $rue,
            'ville' => $ville,
            'code_postal' => $code_postal,
            'message' => $message,
            'carpet_selected' => $carpet_selected,
            'surface' => $surface,
            'steam_cleaner' => $steam_cleaner,
            'delivery_type' => $delivery_type,
            'delivery_cost' => $delivery_cost,
            'submitted_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%f', '%s')
    );
}

// Crează tabelul în baza de date la activare (opțional)
function create_carpet_form_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'carpet_form_submissions';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nom varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        telephone varchar(20),
        date date,
        rue varchar(200),
        ville varchar(100),
        code_postal varchar(10),
        message text,
        carpet_selected varchar(100),
        surface decimal(5,2),
        steam_cleaner varchar(10),
        delivery_type varchar(50),
        delivery_cost decimal(6,2),
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Shortcode pentru afișarea formularului
function carpet_form_shortcode() {
    ob_start();
    include get_template_directory() . '/carpet-form-template.php';
    return ob_get_clean();
}
add_shortcode('carpet_form', 'carpet_form_shortcode');

// Hook pentru activarea temei (pentru crearea tabelului)
add_action('after_switch_theme', 'create_carpet_form_table');
?>