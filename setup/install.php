<?php
/**
 * INSTALAÇÃO – Acesse UMA VEZ em:
 * http://localhost/drabarbarafernandes/setup/install.php
 *
 * Cria todas as tabelas e insere o usuário admin padrão.
 * Usuário: admin | Senha: Admin@2025
 * (Altere a senha logo após o primeiro acesso!)
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/html; charset=utf-8');

$log = [];
$ok  = true;

function step(string $label, callable $fn, array &$log, bool &$ok): void {
    try {
        $fn();
        $log[] = ['ok' => true,  'msg' => $label];
    } catch (Throwable $e) {
        $log[] = ['ok' => false, 'msg' => "$label — ERRO: " . $e->getMessage()];
        $ok    = false;
    }
}

$db = get_db();

// 1. Tabela de configurações
step('Criar tabela settings', function () use ($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key        VARCHAR(120)  PRIMARY KEY,
            value      TEXT          NOT NULL DEFAULT '',
            updated_at TIMESTAMPTZ   NOT NULL DEFAULT NOW()
        )
    ");
}, $log, $ok);

// 2. Tabela de pageviews
step('Criar tabela page_views', function () use ($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS page_views (
            id          SERIAL        PRIMARY KEY,
            ip_address  VARCHAR(45)   NOT NULL DEFAULT '',
            user_agent  TEXT          NOT NULL DEFAULT '',
            referrer    TEXT          NOT NULL DEFAULT '',
            created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
        )
    ");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_pv_created ON page_views (created_at)");
}, $log, $ok);

// 3. Tabela de eventos de clique
step('Criar tabela click_events', function () use ($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS click_events (
            id          SERIAL        PRIMARY KEY,
            event_type  VARCHAR(100)  NOT NULL,
            ip_address  VARCHAR(45)   NOT NULL DEFAULT '',
            created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
        )
    ");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_ce_type    ON click_events (event_type)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_ce_created ON click_events (created_at)");
}, $log, $ok);

// 4. Tabela de leads
step('Criar tabela leads', function () use ($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id          SERIAL        PRIMARY KEY,
            name        VARCHAR(200)  NOT NULL DEFAULT '',
            phone       VARCHAR(40)   NOT NULL DEFAULT '',
            ip_address  VARCHAR(45)   NOT NULL DEFAULT '',
            created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW()
        )
    ");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_leads_created ON leads (created_at)");
}, $log, $ok);

// 5. Tabela de usuários admin
step('Criar tabela admin_users', function () use ($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id            SERIAL        PRIMARY KEY,
            username      VARCHAR(100)  UNIQUE NOT NULL,
            password_hash VARCHAR(255)  NOT NULL,
            created_at    TIMESTAMPTZ   NOT NULL DEFAULT NOW()
        )
    ");
}, $log, $ok);

// 6. Usuário admin padrão
step('Inserir usuário admin padrão (admin / Admin@2025)', function () use ($db) {
    $hash = password_hash('Admin@2025', PASSWORD_BCRYPT);
    $db->prepare(
        "INSERT INTO admin_users (username, password_hash)
         VALUES ('admin', :h)
         ON CONFLICT (username) DO NOTHING"
    )->execute([':h' => $hash]);
}, $log, $ok);

// 7. Configurações padrão
step('Inserir configurações padrão', function () use ($db) {
    $defaults = [
        'doctor_name'      => 'Dra. Barbara Fernandes',
        'doctor_title'     => 'Pediatra',
        'doctor_crm'       => 'CRMMA 13262',
        'doctor_rqe'       => 'RQE 7335',
        'doctor_bio'       => 'Pediatra dedicada ao cuidado integral da criança, oferecendo atendimento baseado em evidências científicas, acolhimento e orientação para as famílias. Meu propósito é promover saúde, desenvolvimento e bem-estar desde os primeiros dias de vida, construindo uma infância mais saudável e feliz.',
        'whatsapp_number'  => '559984225102',
        'instagram_handle' => 'drabarbara.fernandes',
        'featured_link_url'   => 'https://sono-do-bebe.netlify.app/',
        'featured_link_title' => 'Quero entender melhor o sono do meu bebê',
        'featured_link_tag'   => 'Guia Gratuito',
        'profile_photo'    => 'profile.png',
        'specialty_chips'  => json_encode([
            '👶 Neonatologia', '🧠 Desenvolvimento Infantil',
            '🥗 Nutrição Pediátrica', '💉 Vacinação',
            '🌙 Sono Infantil', '🤱 Amamentação',
            '🩺 Check-up Preventivo', '👨‍👩‍👧 Orientação Familiar',
        ], JSON_UNESCAPED_UNICODE),
    ];

    $stmt = $db->prepare(
        "INSERT INTO settings (key, value)
         VALUES (:k, :v)
         ON CONFLICT (key) DO NOTHING"
    );
    foreach ($defaults as $k => $v) {
        $stmt->execute([':k' => $k, ':v' => $v]);
    }
}, $log, $ok);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"/>
<title>Instalação – Dra. Barbara Fernandes</title>
<style>
  body{font-family:system-ui,sans-serif;background:#0f0a1e;color:#e0d6f5;display:flex;justify-content:center;padding:40px 16px}
  .box{max-width:560px;width:100%}
  h1{font-size:1.5rem;margin-bottom:24px;color:#e8628a}
  ul{list-style:none;padding:0}
  li{padding:10px 14px;border-radius:8px;margin-bottom:8px;font-size:.9rem;display:flex;gap:10px;align-items:center}
  .ok{background:#0d2a1a;border:1px solid #1a5a32;color:#4ade80}
  .err{background:#2a0d0d;border:1px solid #5a1a1a;color:#f87171}
  .done{margin-top:24px;padding:20px;border-radius:12px;background:#1a1040;border:1px solid #4a3860}
  a.btn{display:inline-block;margin-top:16px;padding:12px 24px;border-radius:8px;background:linear-gradient(135deg,#e8628a,#c94b77);color:#fff;text-decoration:none;font-weight:700}
  .cred{background:#120d28;border:1px solid #e8628a44;border-radius:8px;padding:16px;margin-bottom:16px}
  .cred p{margin:4px 0;font-size:.85rem;color:#c4b5e8}
  .cred strong{color:#e8628a}
</style>
</head>
<body>
<div class="box">
  <h1>🩺 Instalação do Sistema</h1>

  <div class="cred">
    <p><strong>Credenciais de acesso ao admin:</strong></p>
    <p>Usuário: <strong>admin</strong></p>
    <p>Senha: <strong>Admin@2025</strong></p>
    <p style="margin-top:8px;color:#f87171;font-size:.8rem">⚠️ Altere a senha após o primeiro acesso!</p>
  </div>

  <ul>
    <?php foreach ($log as $item): ?>
    <li class="<?= $item['ok'] ? 'ok' : 'err' ?>">
      <span><?= $item['ok'] ? '✅' : '❌' ?></span>
      <span><?= htmlspecialchars($item['msg']) ?></span>
    </li>
    <?php endforeach ?>
  </ul>

  <?php if ($ok): ?>
  <div class="done">
    <p>✅ <strong>Instalação concluída com sucesso!</strong></p>
    <p style="font-size:.85rem;color:#a78bca;margin-top:8px">Por segurança, delete ou proteja a pasta <code>setup/</code> após a instalação.</p>
    <a class="btn" href="/drabarbarafernandes/admin/index.php">Ir para o Admin →</a>
  </div>
  <?php else: ?>
  <div class="done" style="border-color:#f87171">
    <p>❌ <strong>Alguns passos falharam.</strong> Verifique as credenciais do PostgreSQL em <code>includes/config.php</code> e tente novamente.</p>
  </div>
  <?php endif ?>
</div>
</body>
</html>
