<?php
require_once __DIR__ . '/includes/functions.php';

// Carrega todas as configurações
$name    = get_setting('doctor_name',    'Dra. Barbara Fernandes');
$title   = get_setting('doctor_title',   'Pediatra');
$crm     = get_setting('doctor_crm',     'CRMMA 13262');
$rqe     = get_setting('doctor_rqe',     'RQE 7335');
$bio     = get_setting('doctor_bio',     'Pediatra dedicada ao cuidado integral da criança.');
$wa      = get_setting('whatsapp_number','559984225102');
$insta   = get_setting('instagram_handle','drabarbara.fernandes');
// Múltiplos links em destaque (carrega do DB em formato JSON)
$featured_links_raw = get_setting('featured_links', '[]');
$featured_links     = json_decode($featured_links_raw, true);

// Se vazio, cai de volta para o padrão inicial
if (empty($featured_links)) {
    $featured_links = [[
        'url'   => 'https://sono-do-bebe.netlify.app/',
        'title' => 'Quero entender melhor o sono do meu bebê',
        'tag'   => 'Guia Gratuito',
        'emoji' => '🌙'
    ]];
}
$photo   = get_setting('profile_photo', 'profile.png');
$chips   = json_decode(get_setting('specialty_chips', '[]'), true) ?: [
    '👶 Neonatologia', '🧠 Desenvolvimento Infantil', '🥗 Nutrição Pediátrica',
    '💉 Vacinação', '🌙 Sono Infantil', '🤱 Amamentação',
    '🩺 Check-up Preventivo', '👨‍👩‍👧 Orientação Familiar',
];

// Visual / Aparência
$c_rose       = get_setting('color_rose',     '#e8628a');
$c_lavender   = get_setting('color_lavender', '#b39ddb');
$c_mint       = get_setting('color_mint',     '#69c5b0');
$c_bg_start   = get_setting('color_bg_start', '#f5e8f0');
$c_bg_mid     = get_setting('color_bg_mid',   '#ede8f8');
$c_bg_end     = get_setting('color_bg_end',   '#e0f4ef');
$c_text       = get_setting('color_text',     '#2d2235');
$font_family  = get_setting('font_family',    'Nunito');
$font_size    = max(12, min(22, (int) get_setting('font_size_base', '15')));

// Resolve URL da foto
$photo_src = strpos($photo, '/') === 0 ? $photo : BASE_PATH . '/uploads/' . $photo;
if ($photo === 'profile.png') {
    $photo_src = BASE_PATH . '/profile.png';
}

