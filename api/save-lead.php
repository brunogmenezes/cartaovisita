<?php
/**
 * API: Salva lead (nome + telefone) antes de redirecionar para WhatsApp
 * POST /drabarbarafernandes/api/save-lead.php
 * Body JSON: { "name": "...", "phone": "..." }
 * Retorna: { "ok": true, "whatsapp_url": "https://wa.me/..." }
 */
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$body  = json_decode(file_get_contents('php://input'), true);
$name  = trim($body['name']  ?? '');
$phone = trim($body['phone'] ?? '');

if (strlen($name) < 2) {
    json_response(['ok' => false, 'error' => 'Nome inválido'], 422);
}
if (strlen($phone) < 8) {
    json_response(['ok' => false, 'error' => 'Telefone inválido'], 422);
}

// Sanitize
$name  = substr($name,  0, 200);
$phone = substr(preg_replace('/\D/', '', $phone), 0, 40);

// Salva no banco
try {
    $db   = get_db();
    $stmt = $db->prepare(
        "INSERT INTO leads (name, phone, ip_address) VALUES (:n, :p, :ip)"
    );
    $stmt->execute([
        ':n'  => $name,
        ':p'  => $phone,
        ':ip' => get_client_ip(),
    ]);
} catch (Throwable $e) {
    // Não bloqueia o fluxo se o DB falhar
}

// Rastreia o clique
track_click('whatsapp');

// Monta URL do WhatsApp com mensagem personalizada
$wa_number = get_setting('whatsapp_number', '559984225102');
$wa_msg    = urlencode(
    "Olá, Dra. Barbara! Meu nome é {$name} e gostaria de agendar uma consulta."
);
$wa_url = "https://wa.me/{$wa_number}?text={$wa_msg}";

json_response(['ok' => true, 'whatsapp_url' => $wa_url]);
