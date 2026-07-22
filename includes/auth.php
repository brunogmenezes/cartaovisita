<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function start_admin_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(ADMIN_SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 60 * 60 * 8,   // 8 horas
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function is_logged_in(): bool {
    start_admin_session();
    return !empty($_SESSION['admin_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_PATH . '/admin/index.php');
        exit;
    }
}

function admin_login(string $username, string $password): bool {
    try {
        $db   = get_db();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row  = $stmt->fetch();
        if ($row && password_verify($password, $row['password_hash'])) {
            start_admin_session();
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_user'] = $row['username'];
            return true;
        }
    } catch (Throwable $e) {}
    return false;
}

function admin_logout(): void {
    start_admin_session();
    $_SESSION = [];
    session_destroy();
}

function current_admin(): string {
    start_admin_session();
    return $_SESSION['admin_user'] ?? 'Admin';
}
