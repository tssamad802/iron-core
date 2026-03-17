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
$total_clients = $controller->count('users', ['users.trainer_id' => $user_id]);
$data_plan = $controller->fetch_records('plan', ['*'], '', ['trainer_id' => $user_id]);
if (!empty($data_plan)) {
    $plan_id = $data_plan[0]['id'];
    $data_exercise_condition = [
        'trainer_id' => $user_id,
        'plan_id' => $plan_id
    ];
    $data_exercise = $controller->fetch_records('exercise', ['*'], '', $data_exercise_condition);
} else {
    $plan_id = null;
    $data_exercise = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Plans | GymFlow</title>
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

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="page-title">WORKOUT PLANS</h2>
                        <p class="page-sub">Build, manage and assign training programs</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn" id="btnImport"><i class="fas fa-file-import"></i> Import</button>
                        <button class="btn btn-primary" id="btnCreatePlan"><i class="fas fa-plus"></i> Create
                            Plan</button>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="filter-bar">
                    <div class="tab-group" id="tabGroup">
                        <button class="wp-tab active" data-tab="all">All Plans</button>
                        <button class="wp-tab" data-tab="strength">Strength</button>
                        <button class="wp-tab" data-tab="cardio">Cardio</button>
                        <button class="wp-tab" data-tab="hybrid">Hybrid</button>
                        <button class="wp-tab" data-tab="rehab">Rehab</button>
                    </div>
                    <div class="filter-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search plans..." id="searchInput">
                        </div>
                        <select class="filter-select">
                            <option>All Levels</option>
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                        </select>
                    </div>
                </div>

                <!-- Plans Grid -->
                <div class="plans-grid" id="plansGrid">
                </div><!-- /plans-grid -->

            </div><!-- /content-body -->
        </div><!-- /main-area -->
    </div>

    <!-- ═══ CREATE PLAN MODAL ═══ -->
    <form action="./plan-script" method="post">
        <input type="hidden" name="trainer_id" value="<?php echo $user_id; ?>">
        <div class="modal-backdrop" id="planModal" onclick="handleBackdrop(event)">
            <div class="modal">

                <div class="modal-header">
                    <div class="modal-icon"><i class="fas fa-dumbbell"></i></div>
                    <div class="modal-title-group">
                        <div class="modal-title" id="modalTitle">CREATE PLAN</div>
                        <div class="modal-subtitle" id="modalSubtitle">Fill in the details for your new workout program
                        </div>
                    </div>
                    <a class="modal-close" onclick="closePlanModal()"><i class="fas fa-times"></i></a>
                </div>
                <div class="modal-divider"></div>

                <div class="modal-body">
                    <div class="wp-modal-grid">

                        <div class="form-group">
                            <label class="form-label">Plan Name</label>
                            <div class="input-wrap">
                                <i class="input-icon fas fa-tag"></i>
                                <input type="text" class="form-input" placeholder="e.g. Power Builder 12W" id="planName"
                                    name="plan_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <div class="input-wrap">
                                <i class="input-icon fas fa-layer-group"></i>
                                <select class="form-input form-select" style="padding-left:32px;" name="category">
                                    <option value="">Select category...</option>
                                    <option>Strength</option>
                                    <option>Cardio</option>
                                    <option>Hybrid</option>
                                    <option>Rehab</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Duration (Weeks)</label>
                            <div class="input-wrap">
                                <i class="input-icon fas fa-calendar-week"></i>
                                <input type="number" class="form-input" placeholder="e.g. 12" min="1" max="52"
                                    name="duration">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Days Per Week</label>
                            <div class="wp-days-picker" id="daysPicker">
                                <input type="hidden" name="days[]" id="selectedDays">
                                <button class="wp-day-btn" type="button" data-day="Mon">Mon</button>
                                <button class="wp-day-btn" type="button" data-day="Tue">Tue</button>
                                <button class="wp-day-btn" type="button" data-day="Wed">Wed</button>
                                <button class="wp-day-btn" type="button" data-day="Thu">Thu</button>
                                <button class="wp-day-btn" type="button" data-day="Fri">Fri</button>
                                <button class="wp-day-btn" type="button" data-day="Sat">Sat</button>
                                <button class="wp-day-btn" type="button" data-day="Sun">Sun</button>
                            </div>
                        </div>

                    </div>
                    <div class="wp-exercises-section">
                        <div class="wp-exercises-header">
                            <span class="card-title"><i class="fas fa-list-ul"></i> Exercises</span>
                            <button class="btn-add-ex" id="btnAddExercise" type="button">
                                <i class="fas fa-plus"></i> Add Exercise
                            </button>
                        </div>
                        <div class="wp-exercise-list" id="exerciseList">
                            <div class="wp-exercise-row">
                                <div class="wp-ex-num">1</div>
                                <div class="wp-ex-fields">
                                    <input class="form-input wp-ex-input" type="text"
                                        placeholder="Exercise name (e.g. Barbell Squat)" name="exercise_name[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Sets"
                                        name="sets[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Reps"
                                        name="reps[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Rest(s)"
                                        name="rest[]">
                                </div>
                                <button class="wp-ex-remove" onclick="removeExercise(this)" type="button"><i
                                        class="fas fa-times"></i></button>
                            </div>
                            <div class="wp-exercise-row">
                                <div class="wp-ex-num">2</div>
                                <div class="wp-ex-fields">
                                    <input class="form-input wp-ex-input" type="text"
                                        placeholder="Exercise name (e.g. Romanian Deadlift)" name="exercise_name[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Sets"
                                        name="sets[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Reps"
                                        name="reps[]">
                                    <input class="form-input wp-ex-input-sm" type="text" placeholder="Rest(s)"
                                        name="rest[]">
                                </div>
                                <button class="wp-ex-remove" onclick="removeExercise(this)" type="button"><i
                                        class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-divider"></div>
                <div class="modal-footer">
                    <a class="btn-modal-cancel" onclick="closePlanModal()">Cancel</a>
                    <button class="btn-modal-confirm" type="submit">
                        <i class="fas fa-save"></i> SAVE PLAN
                    </button>
                    <?php $view->showErrors(); ?>
                </div>
            </div>
        </div>
    </form>

    <!-- Toast -->
    <div class="toast" id="toast">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>


    <?php if ($hasErrors): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                openPlanModal();
            });
        </script>
    <?php endif; ?>
    <script>
        const dataPlan = <?php echo json_encode($data_plan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const dataExercise = <?php echo json_encode($data_exercise, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        // console.log(dataPlan);

        const plansGrid = document.getElementById('plansGrid');
        plansGrid.innerHTML = '';

        dataPlan.forEach(plan => {
            const card = document.createElement('div');
            card.className = 'wp-plan-card';
            card.dataset.category = plan.category.toLowerCase();

            // Dummy avatars for now
            const dummyAvatars = `
        <div class="mini-avatar" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">RK</div>
        <div class="mini-avatar" style="background:linear-gradient(135deg,#a855f7,#7c3aed);">AS</div>
        <div class="mini-avatar" style="background:linear-gradient(135deg,#22c55e,#16a34a);">MJ</div>
        <div class="more-count">+2</div>
    `;

            card.innerHTML = `
        <div class="card-top">
            <div class="badge-row">
                <span class="cat-badge cat-${plan.category.toLowerCase()}">${plan.category}</span>
            </div>
            <div class="card-menu-btn" onclick="toggleMenu(this)">
                <i class="fas fa-ellipsis-v"></i>
                <div class="card-dropdown">
                    <a href="./delete-plan?id=${plan.id}"><button class="danger"><i class="fas fa-trash"></i> Delete</button></a>
                </div>
            </div>
        </div>
        <div class="plan-icon icon-orange"><i class="fas fa-dumbbell"></i></div>
        <div class="plan-name">${plan.plan_name}</div>
        <div class="meta-row">
            <div class="meta-item"><i class="fas fa-calendar-week"></i> ${plan.duration} Weeks</div>
            <div class="meta-item"><i class="fas fa-signal"></i> 
    ${plan.days >= 7 ? Math.floor(plan.days / 7) + ' week' + (Math.floor(plan.days / 7) > 1 ? 's' : '') + ' (' + plan.days + ' days)' : plan.days + ' day' + (plan.days > 1 ? 's' : '')}
</div>
        <div class="card-footer">
            <div class="avatars">
                ${dummyAvatars}
            </div>
            <a href="./view-plan?plan_id=${plan.id}">
            <button class="btn btn-sm">View <i class="fas fa-arrow-right"></i></button>
            </a>
        </div>
    `;

            plansGrid.appendChild(card);
        });
        /* ── SIDEBAR ── */
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        });
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        /* ── TABS ── */
        document.getElementById('tabGroup').addEventListener('click', e => {
            const tab = e.target.closest('.wp-tab'); if (!tab) return;
            document.querySelectorAll('.wp-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const filter = tab.dataset.tab;
            document.querySelectorAll('.wp-plan-card:not(.new-plan-card)').forEach(card => {
                card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
                if (card.style.display !== 'none') card.style.animation = 'fadeUp .3s ease both';
            });
        });

        /* ── SEARCH ── */
        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.wp-plan-card:not(.new-plan-card)').forEach(card => {
                const name = card.querySelector('.plan-name')?.textContent.toLowerCase() || '';
                const desc = card.querySelector('.plan-desc')?.textContent.toLowerCase() || '';
                card.style.display = (name.includes(q) || desc.includes(q)) ? '' : 'none';
            });
        });

        /* ── CARD DROPDOWN ── */
        document.addEventListener('click', e => {
            if (!e.target.closest('.card-menu-btn'))
                document.querySelectorAll('.card-dropdown').forEach(d => d.classList.remove('open'));
        });
        function toggleMenu(btn) {
            const dd = btn.querySelector('.card-dropdown');
            document.querySelectorAll('.card-dropdown').forEach(d => { if (d !== dd) d.classList.remove('open'); });
            dd.classList.toggle('open');
        }

        /* ── MODAL ── */
        function openPlanModal() {
            document.getElementById('planModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closePlanModal() {
            document.getElementById('planModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        function handleBackdrop(e) {
            if (e.target === document.getElementById('planModal')) closePlanModal();
        }
        document.getElementById('btnCreatePlan').addEventListener('click', openPlanModal);

        /* ── DAYS ── */
        const dayButtons = document.querySelectorAll(".wp-day-btn");
        const hiddenInput = document.getElementById("selectedDays");

        dayButtons.forEach(btn => {
            btn.addEventListener("click", function () {
                this.classList.toggle("active");

                let selected = [];

                dayButtons.forEach(b => {
                    if (b.classList.contains("active")) {
                        selected.push(b.dataset.day);
                    }
                });

                hiddenInput.value = selected.join(",");
                selected.value = selectedDays;
            });
        });

        /* ── ADD / REMOVE EXERCISE ── */
        let exCount = 2;
        document.getElementById('btnAddExercise').addEventListener('click', () => {
            exCount++;
            const list = document.getElementById('exerciseList');
            const row = document.createElement('div');
            row.className = 'wp-exercise-row';
            row.innerHTML = `
    <div class="wp-ex-num">${exCount}</div>
    <div class="wp-ex-fields">
    <input class="form-input wp-ex-input" type="text" placeholder="Exercise name"  name="exercise_name[]">
    <input class="form-input wp-ex-input-sm" type="text" placeholder="Sets" name="sets[]">
    <input class="form-input wp-ex-input-sm" type="text" placeholder="Reps" name="reps[]">
    <input class="form-input wp-ex-input-sm" type="text" placeholder="Rest(s)" name="rest[]">
    </div>
    <button class="wp-ex-remove" onclick="removeExercise(this)" type="button"><i class="fas fa-times"></i></button>`;
            list.appendChild(row);
            row.querySelector('input').focus();
            row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });

        function removeExercise(btn) {
            const row = btn.closest('.wp-exercise-row');
            row.style.cssText = 'opacity:0;transform:translateX(8px);transition:all .18s ease';
            setTimeout(() => {
                row.remove();
                document.querySelectorAll('.wp-ex-num').forEach((el, i) => el.textContent = i + 1);
                exCount = document.querySelectorAll('.wp-exercise-row').length;
            }, 180);
        }
        /* ── KEYBOARD ── */
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePlanModal(); });
    </script>
    <script src="./js/script.js"></script>
    <script src="./js/theme.js"></script>
</body>

</html>