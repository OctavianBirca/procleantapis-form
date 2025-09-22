<?php
/**
 * Plugin Name: ProClean - Formulaire Devis
 * Description: Formulaire multi-services (Tapis, Textile, Literie, Professionnels) cu AJAX & WhatsApp.
 * Version: 1.0.0
 * Author: ProClean
 */

if (!defined('ABSPATH')) { exit; }

class Proclean_Devis_Form_Plugin {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_shortcode('proclean_devis_form', [$this, 'render_form']);
        // Aliases/diagnostics
        add_shortcode('pcdf', [$this, 'render_form']);
        add_shortcode('pcdf_test', function() { return '<div style="padding:12px;border:1px solid #ccc">PCDF shortcode OK</div>'; });

        add_action('wp_ajax_submit_carpet_form', [$this, 'handle_submit']);
        add_action('wp_ajax_nopriv_submit_carpet_form', [$this, 'handle_submit']);

        if (!function_exists('wp_mail_smtp')) {
            add_action('phpmailer_init', [$this, 'configure_ionos_smtp']);
        }
    }

    public function assets() {
        $base = plugin_dir_url(__FILE__);
        wp_enqueue_style('proclean-form', $base . 'assets/css/style.css', [], '1.0');
        wp_enqueue_script('proclean-form', $base . 'assets/js/script.js', [], '1.0', true);
        wp_localize_script('proclean-form', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('carpet_form_nonce')
        ]);
    }

    public function render_form() {
        if (function_exists('error_log')) { error_log('[PCDF] render_form start'); }
        try {
            ob_start();
            include __DIR__ . '/template.php';
            $out = ob_get_clean();
            if (function_exists('error_log')) { error_log('[PCDF] render_form ok'); }
            return $out;
        } catch (\Throwable $e) {
            if (function_exists('error_log')) { error_log('[PCDF][ERROR] ' . $e->getMessage()); }
            return '<div>PCDF error – see debug log</div>';
        }
    }

    public function configure_ionos_smtp($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.ionos.fr';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 587;
        $phpmailer->Username = 'info@procleantapis.fr';
        $phpmailer->Password = 'CHANGEZ-MOI';
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->From = 'info@procleantapis.fr';
        $phpmailer->FromName = get_bloginfo('name');
    }

    public function handle_submit() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'carpet_form_nonce')) {
            wp_send_json_error('Token invalide');
        }

        $nom = sanitize_text_field($_POST['nom'] ?? '');
        $email = sanitize_email($_POST['courriel'] ?? '');
        if (!$nom || !is_email($email)) {
            wp_send_json_error('Nom/Email manquant');
        }

        $telephone = sanitize_text_field($_POST['telephone'] ?? '');
        $date = sanitize_text_field($_POST['date'] ?? '');
        $rue = sanitize_text_field($_POST['rue'] ?? '');
        $ville = sanitize_text_field($_POST['ville'] ?? '');
        $code_postal = sanitize_text_field($_POST['code-postal'] ?? '');
        $message_txt = sanitize_textarea_field($_POST['message'] ?? '');

        $carpet_selected = sanitize_text_field($_POST['carpet_selected'] ?? '');
        $surface = floatval($_POST['surface'] ?? 0);
        $steam_cleaner = sanitize_text_field($_POST['steam_cleaner'] ?? '');
        $delivery_type = sanitize_text_field($_POST['delivery_type'] ?? '');
        $delivery_cost = floatval($_POST['delivery_cost'] ?? 0);

        $to = 'info@procleantapis.fr';
        $subject = 'Nouvelle demande de devis - ' . $nom;

        $body = "Demande de devis\n\n";
        $body .= "Nom: $nom\nEmail: $email\nTéléphone: $telephone\nDate: $date\n";
        $body .= "Adresse: $rue, $ville $code_postal\n\n";
        if ($carpet_selected) {
            $body .= "Service: $carpet_selected\nSurface: $surface m²\nVapeur: $steam_cleaner\nLivraison: $delivery_type\nCoût: $delivery_cost €\n\n";
        }
        $body .= "Message:\n$message_txt\n";

        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . $email,
        ];

        $sent = wp_mail($to, $subject, $body, $headers);
        if ($sent) {
            wp_send_json_success('OK');
        }
        wp_send_json_error('Mail non envoyé');
    }
}

new Proclean_Devis_Form_Plugin();
