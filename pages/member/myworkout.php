<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
$auth = new auth(['member']);
$member_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$join = "
INNER JOIN plan_clients AS pc ON plan.id = pc.plan_id
INNER JOIN users AS u ON u.id = pc.client_id
LEFT JOIN exercise AS e ON e.plan_id = plan.id";

$columns = [
    'u.*',
    'plan.*',
    'pc.plan_id AS planID',
    'pc.client_id AS clientID',
    'e.id AS exerciseID',
    'e.exercise_name',
    'e.sets',
    'e.reps',
    'e.rest',
    'e.trainer_id',
    'e.created_at',
];

$plans = $controller->fetch_records(
    'plan',
    $columns,
    $join,
    ['pc.client_id' => $member_id]
);
$groupedPlans = [];
foreach ($plans as $row) {
    $planID = $row['planID'];

    if (!isset($groupedPlans[$planID])) {
        $groupedPlans[$planID] = [
            'id' => $planID,
            'plan_name' => $row['plan_name'],
            'category' => $row['category'],
            'duration' => $row['duration'],
            'days' => $row['days'],
            'client' => [
                'id' => $row['clientID'],
                'fullname' => $row['fullname'],
            ],
            'exercise' => []
        ];
    }

    if (!empty($row['exerciseID'])) {
        $groupedPlans[$planID]['exercise'][] = [
            'id' => $row['exerciseID'],
            'name' => $row['exercise_name'],
            'sets' => $row['sets'],
            'reps' => $row['reps'],
            'rest' => $row['rest'],
            'trainer_id' => $row['trainer_id'],
            'created_at' => $row['created_at']
        ];
    }
}
$plans = array_values($groupedPlans);
// echo "<pre>";
// print_r($groupedPlans);
// echo "</pre>";
// exit;


/* ─── Pagination config ─────────────────────────────── */
$perPage = 6;
$total = count($plans);
$totalPg = max(1, (int) ceil($total / $perPage));
$page = max(1, min($totalPg, (int) ($_GET['pg'] ?? 1)));
$offset = ($page - 1) * $perPage;
$visible = array_slice($plans, $offset, $perPage);

