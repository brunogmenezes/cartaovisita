<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_layout.php';

require_login();

$db = get_db();

// Pagination
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$total = (int)$db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$pages = (int)ceil($total / $per_page);

$leads = $db->prepare("
    SELECT id, name, phone, ip_address,
           TO_CHAR(created_at AT TIME ZONE 'America/Fortaleza', 'DD/MM/YYYY HH24:MI') AS created_fmt
    FROM leads
    ORDER BY created_at DESC
    LIMIT :lim OFFSET :off
");
$leads->bindValue(':lim', $per_page, PDO::PARAM_INT);
$leads->bindValue(':off', $offset,   PDO::PARAM_INT);
$leads->execute();
$rows = $leads->fetchAll();

admin_page_start('Leads Captados', 'leads', $total . ' total');
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
  <div>
    <h1 class="page-title">👥 Leads Captados</h1>
    <p class="page-sub">Pessoas que informaram nome e telefone antes de abrir o WhatsApp</p>
  </div>
  <a href="<?= BASE_PATH ?>/admin/leads.php?export=csv" class="btn btn-ghost">
    ⬇️ Exportar CSV
  </a>
</div>

<!-- CSV Export -->
<?php if (isset($_GET['export']) && $_GET['export'] === 'csv'): ?>
<?php
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="leads_' . date('Y-m-d') . '.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['ID', 'Nome', 'Telefone', 'IP', 'Data/Hora']);
  $all = $db->query("
      SELECT id, name, phone, ip_address,
             TO_CHAR(created_at AT TIME ZONE 'America/Fortaleza', 'DD/MM/YYYY HH24:MI') AS created_fmt
      FROM leads ORDER BY created_at DESC")->fetchAll();
  foreach ($all as $r) {
      fputcsv($out, [$r['id'], $r['name'], $r['phone'], $r['ip_address'], $r['created_fmt']]);
  }
  fclose($out);
  exit;
?>
<?php endif ?>

<div class="table-card">
  <div class="table-header">
    <p class="table-title">
      Mostrando <?= count($rows) ?> de <?= $total ?> leads
    </p>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Nome</th>
        <th>Telefone</th>
        <th>IP</th>
        <th>Data / Hora</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
      <tr><td colspan="6" style="text-align:center;padding:36px;color:var(--text-muted)">Nenhum lead captado ainda.</td></tr>
      <?php else: ?>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td style="color:var(--text-muted)"><?= $r['id'] ?></td>
        <td><strong><?= h($r['name']) ?></strong></td>
        <td><?= h($r['phone']) ?></td>
        <td style="color:var(--text-muted);font-size:.78rem"><?= h($r['ip_address']) ?></td>
        <td style="color:var(--text-muted)"><?= h($r['created_fmt']) ?></td>
        <td>
          <a href="https://wa.me/55<?= preg_replace('/\D/','',$r['phone']) ?>?text=<?= urlencode('Olá ' . $r['name'] . '!') ?>"
             target="_blank" rel="noopener" class="btn btn-ghost btn-sm">
            💬 WhatsApp
          </a>
        </td>
      </tr>
      <?php endforeach ?>
      <?php endif ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;gap:8px;align-items:center">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a href="?page=<?= $p ?>"
       class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-ghost' ?>">
      <?= $p ?>
    </a>
    <?php endfor ?>
  </div>
  <?php endif ?>
</div>

<?php admin_page_end(); ?>
