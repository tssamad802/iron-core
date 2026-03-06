<aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <div class="logo-icon"><img src="./images/logo.png"></div>
        <div>
            <div class="logo-text">IronCore</div>
            <div class="logo-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group-label">Main</div>

        <a href="./dashboard">
            <div class="nav-item">
                <span class="nav-icon">📊</span>
                Dashboard
            </div>
        </a>
        <a href="./admin-users">
            <div class="nav-item">
                <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                Users
                <span class="nav-badge"><?php echo $total_users; ?></span>
            </div>
        </a>
        <a href="./admin-members-management">
            <div class="nav-item">
                <span class="nav-icon"><i class="fa-solid fa-user-gear"></i></span>
                Member Management
            </div>
        </a>
        <a href="./admin-plan">
            <div class="nav-item clickable">
            <span class="nav-icon"><i class="fa-solid fa-list-check"></i></span>
            Plans
        </div>
        </a>

        <div class="nav-group-label">Finance</div>
        <a href="./admin-entry">
            <div class="nav-item clickable">
                <span class="nav-icon"><i class="fa-solid fa-credit-card"></i></span>
                Payments
                <span class="nav-badge yellow">5</span>
            </div>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <?php
            $name = $auth->show_name();
            $names = explode(' ', $name);
            $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
            ?>
            <div class="avatar avatar-sm" style="width:36px;height:36px;font-size:13px;border-radius:8px;">
                <?php echo $initials; ?>
            </div>
            <div class="user-meta">
                <div class="name"><?php echo $auth->show_name(); ?></div>
                <div class="role">Super Admin</div>
            </div>
            <span class="user-arrow"></span>
        </div>
    </div>

</aside>

<script>
    const currentPath = window.location.pathname;
    const linkMap = {
        '/dashboard': 0,
        '/admin-users': 1,
        '/admin-members-management': 2,
    };
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => item.classList.remove('active'));
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
            link.querySelector('.nav-item').classList.add('active');
        }
    });
    const clickableDivs = document.querySelectorAll('.sidebar-nav .nav-item.clickable');
    clickableDivs.forEach(div => {
        div.addEventListener('click', function () {
            document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>