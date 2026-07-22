<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_layout.php';

require_login();

$db = get_db();

// ---- Helper queries ----
function count_today(PDO $db, string $table): int {
    $r = $db->query("SELECT COUNT(*) FROM {$table} WHERE created_at >= NOW() - INTERVAL '1 day'")->fetchColumn();
    return (int)$r;
}

function count_period(PDO $db, string $table, string $interval): int {
    $r = $db->query("SELECT COUNT(*) FROM {$table} WHERE created_at >= NOW() - INTERVAL '{$interval}'")->fetchColumn();
    return (int)$r;
}

// ---- Stats ----
$views_today   = count_today($db, 'page_views');
$views_week    = count_period($db, 'page_views', '7 days');
$views_month   = count_period($db, 'page_views', '30 days');
$leads_total   = (int)$db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$leads_week    = count_period($db, 'leads', '7 days');
$clicks_total  = (int)$db->query("SELECT COUNT(*) FROM click_events")->fetchColumn();

// ---- Chart data: pageviews last 30 days ----
$chart_pv = $db->query("
    SELECT TO_CHAR(DATE_TRUNC('day', created_at), 'DD/MM') AS day, COUNT(*) AS total
    FROM page_views
    WHERE created_at >= NOW() - INTERVAL '30 days'
    GROUP BY DATE_TRUNC('day', created_at)
    ORDER BY DATE_TRUNC('day', created_at)
")->fetchAll();

$pv_labels = json_encode(array_column($chart_pv, 'day'), JSON_UNESCAPED_UNICODE);
$pv_data   = json_encode(array_column($chart_pv, 'total'));

// ---- Chart data: clicks by type ----
$chart_clicks = $db->query("
    SELECT event_type, COUNT(*) AS total
    FROM click_events
    GROUP BY event_type
    ORDER BY total DESC
")->fetchAll();

// Carregar títulos reais dos links configurados para mapear no gráfico e tabela
$feat_links_json = get_setting('featured_links', '[]');
$feat_links_list = json_decode($feat_links_json, true) ?: [];

$cl_labels_mapped = array_map(function($r) use ($feat_links_list) {
    $evt = $r['event_type'];
    if (strpos($evt, 'link_') === 0) {
        $idx = (int)substr($evt, 5);
        if (isset($feat_links_list[$idx])) {
            $emoji = $feat_links_list[$idx]['emoji'] ?? '🔗';
            $title = $feat_links_list[$idx]['title'] ?? ('Link ' . ($idx + 1));
            return $emoji . ' ' . $title;
        }
        return '🔗 Link ' . ($idx + 1);
    }
    return match($evt) {
        'whatsapp'   => '💬 WhatsApp',
        'phone'      => '📞 Telefone',
        'instagram'  => '📸 Instagram',
        'sleep_guide'=> '🌙 Guia Sono',
        default      => $evt,
    };
}, $chart_clicks);

$cl_labels = json_encode($cl_labels_mapped, JSON_UNESCAPED_UNICODE);
$cl_data   = json_encode(array_column($chart_clicks, 'total'));

// ---- Recent leads ----
$recent_leads = $db->query("
    SELECT name, phone, ip_address, TO_CHAR(created_at, 'DD/MM/YYYY HH24:MI') AS created_fmt
    FROM leads
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll();

// ---- Recent clicks ----
$recent_clicks = $db->query("
    SELECT event_type, ip_address, TO_CHAR(created_at, 'DD/MM HH24:MI') AS created_fmt
    FROM click_events
    ORDER BY created_at DESC
    LIMIT 8
")->fetchAll();

admin_page_start('Dashboard', 'dashboard', 'Tempo real');
?>

<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <p class="page-sub">Insights de acessos, cliques e leads do cartão virtual</p>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="stats-grid">
  <div class="stat-card rose">
    <div class="stat-icon">👁️</div>
    <p class="stat-label">Acessos hoje</p>
    <p class="stat-value"><?= $views_today ?></p>
    <p class="stat-delta"><?= $views_week ?> nos últimos 7 dias</p>
  </div>

  <div class="stat-card lavender">
    <div class="stat-icon">📅</div>
    <p class="stat-label">Acessos no mês</p>
    <p class="stat-value"><?= $views_month ?></p>
    <p class="stat-delta">últimos 30 dias</p>
  </div>

  <div class="stat-card mint">
    <div class="stat-icon">👥</div>
    <p class="stat-label">Leads captados</p>
    <p class="stat-value"><?= $leads_total ?></p>
    <p class="stat-delta"><?= $leads_week ?> essa semana</p>
  </div>

  <div class="stat-card amber">
    <div class="stat-icon">🖱️</div>
    <p class="stat-label">Cliques totais</p>
    <p class="stat-value"><?= $clicks_total ?></p>
    <p class="stat-delta">em todos os botões</p>
  </div>
</div>

<!-- ===== CHARTS ===== -->
<div class="charts-grid">
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <p class="chart-title">Acessos por dia</p>
        <p class="chart-sub">Últimos 30 dias</p>
      </div>
    </div>
    <div class="chart-wrap">
      <canvas id="chartViews"></canvas>
    </div>
  </div>

  <div class="chart-card">
    <div class="chart-header">
      <div>
        <p class="chart-title">Cliques por botão</p>
        <p class="chart-sub">Total acumulado</p>
      </div>
    </div>
    <div class="chart-wrap">
      <canvas id="chartClicks"></canvas>
    </div>
  </div>
</div>

<!-- ===== RECENT LEADS ===== -->
<div class="table-card" style="margin-bottom:20px">
  <div class="table-header">
    <p class="table-title">👥 Leads Recentes</p>
    <a href="<?= BASE_PATH ?>/admin/leads.php" class="btn btn-ghost btn-sm">Ver todos →</a>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Telefone</th>
        <th>IP</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($recent_leads)): ?>
      <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:28px">Nenhum lead captado ainda.</td></tr>
      <?php else: ?>
      <?php foreach ($recent_leads as $lead): ?>
      <tr>
        <td><strong><?= h($lead['name']) ?></strong></td>
        <td><?= h($lead['phone']) ?></td>
        <td style="color:var(--text-muted);font-size:.78rem"><?= h($lead['ip_address']) ?></td>
        <td style="color:var(--text-muted)"><?= h($lead['created_fmt']) ?></td>
      </tr>
      <?php endforeach ?>
      <?php endif ?>
    </tbody>
  </table>
</div>

<!-- ===== RECENT CLICKS ===== -->
<div class="table-card">
  <div class="table-header">
    <p class="table-title">🖱️ Cliques Recentes</p>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>Botão</th><th>IP</th><th>Quando</th></tr>
    </thead>
    <tbody>
      <?php if (empty($recent_clicks)): ?>
      <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:28px">Sem dados ainda.</td></tr>
      <?php else: ?>
      <?php
      $badge_map = [
          'whatsapp'    => 'badge-mint',
          'phone'       => 'badge-lavender',
          'instagram'   => 'badge-rose',
          'sleep_guide' => 'badge-amber',
      ];
      foreach ($recent_clicks as $click):
          $evt = $click['event_type'];
          $badgeCls = $badge_map[$evt] ?? 'badge-amber';
          
          // Resolve nome dinâmico para os links em destaque
          if (strpos($evt, 'link_') === 0) {
              $idx = (int)substr($evt, 5);
              $label = isset($feat_links_list[$idx]) 
                  ? (($feat_links_list[$idx]['emoji'] ?? '🔗') . ' ' . ($feat_links_list[$idx]['title'] ?? 'Link'))
                  : '🔗 Link ' . ($idx + 1);
          } else {
              $label_map = [
                  'whatsapp'    => '💬 WhatsApp',
                  'phone'       => '📞 Telefone',
                  'instagram'   => '📸 Instagram',
                  'sleep_guide' => '🌙 Guia Sono',
              ];
              $label = $label_map[$evt] ?? $evt;
          }
      ?>
      <tr>
        <td><span class="badge <?= $badgeCls ?>"><?= $label ?></span></td>
        <td style="color:var(--text-muted);font-size:.78rem"><?= h($click['ip_address']) ?></td>
        <td style="color:var(--text-muted)"><?= h($click['created_fmt']) ?></td>
      </tr>
      <?php endforeach ?>
      <?php endif ?>
    </tbody>
  </table>
</div>

<?php
admin_page_end(<<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const gridColor = 'rgba(255,255,255,0.05)';
const textColor = '#a89cc0';

// --- Line chart: pageviews ---
new Chart(document.getElementById('chartViews'), {
  type: 'line',
  data: {
    labels: {$pv_labels},
    datasets: [{
      label: 'Acessos',
      data:  {$pv_data},
      borderColor:     '#e8628a',
      backgroundColor: 'rgba(232,98,138,0.12)',
      borderWidth: 2,
      pointBackgroundColor: '#e8628a',
      pointRadius: 3,
      tension: 0.4,
      fill: true,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 } } },
      y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 }, stepSize: 1 }, beginAtZero: true }
    }
  }
});

// --- Bar chart: clicks ---
new Chart(document.getElementById('chartClicks'), {
  type: 'doughnut',
  data: {
    labels: {$cl_labels},
    datasets: [{
      data:            {$cl_data},
      backgroundColor: ['rgba(105,197,176,0.85)','rgba(179,157,219,0.85)','rgba(232,98,138,0.85)','rgba(245,158,11,0.85)'],
      borderColor:     ['#69c5b0','#b39ddb','#e8628a','#f59e0b'],
      borderWidth: 2,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, padding: 12, boxWidth: 12 } }
    }
  }
});
</script>
JS);
?>
