<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">IC</div>
        <div>
            <div class="logo-text">IronCore</div>
            <div class="logo-sub">Member Portal</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group-label">My Fitness</div>
        <a href="./gear-dashboard" class="nav-item active" onclick="setActive(this)">
            <span class="nav-icon">🏠</span>My Dashboard
        </a>
        <a href="./myworkout" class="nav-item" onclick="setActive(this)">
            <span class="nav-icon">🏋️</span>My Workouts
            <span class="nav-badge">3</span>
        </a>
        <a href="./diet-plan" class="nav-item" onclick="setActive(this)">
            <span class="nav-icon">🏋️</span>Diet Plan
        </a>
    </nav>
    <?php
    $name = $auth->show_name();
    $names = explode(' ', $name);
    $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
    ?>
    <div class="sidebar-footer">
        <!-- Theme Toggle Row -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 13px 10px;gap:8px;">
            <span
                style="font-family:'Rajdhani',sans-serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text-dim);">Appearance</span>
            <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle light/dark mode"
                aria-label="Toggle theme">
                <i class="fa-solid fa-sun  icon-sun"></i>
                <i class="fa-solid fa-moon icon-moon"></i>
            </button>
        </div>
        <div class="sidebar-user">
            <div
                style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,var(--accent),#c23500);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                <?php
                $name = $auth->show_name();
                $names = explode(' ', $name);
                echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                ?>
            </div>
            <div class="user-meta">
                <div class="name"><?php echo $name; ?></div>
                <div class="role">Elite Member · Year 2</div>
            </div>
            <span class="user-arrow"></span>
        </div>
    </div>

</aside>