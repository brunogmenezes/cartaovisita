<?php
/**
 * API: Renderiza o cartão com dados do POST (preview ao vivo)
 * POST /drabarbarafernandes/api/preview.php
 * Aceita os mesmos campos do form de configurações e retorna HTML completo do cartão
 */
require_once __DIR__ . '/../includes/functions.php';

// Lê do POST ou cai de volta para o banco
function pv(string $key, string $default = ''): string {
    return isset($_POST[$key]) ? (string)$_POST[$key] : get_setting($key, $default);
}

$name         = pv('doctor_name',   'Dra. Barbara Fernandes');
$title_dr     = pv('doctor_title',  'Pediatra');
$crm          = pv('doctor_crm',    'CRMMA 13262');
$rqe          = pv('doctor_rqe',    'RQE 7335');
$bio          = pv('doctor_bio',    '');
$wa           = pv('whatsapp_number', '559984225102');
$insta        = pv('instagram_handle', 'drabarbara.fernandes');
$feat_url     = pv('featured_link_url',   'https://sono-do-bebe.netlify.app/');
$feat_title   = pv('featured_link_title', 'Quero entender melhor o sono do meu bebê');
$feat_tag     = pv('featured_link_tag',   'Guia Gratuito');

// Cores customizadas
$c_rose       = pv('color_rose',       '#e8628a');
$c_lavender   = pv('color_lavender',   '#b39ddb');
$c_mint       = pv('color_mint',       '#69c5b0');
$c_bg_start   = pv('color_bg_start',   '#f5e8f0');
$c_bg_mid     = pv('color_bg_mid',     '#ede8f8');
$c_bg_end     = pv('color_bg_end',     '#e0f4ef');
$c_text       = pv('color_text',       '#2d2235');

// Tipografia
$font_body    = pv('font_family',      'Nunito');
$font_size    = max(12, min(22, (int) pv('font_size_base', '15')));

// Foto (sempre do banco)
$photo = get_setting('profile_photo', 'profile.png');
$photo_src = $photo === 'profile.png'
    ? BASE_PATH . '/profile.png'
    : BASE_PATH . '/uploads/' . $photo;

// Chips
$chips_raw  = isset($_POST['specialty_chips']) ? $_POST['specialty_chips'] : get_setting('specialty_chips', '[]');
$chips      = json_decode($chips_raw, true) ?: [];

// URL base para assets funcionarem dentro do iframe (base tag)
$proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base_url = $proto . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH . '/';

// Número formatado
$wa_fmt = preg_replace('/^55(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $wa);

function peh(string $s): string { return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <base href="<?= peh($base_url) ?>"/>
  <title>Preview</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($font_body) ?>:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style id="custom-vars">
    :root {
      --rose:           <?= peh($c_rose) ?>;
      --rose-light:     <?= peh($c_rose) ?>99;
      --rose-pale:      <?= peh($c_rose) ?>22;
      --lavender:       <?= peh($c_lavender) ?>;
      --lavender-light: <?= peh($c_lavender) ?>44;
      --mint:           <?= peh($c_mint) ?>;
      --mint-light:     <?= peh($c_mint) ?>55;
      --text-dark:      <?= peh($c_text) ?>;
    }
    body {
      background: linear-gradient(145deg, <?= peh($c_bg_start) ?> 0%, <?= peh($c_bg_mid) ?> 40%, <?= peh($c_bg_end) ?> 100%) !important;
      font-family: '<?= peh($font_body) ?>', system-ui, sans-serif !important;
      font-size: <?= $font_size ?>px !important;
    }
  </style>
</head>
<body>
  <div class="bg-blob blob-1"></div>
  <div class="bg-blob blob-2"></div>
  <div class="bg-blob blob-3"></div>

  <main class="card-wrapper">

    <section class="hero">
      <div class="hero-bg-pattern"></div>
      <div class="profile-avatar-wrap">
        <div class="avatar-ring">
          <img src="<?= peh($photo_src) ?>" alt="Foto" class="avatar-img"/>
        </div>
        <div class="avatar-badge">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-8v4h4l-5 8z"/>
          </svg>
        </div>
      </div>
      <h1 class="hero-name"><?= nl2br(peh($name)) ?></h1>
      <p class="hero-title"><span class="title-chip"><?= peh($title_dr) ?></span></p>
      <p class="hero-crm"><?= peh($crm) ?> &nbsp;|&nbsp; <?= peh($rqe) ?></p>
      <p class="hero-bio"><?= peh($bio) ?></p>
      <div class="hero-divider"><span>✦</span></div>
    </section>

    <section class="section-block">
      <h2 class="section-label">Agende sua consulta</h2>
      <div class="action-card wa-card" style="cursor:pointer">
        <span class="action-icon wa-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor" width="20" height="20">
            <path d="M16 2C8.268 2 2 8.268 2 16c0 2.476.651 4.8 1.789 6.82L2 30l7.38-1.773A13.93 13.93 0 0 0 16 30c7.732 0 14-6.268 14-14S23.732 2 16 2z"/>
          </svg>
        </span>
        <div class="action-text">
          <span class="action-label">WhatsApp · Agendar Consulta</span>
          <span class="action-value"><?= peh($wa_fmt ?: $wa) ?></span>
        </div>
        <span class="action-chevron">›</span>
      </div>

      <div class="action-card instagram-card" style="cursor:pointer">
        <span class="action-icon insta-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
        </span>
        <div class="action-text">
          <span class="action-label">Instagram</span>
          <span class="action-value">@<?= peh($insta) ?></span>
        </div>
        <span class="action-chevron">›</span>
      </div>
    </section>

    <?php if ($feat_url): ?>
    <section class="section-block">
      <h2 class="section-label">Conteúdo Exclusivo</h2>
      <div class="featured-card" style="cursor:pointer">
        <div class="featured-icon-wrap">
          <div class="featured-icon">🌙</div>
        </div>
        <div class="featured-content">
          <span class="featured-tag"><?= peh($feat_tag) ?></span>
          <h3 class="featured-title"><?= peh($feat_title) ?></h3>
          <span class="featured-link-label">Acessar guia →</span>
        </div>
        <div class="featured-glow"></div>
      </div>
    </section>
    <?php endif ?>

    <?php if ($chips): ?>
    <section class="section-block">
      <h2 class="section-label">Áreas de Atuação</h2>
      <div class="chips-grid">
        <?php foreach ($chips as $chip): ?>
        <span class="chip"><?= peh($chip) ?></span>
        <?php endforeach ?>
      </div>
    </section>
    <?php endif ?>

    <footer class="card-footer">
      <div class="footer-stethoscope">🩺</div>
      <p class="footer-tagline">Cuidando com amor e ciência</p>
      <p class="footer-copy">© <?= date('Y') ?> <?= peh($name) ?></p>
    </footer>

  </main>
</body>
</html>
