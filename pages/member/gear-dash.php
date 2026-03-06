<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
$auth = new auth(['member']);
$get_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$join = "JOIN plan_clients pc ON plan.id = pc.plan_id";
$columns = [
    'plan.id AS plan_id',
    'plan.plan_name',
    'plan.category',
    'plan.days',
    'plan.duration',
    'plan.trainer_id',
    'pc.client_id',
    'pc.assigned_at'
];

$records = $controller->fetch_records(
    'plan',
    $columns,
    $join,
    ['client_id' => $get_id]
);

if (!empty($records)) {
    $record = $records[0];
    $weekly_workouts = $record['duration'];
    $weekly_goal = $record['duration']; 
    $status_text = ($weekly_workouts >= ($weekly_goal / 2)) 
        ? '↑ On track this week' 
        : '↓ Behind schedule';
} else {
    $weekly_workouts = 0;
    $weekly_goal = 0;
    $status_text = 'No plan assigned';
}
// echo '<pre>';
// print_r($records);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore — My Dashboard</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet" />
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body>

    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <?php require_once 'member-sidebar.php'; ?>

    <!-- ══ MAIN WRAP ══ -->
    <div class="main-wrap">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            </div>
            <div class="topbar-right">

                <div class="topbar-profile">
                    <div
                        style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,var(--accent),#c23500);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                        <?php
                        $name = $auth->show_name();
                        $names = explode(' ', $name);
                        echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        ?>
                    </div>
                    <span class="tp-name"><?php echo $name; ?></span>
                </div>

                <button class="logout-btn" onclick="doLogout()">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Logout</span>
                </button>
            </div>
        </header>

        <div class="page-area">

            <!-- Page Header -->
            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title" id="greetTitle">
                        GOOD MORNING, <?php echo htmlspecialchars($name); ?>.
                    </div>
                    <div class="subtitle" id="todayDate">Loading...</div>
                </div>
            </div>

            <!-- STAT CARDS — gym-goer metrics -->
            <div class="stats-row">
                <div class="stat-card orange anim-fade-up anim-d1">
                    <div class="stat-top">
                        <div class="stat-lbl">Weekly Workouts</div>
                        <div class="stat-icon">🏋️</div>
                    </div>
                    <div class="stat-val"> <?= $weekly_workouts ?><span
                            style="font-family:'Rajdhani',sans-serif;font-size:22px;color:var(--text-sec);letter-spacing:1px;">/6</span>
                    </div>
                    <div class="stat-pill pill-up"><?= $status_text ?></div>
                </div>

                <div class="stat-card green anim-fade-up anim-d2">
                    <div class="stat-top">
                        <div class="stat-lbl">Calories Burned</div>
                        <div class="stat-icon">🔥</div>
                    </div>
                    <div class="stat-val">2,840</div>
                    <div class="stat-pill pill-up">↑ 340 above goal</div>
                </div>

                <div class="stat-card blue anim-fade-up anim-d3">
                    <div class="stat-top">
                        <div class="stat-lbl">Current Streak</div>
                        <div class="stat-icon">⚡</div>
                    </div>
                    <div class="stat-val">14<span
                            style="font-family:'Rajdhani',sans-serif;font-size:22px;color:var(--text-sec);letter-spacing:1px;">d</span>
                    </div>
                    <div class="stat-pill pill-neutral">→ Keep it going!</div>
                </div>

                <div class="stat-card yellow anim-fade-up anim-d4">
                    <div class="stat-top">
                        <div class="stat-lbl">Total Volume</div>
                        <div class="stat-icon">💪</div>
                    </div>
                    <div class="stat-val">18.4<span
                            style="font-family:'Rajdhani',sans-serif;font-size:22px;color:var(--text-sec);letter-spacing:1px;">T</span>
                    </div>
                    <div class="stat-pill pill-up">↑ PR this month</div>
                </div>
            </div>
        </div><!-- /page-area -->
    </div><!-- /main-wrap -->

    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>

    <script>
        let _t;
        function showToast(icon, title, sub = '') { const t = document.getElementById('toast'); clearTimeout(_t); t.classList.remove('hide'); document.getElementById('toastIcon').textContent = icon; document.getElementById('toastTitle').textContent = title; document.getElementById('toastSub').textContent = sub; t.style.display = 'flex'; _t = setTimeout(() => { t.classList.add('hide'); setTimeout(() => { t.style.display = 'none'; t.classList.remove('hide'); }, 300); }, 3500); }

        (function init() {
            const h = new Date().getHours();
            const greet = h < 12 ? 'GOOD MORNING' : h < 17 ? 'GOOD AFTERNOON' : 'GOOD EVENING';
            const userName = "<?php echo htmlspecialchars($name); ?>";
            document.getElementById('greetTitle').textContent = greet + ', ' + userName + '.';

            const dateEl = document.getElementById('todayDate');
            if (dateEl) {
                const now = new Date();
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                dateEl.textContent = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} · Push Day 🔥`;
            }
            setTimeout(() =>
                showToast('💪', `Let's get it, ${userName}!`, 'Push Day is loaded and ready'),
                700);
        })();

        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('overlay').classList.toggle('open'); }
        function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('open'); }
        function setActive(el) { document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active')); el.classList.add('active'); }
        function activateTab(el) { const g = el.closest('.tab-group'); if (!g) return; g.querySelectorAll('.tab').forEach(t => t.classList.remove('active')); el.classList.add('active'); }
        function doLogout() { showToast('👋', 'See you next session!', 'Rest well, come back stronger'); setTimeout(() => { window.location.href = 'login'; }, 1000); }
    </script>
</body>

</html>