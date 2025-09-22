<?php
/**
 * Script PHP pentru trimiterea formularului pe IONOS hosting
 * Pentru site-uri statice (non-WordPress)
 */

// Configurare pentru IONOS
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers pentru CORS (dacă este necesar)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verifică dacă este request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

// Funcție pentru sanitizarea datelor
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Colectează și sanitizează datele
$data = array(
    'nom' => sanitize_input($_POST['nom'] ?? ''),
    'email' => sanitize_input($_POST['courriel'] ?? ''),
    'telephone' => sanitize_input($_POST['telephone'] ?? ''),
    'date' => sanitize_input($_POST['date'] ?? ''),
    'rue' => sanitize_input($_POST['rue'] ?? ''),
    'ville' => sanitize_input($_POST['ville'] ?? ''),
    'code_postal' => sanitize_input($_POST['code-postal'] ?? ''),
    'message' => sanitize_input($_POST['message'] ?? ''),
    'carpet_selected' => sanitize_input($_POST['carpet_selected'] ?? ''),
    'surface' => floatval($_POST['surface'] ?? 0),
    'steam_cleaner' => sanitize_input($_POST['steam_cleaner'] ?? ''),
    'delivery_type' => sanitize_input($_POST['delivery_type'] ?? ''),
    'delivery_cost' => floatval($_POST['delivery_cost'] ?? 0)
);

// Validare de bază
if (empty($data['nom']) || empty($data['email'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Le nom et l\'email sont obligatoires']));
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Email invalide']));
}

// Configuration email IONOS
$to_email = 'info@procleantapis.fr'; // Email actualizat
$subject = 'Nouvelle demande de devis - ' . $data['nom'];

// Construire mesaj
$message = "Nouvelle demande de devis reçue:\n\n";
$message .= "═══════════════════════════════════\n";
$message .= "INFORMATIONS PERSONNELLES\n";
$message .= "═══════════════════════════════════\n";
$message .= "Nom: " . $data['nom'] . "\n";
$message .= "Email: " . $data['email'] . "\n";
$message .= "Téléphone: " . $data['telephone'] . "\n";
$message .= "Date souhaitée: " . $data['date'] . "\n";
$message .= "Adresse: " . $data['rue'] . "\n";
$message .= "Ville: " . $data['ville'] . "\n";
$message .= "Code postal: " . $data['code_postal'] . "\n\n";

if (!empty($data['carpet_selected'])) {
    $message .= "═══════════════════════════════════\n";
    $message .= "DÉTAILS DU SERVICE\n";
    $message .= "═══════════════════════════════════\n";
    $message .= "Service sélectionné: " . $data['carpet_selected'] . "\n";
    $message .= "Surface: " . $data['surface'] . " m²\n";
    $message .= "Nettoyage vapeur: " . ($data['steam_cleaner'] === 'true' ? 'Oui' : 'Non') . "\n";
    $message .= "Type de livraison: " . $data['delivery_type'] . "\n";
    $message .= "Coût livraison: " . $data['delivery_cost'] . " €\n\n";
}

if (!empty($data['message'])) {
    $message .= "═══════════════════════════════════\n";
    $message .= "MESSAGE DU CLIENT\n";
    $message .= "═══════════════════════════════════\n";
    $message .= $data['message'] . "\n\n";
}

$message .= "═══════════════════════════════════\n";
$message .= "Demande envoyée le: " . date('d/m/Y à H:i:s') . "\n";
$message .= "IP du client: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Headers email
$headers = array(
    'From: ' . $data['nom'] . ' <' . $data['email'] . '>',
    'Reply-To: ' . $data['email'],
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
);

// Essayer d'envoyer l'email
try {
    $sent = mail($to_email, $subject, $message, implode("\r\n", $headers));
    
    if ($sent) {
        // Log pour debugging
        error_log("Carpet form submission successful: " . $data['email']);
        
        // Sauvegarde dans fichier (optionnel)
        $log_entry = date('[Y-m-d H:i:s] ') . "Form submitted by: " . $data['nom'] . " (" . $data['email'] . ")\n";
        file_put_contents('form_submissions.log', $log_entry, FILE_APPEND | LOCK_EX);
        
        // Réponse de succès
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre demande a été envoyée avec succès!'
        ]);
    } else {
        throw new Exception('Échec de l\'envoi de l\'email');
    }
} catch (Exception $e) {
    error_log("Carpet form submission failed: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de l\'envoi de votre demande. Veuillez réessayer.'
    ]);
}
?>