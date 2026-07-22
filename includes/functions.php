<?php
require_once __DIR__ . '/db.php';

// ---- Settings -------------------------------------------------------

function get_setting(string $key, string $default = ''): string {
    try {
        $db   = get_db();
        $stmt = $db->prepare("SELECT value FROM settings WHERE key = :k");
        $stmt->execute([':k' => $key]);
        $row  = $stmt->fetch();
        return ($row !== false) ? (string)$row['value'] : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function set_setting(string $key, string $value): void {
    $db   = get_db();
    $stmt = $db->prepare(
        "INSERT INTO settings (key, value, updated_at)
         VALUES (:k, :v, NOW())
         ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()"
    );
    $stmt->execute([':k' => $key, ':v' => $value]);
}

// ---- Analytics ------------------------------------------------------

function get_client_ip(): string {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) {
            return trim(explode(',', $_SERVER[$h])[0]);
        }
    }
    return '0.0.0.0';
}

function track_pageview(): void {
    try {
        $db   = get_db();
        $stmt = $db->prepare(
            "INSERT INTO page_views (ip_address, user_agent, referrer)
             VALUES (:ip, :ua, :ref)"
        );
        $stmt->execute([
            ':ip'  => get_client_ip(),
            ':ua'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
            ':ref' => substr($_SERVER['HTTP_REFERER']    ?? '', 0, 512),
        ]);
    } catch (Throwable $e) { /* silently ignore */ }
}

function track_click(string $event_type): void {
    try {
        $db   = get_db();
        $stmt = $db->prepare(
            "INSERT INTO click_events (event_type, ip_address) VALUES (:ev, :ip)"
        );
        $stmt->execute([':ev' => $event_type, ':ip' => get_client_ip()]);
    } catch (Throwable $e) { /* silently ignore */ }
}

// ---- Misc -----------------------------------------------------------

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
