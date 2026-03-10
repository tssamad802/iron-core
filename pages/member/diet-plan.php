<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
require_once './includes/view.php';
$auth = new auth(['member']);
$member_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$view = new view();
$fetching_diet = $controller->fetch_records('diet', ['*'], '', ['member_id' => $member_id]);
// echo '<pre>';
// print_r($fetching_diet);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IronCore — Create Diet Plan</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet" />
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <?php require_once 'member-sidebar.php'; ?>

    <div class="main-area">

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

        <div class="page-area">

            <div class="page-header anim-fade-up">
                <div class="page-title-block">
                    <div class="title">Diet Plan</div>
                    <div class="subtitle">Create your personalized nutrition plan</div>
                </div>
            </div>

            <form action="./diet-plan-script" method="post">
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                <div class="diet-form-card anim-fade-up anim-d1">

                    <div class="diet-form-header">
                        <div class="diet-form-icon"><i class="fa-solid fa-leaf"></i></div>
                        <div>
                            <div class="diet-form-title">Create Diet Plan</div>
                            <div class="diet-form-subtitle">Fill in the details below to set up your plan</div>
                        </div>
                    </div>

                    <div class="diet-form-body">

                        <div class="form-grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Plan Name <span style="color:var(--accent);">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-tag input-icon"></i>
                                    <input type="text" class="form-input" placeholder="e.g. Summer Cut Plan"
                                        name="diet_name">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Goal <span style="color:var(--accent);">*</span></label>
                                <select name="goal" class="form-select">
                                    <option>Weight Loss</option>
                                    <option>Lean Bulk</option>
                                    <option>Maintain Weight</option>
                                    <option>Build Muscle</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Daily Calories (kcal) <span
                                        style="color:var(--accent);">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-fire input-icon"></i>
                                    <input type="number" class="form-input" placeholder="e.g. 2200" name="calories">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Duration (weeks)</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-calendar-days input-icon"></i>
                                    <input type="number" class="form-input" placeholder="e.g. 8" name="duration">
                                </div>
                            </div>
                        </div>

                        <div class="divider" style="margin:4px 0;">
                            <div class="divider-line"></div>
                            <span class="divider-text">Daily Meals</span>
                            <div class="divider-line"></div>
                        </div>

                        <div>
                            <div id="mealList" style="display:flex;flex-direction:column;gap:8px;">
                                <div class="meal-row">
                                    <div class="meal-row-type"><i class="fa-solid fa-sun"></i> Breakfast</div>
                                    <input class="meal-row-input" type="text" placeholder="e.g. Oats, eggs, banana"
                                        name="breakfast">
                                    <a class="meal-remove-btn" onclick="removeMeal(this)"><i
                                            class="fa-solid fa-xmark"></i></a>
                                </div>
                                <div class="meal-row">
                                    <div class="meal-row-type"><i class="fa-solid fa-bowl-food"></i> Lunch</div>
                                    <input class="meal-row-input" type="text" placeholder="e.g. Chicken, rice, veggies"
                                        name="lunch">
                                    <a class="meal-remove-btn" onclick="removeMeal(this)"><i
                                            class="fa-solid fa-xmark"></i></a>
                                </div>
                                <div class="meal-row">
                                    <div class="meal-row-type"><i class="fa-solid fa-moon"></i> Dinner</div>
                                    <input class="meal-row-input" type="text" placeholder="e.g. Salmon, sweet potato"
                                        name="dinner">
                                    <a class="meal-remove-btn" onclick="removeMeal(this)"><i
                                            class="fa-solid fa-xmark"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Notes</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-pen input-icon" style="top:14px;transform:none;"></i>
                                <textarea class="form-input wp-textarea" rows="3" name="notes"
                                    placeholder="Allergies, dietary restrictions, preferences…"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="diet-form-footer">
                        <button class="btn btn-secondary" onclick="resetForm()">Reset</button>
                        <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Save Plan</button>
                    </div>

                </div>
                <?php $view->showErrors(); ?>
            </form>

            <!-- ── Plans Listing ── -->
            <div class="plans-listing anim-fade-up anim-d2">
                <div class="listing-card">

                    <div class="listing-header">
                        <div class="listing-title"><i class="fa-solid fa-list-ul"></i> My Diet Plans</div>
                        <span class="listing-count" id="listingCount"></span>
                    </div>

                    <div class="table-responsive">
                        <table class="listing-table">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th class="col-goal">Goal</th>
                                    <th class="col-duration">Duration</th>
                                    <th>Calories</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="listingBody"></tbody>
                        </table>
                    </div>

                    <div class="listing-pagination">
                        <div class="listing-pag-info" id="pagInfo"></div>
                        <div class="listing-pag-controls" id="pagControls"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="toast" id="toast" style="display:none;">
        <span class="toast-icon" id="toastIcon">⚡</span>
        <div>
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-sub" id="toastSub"></div>
        </div>
    </div>

    <script>
        const mealLabels = [
            { icon: 'fa-sun', label: 'Breakfast' },
            { icon: 'fa-bowl-food', label: 'Lunch' },
            { icon: 'fa-moon', label: 'Dinner' },
        ];


        function removeMeal(btn) {
            if (document.getElementById('mealList').children.length <= 1) return;
            btn.closest('.meal-row').remove();
        }

        function resetForm() {
            document.querySelectorAll('.form-input, .form-select').forEach(el => el.value = '');
        }

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }

        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }

        function doLogout() { window.location.href = './logout.php'; }

        /* ── Listing + Pagination ── */
        const plans = <?php echo json_encode($fetching_diet); ?>;
        console.log(plans);

        const PER_PAGE = 5;
        let currentPage = 1;

        const statusBadge = s => s === 'active'
            ? `<span class="badge badge-active">Active</span>`
            : `<span class="badge badge-expired">Inactive</span>`;

        function renderListing() {
            const total = plans.length;
            const totalPages = Math.ceil(total / PER_PAGE);
            const start = (currentPage - 1) * PER_PAGE;
            const slice = plans.slice(start, start + PER_PAGE);

            // Count badge
            document.getElementById('listingCount').textContent = total + ' Plans';

            // Rows
            document.getElementById('listingBody').innerHTML = slice.map(p => `
                <tr>
                    <td>
                        <div class="plan-name-val">${p.diet_name}</div>
                    </td>
                    <td class="col-goal">${p.goal}</td>
                    <td class="col-duration">${p.duration}</td>
                    <td>${p.calories} kcal</td>
                    <td>
                        <div class="listing-actions">
                            <a href="delete_diet?id=${p.id}"><button class="action-btn remove" title="Delete"><i class="fa-solid fa-trash"></i></button></a>
                        </div>
                    </td>
                </tr>`).join('');

            // Pag info
            document.getElementById('pagInfo').innerHTML =
                `Showing <span>${start + 1}–${Math.min(start + PER_PAGE, total)}</span> of <span>${total}</span>`;

            // Pag buttons
            let btns = `<button class="listing-pag-btn" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></button>`;
            for (let i = 1; i <= totalPages; i++) {
                btns += `<button class="listing-pag-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
            }
            btns += `<button class="listing-pag-btn" onclick="goPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i></button>`;
            document.getElementById('pagControls').innerHTML = btns;
        }

        function goPage(p) {
            const total = plans.length;
            const totalPages = Math.ceil(total / PER_PAGE);
            if (p < 1 || p > totalPages) return;
            currentPage = p;
            renderListing();
        }

        renderListing();
    </script>
    <script src="./js/script.js"></script>
</body>

</html>