<?php
/**
 * Admin shared layout helper
 * Sidebar, header, navigation rendering
 */

function admin_sidebar(string $active = ''): string {
    $base = BASE_PATH;
    $user = current_admin();
    $initial = strtoupper(substr($user, 0, 1));

    $nav = [
        ['href' => '/admin/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard',     'key' => 'dashboard'],
        ['href' => '/admin/settings.php',  'icon' => '⚙️',  'label' => 'Configurações', 'key' => 'settings'],
        ['href' => '/admin/photo.php',     'icon' => '🖼️',  'label' => 'Foto de Perfil','key' => 'photo'],
        ['href' => '/admin/leads.php',     'icon' => '👥',  'label' => 'Leads',         'key' => 'leads'],
    ];

    $items = '';
    foreach ($nav as $n) {
        $cls = $active === $n['key'] ? ' active' : '';
        $items .= "<a href='{$base}{$n['href']}' class='nav-item{$cls}'>"
                . "<span class='nav-icon'>{$n['icon']}</span>{$n['label']}</a>";
    }

    return "
    <aside class='sidebar' id='sidebar'>
      <div class='sidebar-brand'>
        <div class='brand-icon'>🩺</div>
        <div class='brand-text'>
          <div class='brand-name'>Dra. Barbara</div>
          <div class='brand-sub'>Painel Admin</div>
        </div>
      </div>
      <p class='nav-section'>Menu</p>
      {$items}
      <p class='nav-section'>Site</p>
      <a href='{$base}/' target='_blank' class='nav-item'>
        <span class='nav-icon'>🌐</span>Ver Cartão Virtual
      </a>
      <div class='sidebar-footer'>
        <div class='sidebar-user'>
          <div class='user-avatar'>{$initial}</div>
          <div class='user-info'>
            <div class='user-name'>{$user}</div>
            <div class='user-role'>Administrador</div>
          </div>
          <a href='{$base}/admin/logout.php' class='logout-btn' title='Sair'>⎋</a>
        </div>
      </div>
    </aside>";
}

function admin_header(string $title, string $badge = ''): string {
    $b = $badge ? "<span class='header-badge'>{$badge}</span>" : '';
    return "
    <header class='admin-header'>
      <button class='btn btn-ghost btn-sm' onclick=\"document.getElementById('sidebar').classList.toggle('open')\" style='display:none' id='menu-toggle'>☰</button>
      <span class='header-title'>{$title}</span>
      {$b}
    </header>";
}

function admin_page_start(string $title, string $active, string $badge = ''): void {
    require_login();
    echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
  <meta charset='UTF-8'/>
  <meta name='viewport' content='width=device-width,initial-scale=1.0'/>
  <title>{$title} – Admin | Dra. Barbara Fernandes</title>
  <link rel='stylesheet' href='" . BASE_PATH . "/admin/style.css'/>
</head>
<body>
" . admin_sidebar($active) . "
<div class='admin-main'>
" . admin_header($title, $badge) . "
<div class='admin-content'>";
}

function admin_page_end(string $extra_js = ''): void {
    echo "</div></div>{$extra_js}</body></html>";
}
