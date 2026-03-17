<?php
// trainer-sidebar.php — GymFlow Trainer Sidebar
// Highlights the active nav item based on current page.
$current = basename($_SERVER['PHP_SELF'], '.php');
$navItems = [
    ['href' => './trainer-dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Dashboard', 'slug' => 'trainer-dashboard'],
    ['href' => './trainer-clients', 'icon' => 'fas fa-users', 'label' => 'My Clients', 'slug' => 'trainer-clients'],
    ['href' => './member-plans', 'icon' => 'fas fa-id-card', 'label' => 'Members Plans', 'slug' => 'member-plan'],
    ['href' => './workout-plan', 'icon' => 'fas fa-dumbbell', 'label' => 'Workout Plans', 'slug' => 'workout-plan']
];
?>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="fas fa-fire" style="color:#fff;font-size:18px;"></i>
        </div>
        <div>
            <div class="logo-text">GYM<span>FLOW</span></div>
            <div class="logo-sub">Trainer Portal</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav" role="navigation" aria-label="Trainer Navigation">

        <div class="nav-group-label">Main Menu</div>

        <?php foreach ($navItems as $item):
            $isActive = ($item['slug'] !== '' && strpos($current, $item['slug']) !== false);
            ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="nav-item<?= $isActive ? ' active' : '' ?>"
                aria-current="<?= $isActive ? 'page' : 'false' ?>">
                <span class="nav-icon"><i class="<?= $item['icon'] ?>"></i></span>
                <span><?= $item['label'] ?></span>
                <?php if (!empty($item['badge'])): ?>
                    <span class="nav-badge"><?= $item['badge'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>


    </nav>

    <!-- User + Logout -->
    <div class="sidebar-footer">
        <div class="sidebar-user" onclick="window.location='#profile'">
            <div class="sidebar-avatar avatar">
                <?php
                if (isset($auth)) {
                    $name = $auth->show_name();
                    $parts = explode(' ', trim($name));
                    echo strtoupper(
                        substr($parts[0], 0, 1) .
                        (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
                    );
                } else {
                    echo 'TR';
                }
                ?>
            </div>
            <div class="user-meta">
                <div class="name"><?= isset($auth) ? htmlspecialchars($auth->show_name()) : 'Trainer' ?></div>
                <div class="role">Trainer</div>
            </div>
            <i class="fas fa-chevron-right user-chevron"></i>
        </div>

        <button class="sidebar-logout-btn" onclick="doLogout()" type="button" aria-label="Logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>

        <!-- Theme Toggle Row -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 4px 0;gap:8px;">
            <span
                style="font-family:'Rajdhani',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text-dim);">Appearance</span>
            <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle light/dark mode"
                aria-label="Toggle theme">
                <i class="fas fa-sun  icon-sun"></i>
                <i class="fas fa-moon icon-moon"></i>
            </button>
        </div>
    </div>

</aside>