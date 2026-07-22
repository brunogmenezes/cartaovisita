<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_layout.php';

require_login();

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erro no upload. Código: ' . $file['error'];
    } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
        $error = 'Arquivo muito grande. Máximo: 5 MB.';
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $ext_map      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

        if (!in_array($mime, $allowed_mime, true)) {
            $error = 'Tipo de arquivo inválido. Use JPG, PNG, WEBP ou GIF.';
        } else {
            $ext      = $ext_map[$mime];
            $filename = 'profile_' . time() . '.' . $ext;
            $dest     = UPLOAD_DIR . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Delete old uploaded photo (but not the default profile.png)
                $old = get_setting('profile_photo', '');
                if ($old && $old !== 'profile.png' && file_exists(UPLOAD_DIR . $old)) {
                    @unlink(UPLOAD_DIR . $old);
                }
                set_setting('profile_photo', $filename);
                $success = 'Foto atualizada com sucesso!';
            } else {
                $error = 'Não foi possível salvar o arquivo. Verifique as permissões da pasta uploads/.';
            }
        }
    }
}

$current_photo = get_setting('profile_photo', 'profile.png');
$photo_url = $current_photo === 'profile.png'
    ? BASE_PATH . '/profile.png'
    : BASE_PATH . '/uploads/' . $current_photo;

admin_page_start('Foto de Perfil', 'photo');
?>

<div class="page-header">
  <h1 class="page-title">🖼️ Foto de Perfil</h1>
  <p class="page-sub">Atualize a foto exibida no cartão virtual</p>
</div>

<?php if ($success): ?>
<div class="alert alert-success">✅ <?= h($success) ?></div>
<?php endif ?>
<?php if ($error): ?>
<div class="alert alert-error">❌ <?= h($error) ?></div>
<?php endif ?>

<div class="settings-grid">
  <div class="settings-card">
    <h2 class="settings-card-title">📸 Foto Atual</h2>
    <img src="<?= h($photo_url) ?>" alt="Foto atual" id="current-photo"
         style="width:120px;height:120px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 20px;border:3px solid var(--rose);box-shadow:0 6px 20px var(--rose-glow)"/>
    <p style="text-align:center;font-size:.78rem;color:var(--text-muted)"><?= h($current_photo) ?></p>
  </div>

  <div class="settings-card" style="grid-column: span 1">
    <h2 class="settings-card-title">⬆️ Enviar Nova Foto</h2>

    <form method="POST" enctype="multipart/form-data" id="photo-form">
      <div class="upload-zone" id="upload-zone" onclick="document.getElementById('photo-input').click()">
        <img id="preview-img" src="<?= h($photo_url) ?>" alt="Preview"
             class="upload-preview" />
        <p style="font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:4px">
          Clique para selecionar a foto
        </p>
        <p style="font-size:.78rem;color:var(--text-muted)">
          JPG, PNG, WEBP ou GIF &middot; Máximo 5 MB
        </p>
      </div>

      <input type="file" id="photo-input" name="photo"
             accept="image/jpeg,image/png,image/webp,image/gif"
             style="display:none" onchange="previewPhoto(this)" />

      <button type="submit" class="btn btn-primary" id="btn-upload-photo"
              style="margin-top:18px;width:100%">
        💾 Salvar Foto
      </button>
    </form>

    <div style="margin-top:16px;padding:14px;background:var(--surface2);border-radius:10px">
      <p style="font-size:.78rem;color:var(--text-muted);line-height:1.6">
        💡 <strong style="color:var(--text)">Dica:</strong> Use uma foto quadrada para melhor resultado. 
        Recomendado: 400×400px ou maior. A foto será exibida em formato circular no cartão.
      </p>
    </div>
  </div>
</div>

<?php
admin_page_end(<<<'JS'
<script>
function previewPhoto(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    document.getElementById('preview-img').src = e.target.result;
  };
  reader.readAsDataURL(file);
}

// Drag-and-drop
const zone = document.getElementById('upload-zone');
const inp  = document.getElementById('photo-input');

zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', (e) => {
  e.preventDefault();
  zone.classList.remove('drag-over');
  if (e.dataTransfer.files[0]) {
    inp.files = e.dataTransfer.files;
    previewPhoto(inp);
  }
});
</script>
JS);
?>