/* ─── Helpers ──────────────────────────────────────── */
$catMap = [
    'strength' => ['label' => 'Strength', 'class' => 'cat-strength', 'icon' => 'fa-dumbbell'],
    'cardio' => ['label' => 'Cardio', 'class' => 'cat-cardio', 'icon' => 'fa-heart-pulse'],
    'hybrid' => ['label' => 'Hybrid', 'class' => 'cat-hybrid', 'icon' => 'fa-bolt'],
    'rehab' => ['label' => 'Rehab', 'class' => 'cat-rehab', 'icon' => 'fa-shield-heart'],
];
$iconMap = [
    'strength' => 'icon-orange',
    'cardio' => 'icon-blue',
    'hybrid' => 'icon-yellow',
    'rehab' => 'icon-green',
];
$planIconMap = [
    'strength' => 'fa-dumbbell',
    'cardio' => 'fa-heart-pulse',
    'hybrid' => 'fa-bolt',
    'rehab' => 'fa-shield-heart',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore — My Workouts</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet" />
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <?php require_once 'member-sidebar.php'; ?>

    <!-- ══ MAIN AREA ══ -->
    <div class="main-area">

        <!-- Topbar -->
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
                <div class="attendance-btn-wrapper anim-fade-up anim-d5">
                    <?php if ($controller->showAttendanceButton($get_id)): ?>
                        <a href="./attendance-script?id=<?= $get_id ?>">
                            <button class="attendance-btn">
                                <i class="fa-solid fa-calendar-check"></i>
                                Attendance
                            </button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- ══ CONTENT ══ -->
        <div class="content-body">

            <!-- Page Header -->
            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title">My Workout Plans</div>
                    <div class="subtitle">
                        <i class="fa-solid fa-circle-check" style="color:var(--green);margin-right:5px;"></i>
                        <?= $total ?> plan<?= $total !== 1 ? 's' : '' ?> assigned to you
                    </div>
                </div>
            </div>

            <?php
            $catCounts = [];
            foreach ($plans as $p) {
                $cat = $p['category'];
                $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
            }
            ?>
            <div class="cat-strip anim-fade-up anim-d1">
                <?php foreach ($catCounts as $cat => $cnt): ?>
                    <div class="cat-count-chip <?= $cat ?>">
                        <span class="chip-dot"></span>
                        <?= htmlspecialchars($catMap[$cat]['label'] ?? ucfirst($cat)) ?>
                        <span style="opacity:.5;">·</span>
                        <?= $cnt ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Plans Grid -->
            <div class="plans-grid anim-fade-up anim-d2">

                <?php if (empty($visible)): ?>
                    <div class="workout-empty-state">
                        <i class="fa-solid fa-dumbbell"></i>
                        <h3>No Plans Assigned Yet</h3>
                        <p>Your trainer hasn't assigned any workout plans to you. Check back soon!</p>
                    </div>

                <?php else:
                    foreach ($visible as $idx => $plan):
                        $cat = $plan['category'];
                        $cInfo = $catMap[$cat] ?? ['label' => ucfirst($cat), 'class' => 'cat-strength', 'icon' => 'fa-dumbbell'];
                        $iClass = $iconMap[$cat] ?? 'icon-orange';
                        $pIcon = $planIconMap[$cat] ?? 'fa-dumbbell';
                        $exList = $exercises[$plan['id']] ?? [];
                        $exCount = count($exList);
                        $delayClass = 'anim-d' . (($idx % 6) + 1);
                        ?>

                        <div class="wp-plan-card anim-fade-up <?= $delayClass ?>" data-cat="<?= $cat ?>">

                            <!-- Top row: badges + status -->
                            <div class="card-top">
                                <div class="badge-row">
                                    <span class="cat-badge <?= $cInfo['class'] ?>">
                                        <i class="fa-solid <?= $pIcon ?>" style="margin-right:3px;font-size:8px;"></i>
                                        <?= $cInfo['label'] ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Plan identity -->
                            <div class="plan-info">
                                <div class="plan-icon <?= $iClass ?>">
                                    <i class="fa-solid <?= $pIcon ?>"></i>
                                </div>
                                <div class="plan-text">
                                    <div class="plan-name"><?= htmlspecialchars($plan['plan_name']) ?></div>
                                    <div
                                        style="font-size:11px;color:var(--text-dim);margin-top:2px;font-family:'Rajdhani',sans-serif;letter-spacing:1px;">
                                        Plan #<?= $plan['id'] ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Meta info row -->
                            <div class="meta-row">
                                <div class="meta-item">
                                    <i class="fa-regular fa-clock"></i>
                                    <?= $plan['duration'] ?> wks
                                </div>
                                <div class="meta-item">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <?= $plan['days'] ?> days/wk
                                </div>
                            </div>

                            <!-- CTA button -->
                            <button class="btn-view-plan" onclick="openPlanModal(<?= $plan['id'] ?>)"
                                aria-label="View exercises for <?= htmlspecialchars($plan['plan_name']) ?>">
                                <i class="fa-solid fa-eye"></i>
                                View Exercises
                            </button>

                        </div>

                    <?php endforeach; endif; ?>
            </div><!-- /plans-grid -->

            <!-- ══ PAGINATION ══ -->
            <?php if ($totalPg > 1): ?>
                <div class="workouts-pag anim-fade-up anim-d6">
                    <div class="pag-info">
                        Showing <span><?= $offset + 1 ?>–<?= min($offset + $perPage, $total) ?></span> of
                        <span><?= $total ?></span> plans
                    </div>
                    <div class="pag-controls">
                        <?php if ($page > 1): ?>
                            <a href="?pg=<?= $page - 1 ?>" class="pag-btn nav-btn" title="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="pag-btn nav-btn" style="opacity:.3;cursor:not-allowed;">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        <?php endif; ?>

                        <?php for ($pg = 1; $pg <= $totalPg; $pg++): ?>
                            <a href="?pg=<?= $pg ?>" class="pag-btn <?= $pg === $page ? 'active' : '' ?>">
                                <?= $pg ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPg): ?>
                            <a href="?pg=<?= $page + 1 ?>" class="pag-btn nav-btn" title="Next">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="pag-btn nav-btn" style="opacity:.3;cursor:not-allowed;">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- /content-body -->
    </div><!-- /main-area -->

    <!-- ══════════════════════════════════════════════
         EXERCISE MODAL  (populated via JS)
    ══════════════════════════════════════════════ -->
    <div class="modal-backdrop" id="exerciseModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal">

            <!-- Accent bar (from style.css .modal::before) -->
            <div class="modal-header">
                <div class="modal-icon" id="modalIcon">
                    <i class="fa-solid fa-dumbbell"></i>
                </div>
                <div class="modal-title-group">
                    <div class="modal-title" id="modalTitle">Plan Name</div>
                    <div class="modal-subtitle" id="modalSubtitle">Exercise details</div>
                </div>
                <button class="modal-close" onclick="closeModal1()" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-divider"></div>

            <!-- Scrollable body -->
            <div class="modal-body modal-body-scroll" id="modalBody">
                <!-- Filled by JS -->
            </div>

            <div class="modal-footer" style="padding-top:14px;">
                <button class="btn-modal-cancel" onclick="closeModal1()">
                    <i class="fa-solid fa-xmark" style="margin-right:6px;"></i>Close
                </button>
            </div>

        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>

    <script>

        const PLAN_DATA = <?= json_encode(
            array_reduce(
                $plans,
                function ($carry, $p) use ($catMap, $planIconMap) {
            $carry[$p['id']] = [
                'name' => $p['plan_name'],
                'category' => $p['category'],
                'catLabel' => $catMap[$p['category']]['label'] ?? ucfirst($p['category']),
                'icon' => $planIconMap[$p['category']] ?? 'fa-dumbbell',
                'exercises' => $p['exercise'],
            ];
            return $carry;
        },
                []
            ),
            JSON_HEX_TAG
        )
            ?>
    </script>

    <script>
        /* ══════════════════════════════════════════════
           SIDEBAR TOGGLE
        ══════════════════════════════════════════════ */
        function openSidebar() {
            document.querySelector('.sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        /* ══════════════════════════════════════════════
           MODAL
        ══════════════════════════════════════════════ */
        const modal = document.getElementById('exerciseModal');
        const mTitle = document.getElementById('modalTitle');
        const mSub = document.getElementById('modalSubtitle');
        const mIcon = document.getElementById('modalIcon');
        const mBody = document.getElementById('modalBody');

        function openPlanModal(planId) {
            const plan = PLAN_DATA[planId];
            if (!plan) return;

            /* Header */
            mTitle.textContent = plan.name;
            mSub.textContent = plan.catLabel + ' · ' + plan.exercises.length + ' exercise' + (plan.exercises.length !== 1 ? 's' : '');
            mIcon.innerHTML = `<i class="fa-solid ${plan.icon}"></i>`;

            /* Build table */
            if (plan.exercises.length === 0) {
                mBody.innerHTML = `
                    <div class="modal-empty">
                        <i class="fa-solid fa-list-check"></i>
                        <p>No exercises have been added to this plan yet.</p>
                    </div>`;
            } else {
                let rows = plan.exercises.map((ex, i) => `
                    <tr>
                        <td>
                            <span class="ex-num" style="width:26px;height:26px;font-size:10px;">${i + 1}</span>
                        </td>
                        <td>
                            <span class="ex-modal-name">${escapeHtml(ex.name)}</span>
                        </td>
                        <td>
                            <span class="ex-pill pill-sets">${ex.sets}</span>
                        </td>
                        <td>
                            <span class="ex-pill pill-reps">${escapeHtml(ex.reps)}</span>
                        </td>
                        <td class="ex-col-rest">
                            <span class="ex-pill pill-rest">${escapeHtml(ex.rest)}</span>
                        </td>
                    </tr>`).join('');

                mBody.innerHTML = `
                    <table class="ex-modal-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Exercise</th>
                                <th>Sets</th>
                                <th>Reps</th>
                                <th class="ex-col-rest">Rest</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>`;
            }

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            mBody.scrollTop = 0;
        }

        function closeModal1() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        /* Close on backdrop click */
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        /* Close on Escape */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        /* ══════════════════════════════════════════════
           HELPERS
        ══════════════════════════════════════════════ */
        function escapeHtml(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(String(str)));
            return d.innerHTML;
        }

        /* ══════════════════════════════════════════════
           TOAST
        ══════════════════════════════════════════════ */
        function showToast(icon, title, sub, ms = 3500) {
            const t = document.getElementById('toast');
            document.getElementById('toastIcon').textContent = icon;
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastSub').textContent = sub;
            t.style.display = 'flex';
            t.classList.remove('hide');
            t.classList.add('show');
            setTimeout(() => {
                t.classList.add('hide');
                setTimeout(() => {
                    t.classList.remove('show', 'hide');
                    t.style.display = 'none';
                }, 260);
            }, ms);
        }
    </script>
    <script src="./js/script.js"></script>
</body>

</html>