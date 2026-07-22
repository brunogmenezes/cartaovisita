<?php
/**
 * API: Rastreia pageviews e cliques
 * POST /drabarbarafernandes/api/track.php
 * Body JSON: { "type": "pageview" | "whatsapp" | "phone" | "instagram" | "sleep_guide" }
 */
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$type = trim($body['type'] ?? '');

$allowed = ['pageview', 'whatsapp', 'phone', 'instagram', 'sleep_guide'];

// Allow dynamically named featured links, e.g. "featured_0", "featured_1" etc.
$is_featured = (strpos($type, 'featured_') === 0);

if (!in_array($type, $allowed, true) && !$is_featured) {
    json_response(['ok' => false, 'error' => 'Invalid event type'], 400);
}

if ($type === 'pageview') {
    track_pageview();
} else {
    track_click($type);
}

json_response(['ok' => true]);
