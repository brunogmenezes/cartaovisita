<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_layout.php';

require_login();

$success = '';
$error   = '';

$all_fields = [
    'doctor_name','doctor_title','doctor_crm','doctor_rqe','doctor_bio',
    'whatsapp_number','instagram_handle',
    'featured_link_url','featured_link_title','featured_link_tag',
    // Visual
    'color_rose','color_lavender','color_mint',
    'color_bg_start','color_bg_mid','color_bg_end','color_text',
    'font_family','font_size_base',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($all_fields as $key) {
            if (isset($_POST[$key])) {
                set_setting($key, trim($_POST[$key]));
            }
        }
        if (isset($_POST['specialty_chips'])) {
            $arr = json_decode($_POST['specialty_chips'], true);
            if (is_array($arr)) {
                $arr = array_values(array_filter(array_map('trim', $arr)));
                set_setting('specialty_chips', json_encode($arr, JSON_UNESCAPED_UNICODE));
            }
        }
        $success = 'Configurações salvas com sucesso!';
    } catch (Throwable $e) {
        $error = 'Erro ao salvar: ' . $e->getMessage();
    }
}

// Defaults
$def = [
    'doctor_name'         => 'Dra. Barbara Fernandes',
    'doctor_title'        => 'Pediatra',
    'doctor_crm'          => 'CRMMA 13262',
    'doctor_rqe'          => 'RQE 7335',
    'doctor_bio'          => '',
    'whatsapp_number'     => '559984225102',
    'instagram_handle'    => 'drabarbara.fernandes',
    'featured_link_url'   => 'https://sono-do-bebe.netlify.app/',
    'featured_link_title' => 'Quero entender melhor o sono do meu bebê',
    'featured_link_tag'   => 'Guia Gratuito',
    'color_rose'          => '#e8628a',
    'color_lavender'      => '#b39ddb',
    'color_mint'          => '#69c5b0',
    'color_bg_start'      => '#f5e8f0',
    'color_bg_mid'        => '#ede8f8',
    'color_bg_end'        => '#e0f4ef',
    'color_text'          => '#2d2235',
    'font_family'         => 'Nunito',
    'font_size_base'      => '15',
    'specialty_chips'     => '[]',
];

$s = [];
foreach ($all_fields as $k) {
    $s[$k] = get_setting($k, $def[$k] ?? '');
}
$s['specialty_chips'] = get_setting('specialty_chips', '[]');
$chips = json_decode($s['specialty_chips'], true) ?: [];

$google_fonts = ['Nunito','Inter','Poppins','Roboto','Lato','Open Sans','Raleway','DM Sans','Outfit','Plus Jakarta Sans'];

admin_page_start('Configurações', 'settings');
?>

<div class="page-header">
  <h1 class="page-title">⚙️ Configurações do Cartão</h1>
  <p class="page-sub">As alterações são refletidas no preview ao vivo →</p>
</div>

<?php if ($success): ?><div class="alert alert-success">✅ <?= h($success) ?></div><?php endif ?>
<?php if ($error):   ?><div class="alert alert-error">❌ <?= h($error)   ?></div><?php endif ?>

