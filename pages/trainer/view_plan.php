<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
require_once './includes/view.php';
$view = new view();
$hasErrors = !empty($_SESSION['errors']);

$auth = new auth(['trainer']);
$user_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$plan_id = $_GET['plan_id'];
$join = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
INNER JOIN plan_clients ON users.id = plan_clients.client_id
INNER JOIN plan ON plan.id = plan_clients.plan_id
";
$join1 = "
INNER JOIN role ON users.role = role.id
INNER JOIN status ON users.status = status.id
";
$columns = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name',
    'plan.id AS plan_p_id',
    'plan_clients.plan_id AS plan_c'
];
$columns1 = [
    'users.*',
    'role.role AS role_name',
    'status.status AS status_name',
];
if ($plan_id) {
    $plans = $controller->fetch_records('plan', ['*'], '', ['id' => $plan_id, 'trainer_id' => $user_id]);
}
$plan = !empty($plans) ? $plans[0] : null;

if ($plan) {
    $exercises = $controller->fetch_records('exercise', ['*'], '', [
        'trainer_id' => $user_id,
        'plan_id' => $plan_id
    ]);
} else {
    $exercises = [];
}
$clients = $controller->fetch_records('users', $columns, $join, ['plan_clients.plan_id' => $plan_id]);
$plan_clients_dropdown = $controller->fetch_records('users', $columns1, $join1, ['users.trainer_id' => $user_id]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $plan ? htmlspecialchars($plan['plan_name']) : 'View Plan' ?> | GymFlow</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body class="dashboard-body">

    <?php require_once 'trainer-sidebar.php'; ?>

    <div class="dashboard-container">
        <div class="main-area">

            <!-- TOPBAR -->
            <header class="topbar">
                <button class="menu-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar-left">
                    <h1 class="font-heading" style="font-size: 20px;">TRAINER DASHBOARD</h1>
                </div>
                <div class="topbar-right">
                    <div class="topbar-icon-btn">
                    </div>
                    <div class="avatar avatar-sm"
                        style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,var(--accent),#c23500);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                        <?php
                        $name = $auth->show_name();
                        $names = explode(' ', $name);
                        echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        ?>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <div class="content-body anim-fade-up">

                <?php if (!$plan): ?>
                    <!-- ── No Plan Found ── -->
                    <div style="text-align:center;padding:80px 20px;">
                        <div style="font-size:52px;margin-bottom:16px;opacity:0.2;"><i class="fas fa-dumbbell"></i></div>
                        <h3 class="font-heading" style="font-size:22px;margin-bottom:8px;color:var(--text-prime);">No Plan
                            Found</h3>
                        <p style="color:var(--text-sec);margin-bottom:24px;">This plan doesn't exist or you don't have
                            access to it.</p>
                        <a href="./workout-plan" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Plans
                        </a>
                    </div>

                <?php else:
                    $days_raw = $plan['days'] ?? '';
                    $selected_days = array_map('trim', explode(',', $days_raw));
                    $all_days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    $day_count = count(array_filter($selected_days));
                    $total_sets = array_sum(array_column($exercises, 'sets'));
                    $ex_count = count($exercises);
                    $client_count = count($clients);
                    ?>

                    <!-- ── Hero Banner ── -->
                    <div class="plan-hero anim-fade-up">
                        <div class="plan-hero-bg"></div>
                        <div class="plan-hero-grid"></div>
                        <div class="plan-hero-content">
                            <div class="plan-hero-icon">
                                <?php
                                $iconMap = [
                                    'strength' => 'fas fa-dumbbell',
                                    'cardio' => 'fas fa-running',
                                    'hybrid' => 'fas fa-bolt',
                                    'rehab' => 'fas fa-heartbeat',
                                ];
                                $cat = strtolower($plan['category'] ?? 'strength');
                                echo '<i class="' . ($iconMap[$cat] ?? 'fas fa-dumbbell') . '"></i>';
                                ?>
                            </div>

                            <div class="plan-hero-meta">
                                <div class="plan-hero-badges">
                                    <span
                                        class="cat-badge cat-<?= $cat ?>"><?= htmlspecialchars($plan['category']) ?></span>
                                    <span class="status-badge status-active">Active</span>
                                </div>
                                <div class="plan-hero-name"><?= htmlspecialchars($plan['plan_name']) ?></div>
                                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                                    <span
                                        style="font-size:12px;color:var(--text-sec);display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-calendar-week" style="color:var(--accent);font-size:11px;"></i>
                                        <?= htmlspecialchars($plan['duration']) ?> Weeks
                                    </span>
                                    <span
                                        style="font-size:12px;color:var(--text-sec);display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-calendar-day" style="color:var(--accent);font-size:11px;"></i>
                                        <?= $day_count ?> Days / Week
                                    </span>
                                    <span
                                        style="font-size:12px;color:var(--text-sec);display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-list-ul" style="color:var(--accent);font-size:11px;"></i>
                                        <?= $ex_count ?> Exercises
                                    </span>
                                </div>
                            </div>

                            <div class="plan-hero-actions">
                                <a class="btn btn-primary" onclick="openAssignModal1();" style="gap:7px;">
                                    <i class="fas fa-users"></i> Assign Clients
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ── KPI Row ── -->
                    <div class="plan-kpi-row anim-fade-up anim-d1">
                        <div class="plan-kpi orange">
                            <div class="plan-kpi-icon"><i class="fas fa-calendar-week"></i></div>
                            <div class="plan-kpi-body">
                                <div class="plan-kpi-val"><?= htmlspecialchars($plan['duration']) ?></div>
                                <div class="plan-kpi-lbl">Total Weeks</div>
                            </div>
                        </div>
                        <div class="plan-kpi blue">
                            <div class="plan-kpi-icon"><i class="fas fa-calendar-day"></i></div>
                            <div class="plan-kpi-body">
                                <div class="plan-kpi-val"><?= $day_count ?></div>
                                <div class="plan-kpi-lbl">Days / Week</div>
                            </div>
                        </div>
                        <div class="plan-kpi green">
                            <div class="plan-kpi-icon"><i class="fas fa-list-ul"></i></div>
                            <div class="plan-kpi-body">
                                <div class="plan-kpi-val"><?= $ex_count ?></div>
                                <div class="plan-kpi-lbl">Exercises</div>
                            </div>
                        </div>
                        <div class="plan-kpi yellow">
                            <div class="plan-kpi-icon"><i class="fas fa-layer-group"></i></div>
                            <div class="plan-kpi-body">
                                <div class="plan-kpi-val"><?= $total_sets ?></div>
                                <div class="plan-kpi-lbl">Total Sets</div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Main Layout ── -->
                    <div class="plan-layout anim-fade-up anim-d2">

                        <!-- LEFT: Exercises + Clients Tables -->
                        <div>

                            <!-- ── Exercises Card ── -->
                            <div class="exercises-card">
                                <div class="exercises-card-header">
                                    <div class="card-title">
                                        <i class="fas fa-list-ul"></i> Exercise List
                                    </div>
                                    <span class="ex-count-badge" id="ex-badge"><?= $ex_count ?> exercises</span>
                                </div>

                                <?php if (empty($exercises)): ?>
                                    <div class="ex-empty">
                                        <i class="fas fa-dumbbell"></i>
                                        <p>No exercises added to this plan yet.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="exercises-table" id="exercises-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Exercise</th>
                                                    <th class="col-sets">Sets</th>
                                                    <th class="col-reps">Reps</th>
                                                    <th class="col-rest">Rest</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="exercises-tbody">
                                                <?php foreach ($exercises as $i => $ex): ?>
                                                    <tr class="ex-row" data-index="<?= $i ?>">
                                                        <td class="ex-num-cell">
                                                            <span class="ex-num"><?= $i + 1 ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="ex-name-cell">
                                                                <div class="ex-icon">
                                                                    <i class="fas fa-dumbbell"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="ex-name">
                                                                        <?= htmlspecialchars($ex['exercise_name']) ?>
                                                                    </div>
                                                                    <!-- Mobile: pills shown below name -->
                                                                    <div class="ex-mob-pills">
                                                                        <span class="stat-pill-sm pill-sets">
                                                                            <?= htmlspecialchars($ex['sets']) ?> sets
                                                                        </span>
                                                                        <span class="stat-pill-sm pill-reps">
                                                                            <?= htmlspecialchars($ex['reps']) ?> reps
                                                                        </span>
                                                                        <span class="stat-pill-sm pill-rest">
                                                                            <?= htmlspecialchars($ex['rest']) ?>s rest
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="col-sets">
                                                            <span class="stat-pill-sm pill-sets">
                                                                <?= htmlspecialchars($ex['sets']) ?> sets
                                                            </span>
                                                        </td>
                                                        <td class="col-reps">
                                                            <span class="stat-pill-sm pill-reps">
                                                                <?= htmlspecialchars($ex['reps']) ?> reps
                                                            </span>
                                                        </td>
                                                        <td class="col-rest">
                                                            <span class="stat-pill-sm pill-rest">
                                                                <?= htmlspecialchars($ex['rest']) ?>s
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="./delete-exercise?exercise_id=<?= $ex['id'] ?>&plan_id=<?= $plan_id ?>"
                                                                class="action-btn remove"
                                                                onclick="return confirm('Delete this exercise?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Exercise Pagination -->
                                    <div class="pag-bar" id="ex-pag-bar">
                                        <div class="pag-info" id="ex-pag-info">
                                            Showing <span id="ex-pag-from">1</span>–<span id="ex-pag-to">5</span> of <span
                                                id="ex-pag-total"><?= $ex_count ?></span>
                                        </div>
                                        <div class="pag-controls" id="ex-pag-controls"></div>
                                    </div>

                                <?php endif; ?>
                            </div>
                            <!-- /exercises-card -->

                            <!-- ── Clients Card  ── -->
                            <div class="clients-card anim-fade-up anim-d3">
                                <div class="clients-card-header">
                                    <div class="card-title">
                                        <i class="fas fa-users"></i> My Clients
                                    </div>
                                    <span class="ex-count-badge" id="cl-badge"><?= $client_count ?> clients</span>
                                </div>

                                <?php if (empty($clients)): ?>
                                    <div class="clients-empty-tbl">
                                        <i class="fas fa-user-slash"></i>
                                        <p>No clients assigned yet.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="clients-table" id="clients-table">
                                            <thead>
                                                <tr>
                                                    <th>Client</th>
                                                    <th class="col-email">Email</th>
                                                    <th class="col-status">Status</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="clients-tbody">
                                                <?php
                                                $avatarColors = [
                                                    'linear-gradient(135deg,#ff4500,#c23500)',
                                                    'linear-gradient(135deg,#3b82f6,#2563eb)',
                                                    'linear-gradient(135deg,#a855f7,#7c3aed)',
                                                    'linear-gradient(135deg,#22c55e,#16a34a)',
                                                    'linear-gradient(135deg,#f59e0b,#d97706)',
                                                ];
                                                foreach ($clients as $ci => $client):
                                                    $parts = explode(' ', trim($client['fullname']));
                                                    $initials = strtoupper(
                                                        substr($parts[0], 0, 1) .
                                                        (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
                                                    );
                                                    $avatarBg = $avatarColors[$ci % count($avatarColors)];
                                                    ?>
                                                    <tr class="cl-row" data-index="<?= $ci ?>">
                                                        <td>
                                                            <div class="client-cell">
                                                                <div class="client-tbl-avatar" style="background:<?= $avatarBg ?>;">
                                                                    <?= htmlspecialchars($initials) ?>
                                                                </div>
                                                                <div>
                                                                    <div class="client-tbl-name">
                                                                        <?= htmlspecialchars($client['fullname']) ?>
                                                                    </div>
                                                                    <div class="client-tbl-email" style="display:none;"
                                                                        id="cl-mob-email-<?= $ci ?>">
                                                                        <?= htmlspecialchars($client['email'] ?? '') ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="col-email" style="color:var(--text-dim);font-size:12px;">
                                                            <?= htmlspecialchars($client['email'] ?? '—') ?>
                                                        </td>
                                                        <td class="col-status">
                                                            <span class="client-online-dot">
                                                                <?= htmlspecialchars($client['status_name'] ?? '—') ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="./remove-plan-client?client_id=<?= $client['id'] ?>&plan_id=<?= $plan_id ?>"
                                                                class="action-btn remove"
                                                                onclick="return confirm('Remove this client from the plan?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Clients Pagination -->
                                    <div class="pag-bar" id="cl-pag-bar">
                                        <div class="pag-info" id="cl-pag-info">
                                            Showing <span id="cl-pag-from">1</span>–<span id="cl-pag-to">5</span> of <span
                                                id="cl-pag-total"><?= $client_count ?></span>
                                        </div>
                                        <div class="pag-controls" id="cl-pag-controls"></div>
                                    </div>

                                <?php endif; ?>
                            </div>
                            <!-- /clients-card -->

                        </div><!-- /left col -->

                        <!-- RIGHT: Info Sidebar -->
                        <div class="plan-right-col">

                            <!-- Schedule Card -->
                            <div class="side-card">
                                <div class="side-card-header">
                                    <div class="card-title">
                                        <i class="fas fa-calendar-alt"></i> Weekly Schedule
                                    </div>
                                </div>
                                <div class="side-card-body">
                                    <div class="days-grid">
                                        <?php foreach ($all_days as $day):
                                            $isActive = in_array($day, $selected_days);
                                            ?>
                                            <div class="day-chip <?= $isActive ? 'active' : '' ?>">
                                                <?= $day ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div
                                        style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);font-size:12px;color:var(--text-sec);display:flex;align-items:center;gap:6px;">
                                        <i class="fas fa-info-circle" style="color:var(--accent);font-size:11px;"></i>
                                        <?= $day_count ?> training <?= $day_count === 1 ? 'day' : 'days' ?> per week
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Details Card -->
                            <div class="side-card">
                                <div class="side-card-header">
                                    <div class="card-title">
                                        <i class="fas fa-info-circle"></i> Plan Details
                                    </div>
                                </div>
                                <div class="side-card-body">
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-layer-group"></i> Category</span>
                                        <span class="info-val">
                                            <span
                                                class="cat-badge cat-<?= $cat ?>"><?= htmlspecialchars($plan['category']) ?></span>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-calendar-week"></i> Duration</span>
                                        <span class="info-val"><?= htmlspecialchars($plan['duration']) ?> Weeks</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-calendar-day"></i> Frequency</span>
                                        <span class="info-val"><?= $day_count ?>x / Week</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-list-ul"></i> Exercises</span>
                                        <span class="info-val"><?= $ex_count ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-layer-group"></i> Total Sets</span>
                                        <span class="info-val"><?= $total_sets ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-calendar-check"></i> Total Sessions</span>
                                        <span class="info-val"><?= $plan['duration'] * $day_count ?></span>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /plan-right-col -->

                    </div><!-- /plan-layout -->

                <?php endif; ?>

            </div><!-- /content-body -->
        </div><!-- /main-area -->
    </div>

    <!-- Toast -->
    <div class="toast" id="toast">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>
    <!-- Assign Clients Modal (Centered Multi-Select) -->
    <form action="./assign-plan-clients" method="post">
        <div id="assignClientsModal" class="modal-overlay">
            <div class="modal-content anim-fade-up">
                <div class="modal-header">
                    <h2>Assign Clients to Plan</h2>
                    <button class="modal-close" onclick="closeAssignModal()"><i class="fas fa-times"></i></button>
                </div>

                <div class="modal-body">
                    <!-- Multi-Select Dropdown -->
                    <div class="multi-select">
                        <div class="select-box" onclick="toggleDropdown()">
                            <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
                            <span id="selectedClients">Select clients...</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="options-container" id="clientsDropdown">
                            <?php
                            foreach ($plan_clients_dropdown as $row) { ?>
                                <label class="option">
                                    <input type="checkbox" name="clients[]" value="<?= $row['id'] ?>">
                                    <?php echo $row['fullname']; ?>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-ghost" onclick="closeAssignModal()">Cancel</a>
                    <button class="btn btn-primary">Assign Selected</button>
                </div>
                <?php $view->showErrors(); ?>
            </div>
        </div>
    </form>


    <?php if ($hasErrors): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                openAssignModal1();
            });
        </script>
    <?php endif; ?>
    <script>
        /* ── SIDEBAR ── */
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        });

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        function showToast(title, sub = '', icon = '⚡') {
            const t = document.getElementById('toast');
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastSub').textContent = sub;
            document.getElementById('toastIcon').textContent = icon;
            t.classList.add('show');
            setTimeout(() => {
                t.classList.add('hide');
                setTimeout(() => { t.classList.remove('show', 'hide'); }, 260);
            }, 3200);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.plan-kpi-val').forEach(el => {
                const target = parseInt(el.textContent, 10);
                if (isNaN(target) || target === 0) return;
                let current = 0;
                const step = Math.max(1, Math.ceil(target / 25));
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current;
                    if (current >= target) clearInterval(timer);
                }, 35);
            });
        });

        function openEditModal() {
            showToast('Edit Plan', 'Redirecting to edit view…', '✏️');
        }
        function initPaginator({ rowSelector, perPage = 5, controlsId, fromId, toId, totalId, barId }) {
            const rows = Array.from(document.querySelectorAll(rowSelector));
            const total = rows.length;
            const controls = document.getElementById(controlsId);
            const bar = document.getElementById(barId);

            if (!controls || total === 0) return;

            if (total <= perPage) {
                if (bar) bar.style.display = 'none';
                return;
            }

            const totalPages = Math.ceil(total / perPage);
            let currentPage = 1;

            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * perPage;
                const end = start + perPage;

                rows.forEach((row, i) => {
                    row.classList.toggle('pag-hidden', i < start || i >= end);
                });

                // Update info text
                const actualEnd = Math.min(end, total);
                if (document.getElementById(fromId)) document.getElementById(fromId).textContent = start + 1;
                if (document.getElementById(toId)) document.getElementById(toId).textContent = actualEnd;
                if (document.getElementById(totalId)) document.getElementById(totalId).textContent = total;

                renderControls();
            }

            function renderControls() {
                controls.innerHTML = '';

                const prev = makeBtn('<i class="fas fa-chevron-left"></i>', 'nav-btn', () => showPage(currentPage - 1));
                prev.disabled = currentPage === 1;
                controls.appendChild(prev);

                const pages = getPageRange(currentPage, totalPages);
                pages.forEach(p => {
                    if (p === '…') {
                        const ellipsis = document.createElement('span');
                        ellipsis.textContent = '…';
                        ellipsis.style.cssText = 'color:var(--text-dim);font-size:12px;line-height:30px;padding:0 2px;';
                        controls.appendChild(ellipsis);
                    } else {
                        const btn = makeBtn(p, '', () => showPage(p));
                        if (p === currentPage) btn.classList.add('active');
                        controls.appendChild(btn);
                    }
                });

                // Next button
                const next = makeBtn('<i class="fas fa-chevron-right"></i>', 'nav-btn', () => showPage(currentPage + 1));
                next.disabled = currentPage === totalPages;
                controls.appendChild(next);
            }

            function makeBtn(label, extraClass, onClick) {
                const btn = document.createElement('button');
                btn.className = 'pag-btn' + (extraClass ? ' ' + extraClass : '');
                btn.innerHTML = label;
                btn.addEventListener('click', onClick);
                return btn;
            }

            function getPageRange(current, last) {
                if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
                if (current <= 4) return [1, 2, 3, 4, 5, '…', last];
                if (current >= last - 3) return [1, '…', last - 4, last - 3, last - 2, last - 1, last];
                return [1, '…', current - 1, current, current + 1, '…', last];
            }

            showPage(1);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initPaginator({
                rowSelector: '.ex-row',
                perPage: 5,
                controlsId: 'ex-pag-controls',
                fromId: 'ex-pag-from',
                toId: 'ex-pag-to',
                totalId: 'ex-pag-total',
                barId: 'ex-pag-bar',
            });

            initPaginator({
                rowSelector: '.cl-row',
                perPage: 5,
                controlsId: 'cl-pag-controls',
                fromId: 'cl-pag-from',
                toId: 'cl-pag-to',
                totalId: 'cl-pag-total',
                barId: 'cl-pag-bar',
            });

            function toggleMobEmails() {
                const emailColHidden = window.getComputedStyle(
                    document.querySelector('.clients-table .col-email') || document.createElement('td')
                ).display === 'none';

                document.querySelectorAll('[id^="cl-mob-email-"]').forEach(el => {
                    el.style.display = emailColHidden ? 'block' : 'none';
                });
            }

            toggleMobEmails();
            window.addEventListener('resize', toggleMobEmails);
        });

        // Modal functions
        function openAssignModal1() { document.getElementById('assignClientsModal').classList.add('show'); }
        function closeAssignModal() { document.getElementById('assignClientsModal').classList.remove('show'); }

        // Dropdown toggle
        function toggleDropdown() {
            const dropdown = document.getElementById('clientsDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Update selected clients text
        document.querySelectorAll('#clientsDropdown input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', () => {
                const selected = Array.from(document.querySelectorAll('#clientsDropdown input[type="checkbox"]:checked'))
                    .map(c => c.parentElement.textContent.trim());
                document.getElementById('selectedClients').textContent = selected.length ? selected.join(', ') : 'Select clients...';
            });
        });

        function assignSelectedClients() {
            const selectedC = document.getElementById('selectedC');
            const selected = Array.from(
                document.querySelectorAll('#clientsDropdown input[type="checkbox"]:checked')
            ).map(c => c.value);
            selectedC.value = selected.join(',');
        }

        document.addEventListener('click', e => {
            const modal = document.getElementById('assignClientsModal');
            const dropdown = document.getElementById('clientsDropdown');
            const box = modal.querySelector('.select-box');
            if (!box.contains(e.target)) dropdown.style.display = 'none';
        });
    </script>
    <script src="./js/script.js"></script>
    <script src="./js/theme.js"></script>
</body>

</html>