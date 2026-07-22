<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . BASE_PATH . '/admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (admin_login($username, $password)) {
        header('Location: ' . BASE_PATH . '/admin/dashboard.php');
        exit;
    }
    $error = 'Usuário ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Login – Admin | Dra. Barbara Fernandes</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/admin/style.css"/>
</head>
<body class="login-page">

<div class="login-box">
  <div class="login-logo">🩺</div>
  <h1 class="login-title">Área Administrativa</h1>
  <p class="login-sub">Entre com suas credenciais para continuar</p>

  <?php if ($error): ?>
  <div class="alert alert-error">⚠️ <?= h($error) ?></div>
  <?php endif ?>

  <form method="POST" class="login-form" autocomplete="on">
    <div class="form-group">
      <label class="form-label" for="username">Usuário</label>
      <input class="form-input" type="text" id="username" name="username"
             autocomplete="username" required
             value="<?= h($_POST['username'] ?? '') ?>" />
    </div>

    <div class="form-group">
      <label class="form-label" for="password">Senha</label>
      <input class="form-input" type="password" id="password" name="password"
             autocomplete="current-password" required />
    </div>

    <button type="submit" class="login-submit" id="btn-login">
      Entrar no Painel →
    </button>
  </form>

  <p class="login-footer">Dra. Barbara Fernandes &middot; Cartão Virtual</p>
</div>

</body>
</html>