<!-- SPLIT LAYOUT -->
<div class="settings-split">

  <!-- ======= LEFT: FORM ======= -->
  <div class="settings-panels">
  <form id="settings-form" method="POST">
    <input type="hidden" name="specialty_chips" id="chips-json-input" value='<?= h($s['specialty_chips']) ?>'/>

    <div class="settings-grid">

      <!-- PERFIL -->
      <div class="settings-card">
        <h2 class="settings-card-title">👩‍⚕️ Perfil da Médica</h2>
        <div class="form-group">
          <label class="form-label" for="doctor_name">Nome completo</label>
          <input class="form-input" type="text" id="doctor_name" name="doctor_name" value="<?= h($s['doctor_name']) ?>" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="doctor_title">Título / Especialidade</label>
          <input class="form-input" type="text" id="doctor_title" name="doctor_title" value="<?= h($s['doctor_title']) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="doctor_crm">CRM</label>
          <input class="form-input" type="text" id="doctor_crm" name="doctor_crm" value="<?= h($s['doctor_crm']) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="doctor_rqe">RQE</label>
          <input class="form-input" type="text" id="doctor_rqe" name="doctor_rqe" value="<?= h($s['doctor_rqe']) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="doctor_bio">Biografia</label>
          <textarea class="form-textarea" id="doctor_bio" name="doctor_bio" rows="5"><?= h($s['doctor_bio']) ?></textarea>
        </div>
      </div>

      <!-- CONTATO -->
      <div class="settings-card">
        <h2 class="settings-card-title">📞 Contato & Redes Sociais</h2>
        <div class="form-group">
          <label class="form-label" for="whatsapp_number">Número do WhatsApp</label>
          <input class="form-input" type="text" id="whatsapp_number" name="whatsapp_number" value="<?= h($s['whatsapp_number']) ?>" placeholder="559984225102"/>
          <p class="form-hint">Com código do país, sem espaços. Ex: 559984225102</p>
        </div>
        <div class="form-group">
          <label class="form-label" for="instagram_handle">Instagram</label>
          <div style="display:flex;align-items:center;gap:4px">
            <span style="color:var(--text-muted);font-size:.9rem;padding:10px 8px 10px 14px;background:var(--surface2);border-radius:10px 0 0 10px;border:1.5px solid rgba(255,255,255,.1);border-right:none">@</span>
            <input class="form-input" type="text" id="instagram_handle" name="instagram_handle" value="<?= h($s['instagram_handle']) ?>" style="border-radius:0 10px 10px 0"/>
          </div>
        </div>
        <div style="padding:14px;background:var(--surface2);border-radius:10px;margin-top:4px">
          <p style="font-size:.72rem;color:var(--text-muted);margin-bottom:6px;font-weight:700">📍 Link WhatsApp:</p>
          <p style="font-size:.76rem;color:var(--mint);word-break:break-all" id="wa-preview">https://wa.me/<?= h($s['whatsapp_number']) ?></p>
        </div>
      </div>

      <!-- LINK EM DESTAQUE -->
      <div class="settings-card">
        <h2 class="settings-card-title">🌙 Link em Destaque</h2>
        <div class="form-group">
          <label class="form-label" for="featured_link_tag">Rótulo (tag)</label>
          <input class="form-input" type="text" id="featured_link_tag" name="featured_link_tag" value="<?= h($s['featured_link_tag']) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="featured_link_title">Título do link</label>
          <input class="form-input" type="text" id="featured_link_title" name="featured_link_title" value="<?= h($s['featured_link_title']) ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="featured_link_url">URL de destino</label>
          <input class="form-input" type="url" id="featured_link_url" name="featured_link_url" value="<?= h($s['featured_link_url']) ?>"/>
        </div>
      </div>

      <!-- CHIPS -->
      <div class="settings-card">
        <h2 class="settings-card-title">🏷️ Áreas de Atuação</h2>
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px">Clique em ✕ para remover.</p>
        <div class="chips-editor" id="chips-editor">
          <?php foreach ($chips as $chip): ?>
          <span class="chip-item" data-value="<?= h($chip) ?>">
            <?= h($chip) ?>
            <button type="button" class="chip-remove" onclick="removeChip(this)">✕</button>
          </span>
          <?php endforeach ?>
        </div>
        <div style="margin-top:14px">
          <p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px">Ícone:</p>
          <div style="display:flex;flex-wrap:wrap;gap:5px;padding:10px;background:var(--surface2);border-radius:10px;margin-bottom:10px">
            <?php
            $emojis = ['👶','🧒','👧','🍼','🤱','🧠','🫀','🫁','🦷','🩺','💉','💊','🩹','🩻','🔬','🏥','🚑','❤️','💚','🌱','🌿','🍎','🥗','🥛','😴','🌙','⭐','🌟','☀️','🤗','🤝','👨‍👩‍👧','👪','🏃','🧘','💪','🎯','📋','📅','🔔','✅','⚕️','🩼','🧬','🫶','🌸','🌺','🎀','💫','🌈'];
            foreach ($emojis as $e): ?>
            <button type="button" class="emoji-btn" onclick="insertEmoji('<?= $e ?>')" title="<?= $e ?>"><?= $e ?></button>
            <?php endforeach ?>
          </div>
        </div>
        <div class="chip-add-row">
          <input class="chip-add-input" type="text" id="chip-new-input" placeholder="Ex: 🏥 Consulta Domiciliar" maxlength="60"/>
          <button type="button" class="btn btn-ghost btn-sm" onclick="addChip()">+ Adicionar</button>
        </div>
      </div>

    </div><!-- /settings-grid -->

    <!-- APARÊNCIA VISUAL (full-width) -->
    <div class="settings-card" style="margin-top:20px">
      <h2 class="settings-card-title">🎨 Aparência Visual</h2>

      <div class="visual-grid">

        <!-- Cores -->
        <div class="visual-section">
          <p class="visual-section-title">🖌️ Cores</p>

          <div class="color-row">
            <div class="color-picker-group">
              <div class="color-preview-dot" id="dot-rose" style="background:<?= h($s['color_rose']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_rose">Cor Principal</label>
                <p class="form-hint">Botões, rótulos, chips</p>
              </div>
              <input type="color" class="color-input" id="color_rose" name="color_rose" value="<?= h($s['color_rose']) ?>"/>
            </div>

            <div class="color-picker-group">
              <div class="color-preview-dot" id="dot-lavender" style="background:<?= h($s['color_lavender']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_lavender">Cor Secundária</label>
                <p class="form-hint">Chips, blobs, destaques</p>
              </div>
              <input type="color" class="color-input" id="color_lavender" name="color_lavender" value="<?= h($s['color_lavender']) ?>"/>
            </div>

            <div class="color-picker-group">
              <div class="color-preview-dot" id="dot-mint" style="background:<?= h($s['color_mint']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_mint">Cor Terciária</label>
                <p class="form-hint">Acentos, links</p>
              </div>
              <input type="color" class="color-input" id="color_mint" name="color_mint" value="<?= h($s['color_mint']) ?>"/>
            </div>

            <div class="color-picker-group">
              <div class="color-preview-dot" id="dot-text" style="background:<?= h($s['color_text']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_text">Texto</label>
                <p class="form-hint">Cor do texto principal</p>
              </div>
              <input type="color" class="color-input" id="color_text" name="color_text" value="<?= h($s['color_text']) ?>"/>
            </div>
          </div>

          <p class="visual-section-title" style="margin-top:20px">🌅 Fundo (gradiente)</p>
          <div class="gradient-preview" id="gradient-preview"
               style="background: linear-gradient(135deg, <?= h($s['color_bg_start']) ?>, <?= h($s['color_bg_mid']) ?>, <?= h($s['color_bg_end']) ?>)">
          </div>
          <div class="color-row" style="margin-top:10px">
            <div class="color-picker-group">
              <div class="color-preview-dot" style="background:<?= h($s['color_bg_start']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_bg_start">Início</label>
              </div>
              <input type="color" class="color-input" id="color_bg_start" name="color_bg_start" value="<?= h($s['color_bg_start']) ?>"/>
            </div>
            <div class="color-picker-group">
              <div class="color-preview-dot" style="background:<?= h($s['color_bg_mid']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_bg_mid">Meio</label>
              </div>
              <input type="color" class="color-input" id="color_bg_mid" name="color_bg_mid" value="<?= h($s['color_bg_mid']) ?>"/>
            </div>
            <div class="color-picker-group">
              <div class="color-preview-dot" style="background:<?= h($s['color_bg_end']) ?>"></div>
              <div class="color-picker-info">
                <label class="form-label" for="color_bg_end">Fim</label>
              </div>
              <input type="color" class="color-input" id="color_bg_end" name="color_bg_end" value="<?= h($s['color_bg_end']) ?>"/>
            </div>
          </div>

          <!-- Paletas predefinidas -->
          <p class="visual-section-title" style="margin-top:20px">✨ Paletas rápidas</p>
          <div class="palette-row" id="palette-row">
            <?php
            $palettes = [
              ['name'=>'Rosa Original',  'rose'=>'#e8628a','lav'=>'#b39ddb','mint'=>'#69c5b0','bg1'=>'#f5e8f0','bg2'=>'#ede8f8','bg3'=>'#e0f4ef','text'=>'#2d2235'],
              ['name'=>'Azul Bebê',      'rose'=>'#5b9bd5','lav'=>'#90caf9','mint'=>'#80cbc4','bg1'=>'#e3f0fb','bg2'=>'#e8f4f8','bg3'=>'#e0f7fa','text'=>'#1a2d45'],
              ['name'=>'Verde Saúde',    'rose'=>'#4caf82','lav'=>'#81c784','mint'=>'#4dd0e1','bg1'=>'#e8f5e9','bg2'=>'#f1f8e9','bg3'=>'#e0f7fa','text'=>'#1b3a2d'],
              ['name'=>'Lilás Suave',    'rose'=>'#9c6bcc','lav'=>'#ce93d8','mint'=>'#80cbc4','bg1'=>'#f3e5f5','bg2'=>'#ede7f6','bg3'=>'#e8eaf6','text'=>'#2d1b45'],
              ['name'=>'Coral Quente',   'rose'=>'#f4845f','lav'=>'#ffab91','mint'=>'#80deea','bg1'=>'#fce4dc','bg2'=>'#fff3e0','bg3'=>'#e0f7fa','text'=>'#3d1c10'],
              ['name'=>'Dourado Elegante','rose'=>'#c9a227','lav'=>'#f4d03f','mint'=>'#52be80','bg1'=>'#fdf9e8','bg2'=>'#fef9e7','bg3'=>'#eafaf1','text'=>'#2d2200'],
            ];
            foreach ($palettes as $pal): ?>
            <button type="button" class="palette-btn" title="<?= h($pal['name']) ?>"
                    data-rose="<?= $pal['rose'] ?>" data-lav="<?= $pal['lav'] ?>" data-mint="<?= $pal['mint'] ?>"
                    data-bg1="<?= $pal['bg1'] ?>" data-bg2="<?= $pal['bg2'] ?>" data-bg3="<?= $pal['bg3'] ?>" data-text="<?= $pal['text'] ?>"
                    onclick="applyPalette(this)">
              <span style="background:<?= $pal['rose'] ?>"></span>
              <span style="background:<?= $pal['lav'] ?>"></span>
              <span style="background:<?= $pal['mint'] ?>"></span>
              <span class="palette-name"><?= h($pal['name']) ?></span>
            </button>
            <?php endforeach ?>
          </div>
        </div>

        <!-- Tipografia -->
        <div class="visual-section">
          <p class="visual-section-title">🔤 Tipografia</p>

          <div class="form-group">
            <label class="form-label" for="font_family">Fonte principal</label>
            <select class="form-input" id="font_family" name="font_family" style="cursor:pointer">
              <?php foreach ($google_fonts as $f): ?>
              <option value="<?= h($f) ?>" <?= $s['font_family'] === $f ? 'selected' : '' ?>><?= h($f) ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">
              Tamanho base: <strong id="font-size-val"><?= h($s['font_size_base']) ?></strong>px
            </label>
            <input type="range" class="font-range" id="font_size_base" name="font_size_base"
                   min="12" max="20" step="1" value="<?= h($s['font_size_base']) ?>"/>
            <div style="display:flex;justify-content:space-between;font-size:.68rem;color:var(--text-muted);margin-top:4px">
              <span>Pequeno (12px)</span><span>Grande (20px)</span>
            </div>
          </div>

          <!-- Preview de fonte -->
          <div class="font-preview-box" id="font-preview-box">
            <p class="font-preview-title" id="font-preview-title">Dra. Barbara Fernandes</p>
            <p class="font-preview-body" id="font-preview-body">Pediatra dedicada ao cuidado integral da criança.</p>
          </div>
        </div>

      </div><!-- /visual-grid -->
    </div>

    <div style="margin-top:24px;display:flex;gap:12px;align-items:center">
      <button type="submit" class="btn btn-primary" id="btn-save-settings">💾 Salvar Configurações</button>
      <a href="<?= BASE_PATH ?>/" target="_blank" class="btn btn-ghost">🌐 Ver Cartão ao vivo</a>
      <span id="autosave-hint" style="font-size:.75rem;color:var(--text-muted)"></span>
    </div>
  </form>
  </div><!-- /settings-panels -->

  <!-- ======= RIGHT: PREVIEW ======= -->
  <div class="preview-pane" id="preview-pane">
    <div class="preview-header">
      <span style="font-size:.8rem;font-weight:700;color:var(--text)">📱 Preview ao vivo</span>
      <span class="preview-status-badge" id="preview-status">ao vivo</span>
    </div>

    <div class="preview-phone-wrap">
      <div class="preview-phone">
        <div class="preview-notch"></div>
        <div class="preview-screen">
          <div id="preview-loading" style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:10px;color:#666">
            <div style="width:36px;height:36px;border:3px solid #eee;border-top-color:#e8628a;border-radius:50%;animation:spin .8s linear infinite"></div>
            <span style="font-size:.72rem">Carregando...</span>
          </div>
          <iframe id="card-preview-frame"
                  title="Preview do cartão virtual"
                  style="display:none"
                  scrolling="yes"></iframe>
        </div>
        <div class="preview-home-btn"></div>
      </div>
    </div>

    <div style="display:flex;gap:8px;justify-content:center;margin-top:14px">
      <button class="btn btn-ghost btn-sm" onclick="fetchPreview(true)">🔄 Atualizar</button>
      <a href="<?= BASE_PATH ?>/" target="_blank" class="btn btn-ghost btn-sm">↗ Abrir</a>
    </div>
    <p style="text-align:center;font-size:.68rem;color:var(--text-muted);margin-top:8px">
      As cores atualizam instantaneamente.<br/>Textos atualizam em 0,7s.
    </p>
  </div>