function h2(string $s): string { return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h2($name) ?> – <?= h2($title) ?> | <?= h2($crm) ?></title>
  <meta name="description" content="<?= h2($name) ?> – <?= h2($title) ?>. Atendimento baseado em evidências científicas. <?= h2($crm) ?> / <?= h2($rqe) ?>." />
  <meta property="og:title" content="<?= h2($name) ?> – <?= h2($title) ?>" />
  <meta property="og:description" content="<?= h2($bio) ?>" />
  <meta property="og:type" content="website" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($font_family) ?>:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_PATH ?>/style.css" />
  <style>
    :root {
      --rose:           <?= h2($c_rose) ?>;
      --rose-light:     <?= h2($c_rose) ?>99;
      --rose-pale:      <?= h2($c_rose) ?>22;
      --lavender:       <?= h2($c_lavender) ?>;
      --lavender-light: <?= h2($c_lavender) ?>44;
      --mint:           <?= h2($c_mint) ?>;
      --mint-light:     <?= h2($c_mint) ?>55;
      --text-dark:      <?= h2($c_text) ?>;
    }
    body {
      background: linear-gradient(145deg, <?= h2($c_bg_start) ?> 0%, <?= h2($c_bg_mid) ?> 40%, <?= h2($c_bg_end) ?> 100%) !important;
      font-family: '<?= h2($font_family) ?>', system-ui, sans-serif !important;
      font-size: <?= $font_size ?>px !important;
    }
  </style>
</head>
<body>

  <div class="bg-blob blob-1"></div>
  <div class="bg-blob blob-2"></div>
  <div class="bg-blob blob-3"></div>

  <main class="card-wrapper" id="main-content">

    <!-- ===== HERO / PROFILE ===== -->
    <section class="hero" aria-label="Perfil da médica">
      <div class="hero-bg-pattern"></div>

      <div class="profile-avatar-wrap">
        <div class="avatar-ring">
          <img src="<?= h2($photo_src) ?>" alt="Foto de <?= h2($name) ?>" class="avatar-img" id="profile-photo" />
        </div>
        <div class="avatar-badge" aria-label="<?= h2($title) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-8v4h4l-5 8z"/></svg>
        </div>
      </div>

      <h1 class="hero-name"><?= h2($name) ?></h1>
      <p class="hero-title"><span class="title-chip"><?= h2($title) ?></span></p>
      <p class="hero-crm"><?= h2($crm) ?> &nbsp;|&nbsp; <?= h2($rqe) ?></p>
      <p class="hero-bio"><?= h2($bio) ?></p>
      <div class="hero-divider"><span>✦</span></div>
    </section>

    <!-- ===== CONTACT ACTIONS ===== -->
    <section class="section-block" aria-label="Agendamento e contato">
      <h2 class="section-label">Agende sua consulta</h2>

      <!-- Botão WhatsApp com número em destaque -->
      <button class="action-card wa-card" id="btn-whatsapp"
              data-phone="<?= h2($wa) ?>"
              aria-label="Agendar consulta pelo WhatsApp">
        <span class="action-icon wa-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor" width="20" height="20">
            <path d="M16 2C8.268 2 2 8.268 2 16c0 2.476.651 4.8 1.789 6.82L2 30l7.38-1.773A13.93 13.93 0 0 0 16 30c7.732 0 14-6.268 14-14S23.732 2 16 2zm0 25.6c-2.196 0-4.27-.582-6.063-1.6l-.434-.255-4.38 1.052 1.073-4.27-.28-.445A11.56 11.56 0 0 1 4.4 16C4.4 9.592 9.592 4.4 16 4.4S27.6 9.592 27.6 16 22.408 27.6 16 27.6zm6.37-8.67c-.35-.175-2.065-1.02-2.385-1.135-.32-.115-.553-.175-.785.175s-.9 1.135-1.103 1.368c-.203.232-.406.261-.757.087-.35-.175-1.477-.544-2.814-1.736-1.04-.928-1.742-2.073-1.946-2.423-.203-.35-.022-.538.152-.713.157-.156.35-.407.525-.61.175-.204.233-.35.35-.583.116-.233.058-.436-.029-.61s-.785-1.894-1.077-2.594c-.283-.68-.57-.588-.785-.598l-.668-.011c-.233 0-.61.087-.928.436s-1.22 1.194-1.22 2.91 1.25 3.375 1.424 3.608c.175.233 2.46 3.76 5.96 5.273.833.36 1.484.574 1.99.735.836.265 1.597.227 2.198.138.67-.1 2.065-.843 2.356-1.658.29-.814.29-1.512.203-1.658-.087-.145-.32-.232-.668-.407z"/>
          </svg>
        </span>
        <div class="action-text">
          <span class="action-label">WhatsApp · Agendar Consulta</span>
          <span class="action-value"><?= h2(preg_replace('/^55(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $wa)) ?></span>
        </div>
        <span class="action-chevron">›</span>
      </button>

      <div class="action-card instagram-card" id="btn-instagram" style="cursor:pointer" data-url="https://www.instagram.com/<?= h2($insta) ?>">
        <span class="action-icon insta-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
        </span>
        <div class="action-text">
          <span class="action-label">Instagram</span>
          <span class="action-value">@<?= h2($insta) ?></span>
        </div>
        <span class="action-chevron">›</span>
      </div>
    </section>

    <!-- ===== FEATURED LINK ===== -->
    <?php if (!empty($featured_links)): ?>
    <section class="section-block" aria-label="Conteúdo especial">
      <h2 class="section-label">Conteúdo Exclusivo</h2>

      <div style="display:flex;flex-direction:column;gap:12px">
        <?php foreach ($featured_links as $index => $link):
            $f_url   = $link['url'] ?? '';
            $f_title = $link['title'] ?? '';
            $f_tag   = $link['tag'] ?? 'Destaque';
            $f_emoji = $link['emoji'] ?? '🌙';
            if (!$f_url) continue;
        ?>
        <div class="featured-card featured-lead-btn" data-url="<?= h2($f_url) ?>">
          <div class="featured-icon-wrap">
            <div class="featured-icon"><?= h2($f_emoji) ?></div>
          </div>
          <div class="featured-content">
            <span class="featured-tag"><?= h2($f_tag) ?></span>
            <h3 class="featured-title"><?= h2($f_title) ?></h3>
            <span class="featured-link-label">Acessar guia →</span>
          </div>
          <div class="featured-glow"></div>
        </div>
        <?php endforeach ?>
      </div>
    </section>
    <?php endif ?>

    <!-- ===== SPECIALTY CHIPS ===== -->
    <section class="section-block" aria-label="Especialidades">
      <h2 class="section-label">Áreas de Atuação</h2>
      <div class="chips-grid">
        <?php foreach ($chips as $i => $chip): ?>
        <span class="chip" id="chip-<?= $i ?>"><?= h2($chip) ?></span>
        <?php endforeach ?>
      </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="card-footer" aria-label="Rodapé">
      <div class="footer-stethoscope">🩺</div>
      <p class="footer-tagline">Cuidando com amor e ciência</p>
      <p class="footer-copy">© <?= date('Y') ?> <?= h2($name) ?> &nbsp;·&nbsp; Todos os direitos reservados</p>
    </footer>

  </main>

  <!-- ===== MODAL WHATSAPP ===== -->
  <div class="modal-overlay" id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-box">
      <button class="modal-close" id="modal-close" aria-label="Fechar">✕</button>

      <div class="modal-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor" width="32" height="32">
          <path d="M16 2C8.268 2 2 8.268 2 16c0 2.476.651 4.8 1.789 6.82L2 30l7.38-1.773A13.93 13.93 0 0 0 16 30c7.732 0 14-6.268 14-14S23.732 2 16 2z"/>
        </svg>
      </div>

      <h2 class="modal-title" id="modal-title">Antes de continuar…</h2>
      <p class="modal-subtitle">Informe seus dados para prosseguir com o agendamento</p>

      <form id="lead-form" novalidate>
        <div class="form-field">
          <label for="lead-name">Seu nome *</label>
          <input type="text" id="lead-name" name="name"
                 placeholder="Ex: Maria Silva"
                 autocomplete="name" required minlength="2" />
          <span class="field-error" id="err-name"></span>
        </div>

        <div class="form-field">
          <label for="lead-phone">Seu telefone *</label>
          <input type="tel" id="lead-phone" name="phone"
                 placeholder="(99) 99999-9999"
                 autocomplete="tel" required minlength="8" />
          <span class="field-error" id="err-phone"></span>
        </div>

        <button type="submit" class="modal-submit" id="modal-submit-btn">
          <span id="submit-text">Continuar para WhatsApp →</span>
          <span id="submit-loading" style="display:none">Aguarde…</span>
        </button>
      </form>

      <p class="modal-privacy">🔒 Seus dados são usados apenas para contato e não serão compartilhados.</p>
    </div>
  </div>

  <script>
    // Passa configs do PHP para o JS
    const APP_BASE = '<?= BASE_PATH ?>';
  </script>
  <script src="<?= BASE_PATH ?>/script.js"></script>
</body>
</html>