</div><!-- /settings-split -->

<?php
// Define BASE dynamically using the BASE_PATH constant from config.php
echo "<script>const BASE = '" . BASE_PATH . "';</script>";

admin_page_end(<<<'JSCRIPT'
<script>
const previewFrame = document.getElementById('card-preview-frame');

/* ===== PREVIEW FUNCTIONS ===== */
let previewTimer = null;

function setStatus(msg, type = 'ok') {
  const el = document.getElementById('preview-status');
  if (!el) return;
  el.textContent = msg;
  el.style.background = type === 'ok' ? 'rgba(105,197,176,.2)' : 'rgba(232,98,138,.2)';
  el.style.color = type === 'ok' ? '#69c5b0' : '#e8628a';
}

async function fetchPreview(force = false) {
  setStatus('atualizando…', 'loading');
  const form = document.getElementById('settings-form');
  const fd   = new FormData(form);
  try {
    const res  = await fetch(BASE + '/api/preview.php', { method: 'POST', body: fd });
    const html = await res.text();
    previewFrame.srcdoc = html;
    setStatus('ao vivo');
  } catch(e) {
    setStatus('erro', 'err');
  }
}

// Try to apply colors directly to iframe DOM (faster than full refetch)
function applyColorsToFrame() {
  try {
    let doc = null;
    try {
      doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
    } catch(err) {
      fetchPreview();
      return;
    }
    if (!doc || !doc.body || !doc.documentElement) { fetchPreview(); return; }
    const root = doc.documentElement;
    const rose = document.getElementById('color_rose')?.value;
    const lav  = document.getElementById('color_lavender')?.value;
    const mint = document.getElementById('color_mint')?.value;
    const txt  = document.getElementById('color_text')?.value;
    const bg1  = document.getElementById('color_bg_start')?.value;
    const bg2  = document.getElementById('color_bg_mid')?.value;
    const bg3  = document.getElementById('color_bg_end')?.value;

    if (rose) { root.style.setProperty('--rose', rose); root.style.setProperty('--rose-light', rose + '99'); root.style.setProperty('--rose-pale', rose + '22'); }
    if (lav)  { root.style.setProperty('--lavender', lav); root.style.setProperty('--lavender-light', lav + '44'); }
    if (mint) { root.style.setProperty('--mint', mint); root.style.setProperty('--mint-light', mint + '55'); }
    if (txt)  root.style.setProperty('--text-dark', txt);
    if (bg1 && bg2 && bg3) doc.body.style.background = `linear-gradient(145deg,${bg1} 0%,${bg2} 40%,${bg3} 100%)`;

    setStatus('cores aplicadas');
  } catch(e) {
    fetchPreview();
  }
}

function applyFontToFrame() {
  try {
    let doc = null;
    try {
      doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
    } catch(err) {
      fetchPreview();
      return;
    }
    if (!doc || !doc.body) { fetchPreview(); return; }
    const font = document.getElementById('font_family')?.value;
    const size = document.getElementById('font_size_base')?.value;
    if (font) doc.body.style.fontFamily = `'${font}', system-ui, sans-serif`;
    if (size) doc.body.style.fontSize   = size + 'px';
    setStatus('fonte aplicada');
  } catch(e) { fetchPreview(); }
}

/* ===== FORM EVENTS ===== */
document.getElementById('settings-form').addEventListener('input', (e) => {
  const name = e.target.name || '';

  // Instant: colors
  if (name.startsWith('color_')) {
    const dot = document.getElementById('dot-' + name.replace('color_',''));
    if (dot) dot.style.background = e.target.value;
    // update gradient preview
    const bg1 = document.getElementById('color_bg_start')?.value;
    const bg2 = document.getElementById('color_bg_mid')?.value;
    const bg3 = document.getElementById('color_bg_end')?.value;
    const gp  = document.getElementById('gradient-preview');
    if (gp && bg1 && bg2 && bg3) gp.style.background = `linear-gradient(135deg,${bg1},${bg2},${bg3})`;
    applyColorsToFrame();
    return;
  }

  // Instant: font family
  if (name === 'font_family') {
    updateFontPreview();
    applyFontToFrame();
    return;
  }

  // Instant: font size slider
  if (name === 'font_size_base') {
    document.getElementById('font-size-val').textContent = e.target.value;
    updateFontPreview();
    applyFontToFrame();
    return;
  }

  // WhatsApp preview text
  if (name === 'whatsapp_number') {
    const el = document.getElementById('wa-preview');
    if (el) el.textContent = 'https://wa.me/' + e.target.value.replace(/\D/g,'');
  }

  // Debounced: text content → full refetch
  clearTimeout(previewTimer);
  previewTimer = setTimeout(() => fetchPreview(), 700);
});

/* ===== FONT PREVIEW ===== */
const fontCache = {};
function updateFontPreview() {
  const font = document.getElementById('font_family')?.value || 'Nunito';
  const size = document.getElementById('font_size_base')?.value || '15';
  const box  = document.getElementById('font-preview-box');
  const t    = document.getElementById('font-preview-title');
  const b    = document.getElementById('font-preview-body');
  if (!box) return;

  // Load font if not already loaded
  if (!fontCache[font]) {
    const lnk = document.createElement('link');
    lnk.rel  = 'stylesheet';
    lnk.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(font)}:wght@400;700&display=swap`;
    document.head.appendChild(lnk);
    fontCache[font] = true;
  }

  box.style.fontFamily = `'${font}', system-ui, sans-serif`;
  if (t) t.style.fontSize = (parseInt(size) * 1.4) + 'px';
  if (b) b.style.fontSize = size + 'px';
}

/* ===== PALETTE ===== */
function applyPalette(btn) {
  document.querySelectorAll('.palette-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const map = {
    'color_rose': btn.dataset.rose, 'color_lavender': btn.dataset.lav,
    'color_mint': btn.dataset.mint, 'color_bg_start': btn.dataset.bg1,
    'color_bg_mid': btn.dataset.bg2, 'color_bg_end': btn.dataset.bg3,
    'color_text': btn.dataset.text,
  };
  for (const [id, val] of Object.entries(map)) {
    const el = document.getElementById(id);
    if (el) { el.value = val; el.dispatchEvent(new Event('input', {bubbles:true})); }
  }
}

/* ===== CHIPS ===== */
function getChips() {
  return [...document.querySelectorAll('#chips-editor .chip-item')].map(el => el.dataset.value);
}
function updateChipsInput() {
  document.getElementById('chips-json-input').value = JSON.stringify(getChips());
}
function removeChip(btn) { btn.closest('.chip-item').remove(); updateChipsInput(); schedulePreview(); }
function addChip() {
  const input = document.getElementById('chip-new-input');
  const val   = input.value.trim();
  if (!val) return;
  const span = document.createElement('span');
  span.className = 'chip-item'; span.dataset.value = val;
  span.innerHTML = `${val} <button type="button" class="chip-remove" onclick="removeChip(this)">✕</button>`;
  document.getElementById('chips-editor').appendChild(span);
  updateChipsInput(); schedulePreview();
  input.value = ''; input.focus();
}
function schedulePreview() { clearTimeout(previewTimer); previewTimer = setTimeout(() => fetchPreview(), 700); }
function insertEmoji(emoji) {
  const input = document.getElementById('chip-new-input');
  const s = input.selectionStart, e2 = input.selectionEnd;
  input.value = input.value ? input.value.slice(0,s)+emoji+input.value.slice(e2) : emoji+' ';
  event.currentTarget.classList.add('emoji-active');
  setTimeout(()=>event.currentTarget.classList.remove('emoji-active'),400);
  input.focus();
}
document.getElementById('chip-new-input')?.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();addChip();}});
updateChipsInput();

/* ===== INIT ===== */
updateFontPreview();
// Load preview via srcdoc (avoids Apache 403 on direct iframe src)
window.addEventListener('DOMContentLoaded', () => fetchPreview(true));

// When srcdoc iframe finishes loading, hide spinner and show iframe
previewFrame.addEventListener('load', () => {
  const loader = document.getElementById('preview-loading');
  if (loader) loader.style.display = 'none';
  previewFrame.style.display = 'block';
  setStatus('ao vivo');
});
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }

/* Split layout */

.settings-split {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}
.settings-panels {
  flex: 1;
  min-width: 0;
}

/* Preview pane */
.preview-pane {
  width: 300px;
  flex-shrink: 0;
  position: sticky;
  top: calc(var(--header-h, 64px) + 16px);
}
.preview-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 12px;
}
.preview-status-badge {
  font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
  padding: 3px 10px; border-radius: 100px;
  background: rgba(105,197,176,.2); color: #69c5b0;
  transition: all .3s ease;
}

/* Phone mockup */
.preview-phone-wrap { display: flex; justify-content: center; }
.preview-phone {
  width: 256px;
  background: linear-gradient(145deg, #1a1a2e, #16213e);
  border-radius: 36px;
  padding: 14px 10px;
  box-shadow: 0 24px 60px rgba(0,0,0,.6), inset 0 0 0 1px rgba(255,255,255,.06);
  position: relative;
}
.preview-notch {
  width: 80px; height: 18px;
  background: #0a0a18; border-radius: 0 0 12px 12px;
  margin: 0 auto 10px; position: relative; z-index: 2;
}
.preview-notch::before {
  content: ''; position: absolute; top: 5px; left: 50%; transform: translateX(-50%);
  width: 8px; height: 8px; border-radius: 50%;
  background: #1a1a2e; box-shadow: 0 0 0 2px rgba(255,255,255,.08);
}
.preview-screen {
  width: 236px; height: 500px; border-radius: 20px;
  overflow: hidden; background: #fff; position: relative;
}
.preview-screen iframe {
  width: 390px; height: 830px; border: none;
  transform: scale(0.605); transform-origin: top left;
  pointer-events: none;
}
.preview-home-btn {
  width: 60px; height: 5px;
  background: rgba(255,255,255,.15); border-radius: 3px;
  margin: 12px auto 0;
}

/* Visual editor */
.visual-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}
@media (max-width: 900px) { .visual-grid { grid-template-columns: 1fr; } }

.visual-section-title {
  font-size: .72rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .12em;
  color: var(--rose); margin-bottom: 12px;
}
.color-row {
  display: flex; flex-wrap: wrap; gap: 10px;
}
.color-picker-group {
  display: flex; align-items: center; gap: 10px;
  background: var(--surface2); border-radius: 10px;
  padding: 10px 12px; flex: 1; min-width: 160px;
  cursor: pointer; transition: border .2s ease;
  border: 1.5px solid transparent;
}
.color-picker-group:has(input:focus) { border-color: var(--rose); }
.color-preview-dot {
  width: 28px; height: 28px; border-radius: 50%;
  flex-shrink: 0; border: 2px solid rgba(255,255,255,.2);
  box-shadow: 0 2px 8px rgba(0,0,0,.3);
  transition: background .2s ease;
}
.color-picker-info { flex: 1; }
.color-input {
  width: 36px; height: 36px; border: none; border-radius: 8px;
  cursor: pointer; background: none; padding: 0;
}
.color-input::-webkit-color-swatch-wrapper { padding: 0; border-radius: 6px; }
.color-input::-webkit-color-swatch { border: none; border-radius: 6px; }

.gradient-preview {
  height: 40px; border-radius: 10px;
  border: 1px solid rgba(255,255,255,.08);
  transition: background .3s ease;
}

/* Palettes */
.palette-row { display: flex; flex-wrap: wrap; gap: 8px; }
.palette-btn {
  display: flex; align-items: center; gap: 3px;
  padding: 6px 10px; border-radius: 20px;
  background: var(--surface2); border: 1.5px solid var(--border);
  cursor: pointer; transition: all .2s ease;
  font-size: .72rem; color: var(--text-mid);
}
.palette-btn span:not(.palette-name) {
  width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
}
.palette-btn .palette-name { margin-left: 4px; }
.palette-btn:hover { border-color: var(--rose); background: var(--rose-dim); color: var(--text); }
.palette-btn.active { border-color: var(--rose); background: var(--rose-dim); color: var(--rose); }

/* Font preview */
.font-range { width: 100%; accent-color: var(--rose); }
.font-preview-box {
  background: var(--surface2); border-radius: 10px;
  padding: 16px; margin-top: 12px;
  border: 1px solid var(--border);
  transition: font-family .3s ease;
}
.font-preview-title {
  font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 4px;
  transition: all .3s ease;
}
.font-preview-body {
  font-size: .85rem; color: var(--text-muted); line-height: 1.6;
  transition: all .3s ease;
}

/* Emoji buttons */
.emoji-btn {
  width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,.08);
  border-radius: 8px; background: var(--surface); font-size: 1rem;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all .15s ease; line-height: 1;
}
.emoji-btn:hover { background: rgba(232,98,138,.2); border-color: var(--rose); transform: scale(1.15); }
.emoji-btn.emoji-active { background: rgba(232,98,138,.35); transform: scale(1.2); }

/* Responsive */
@media (max-width: 1100px) {
  .settings-split { flex-direction: column-reverse; }
  .preview-pane { width: 100%; position: static; }
  .preview-phone-wrap { justify-content: center; }
}
</style>
JSCRIPT);
?>
