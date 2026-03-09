<?php
require_once './includes/config.session.inc.php';
require_once './includes/dbh.inc.php';
require_once './includes/middleware.php';
require_once './includes/model.php';
require_once './includes/control.php';
$auth = new auth(['trainer']);
$user_id = $auth->get_id();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$join = "LEFT JOIN users ON diet.member_id = users.id";
$column = [
    "diet.id",
    "diet.diet_name",
    "diet.goal",
    "diet.calories",
    "diet.duration",
    "diet.breakfast",
    "diet.lunch",
    "diet.dinner",
    "diet.notes",
    "users.fullname"
];
$fetching_plans = $controller->fetch_records('diet', $column, $join);
// echo '<pre>';
// print_r($fetching_plans);
// echo '</pre>';
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymFlow</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">
</head>

<body class="dashboard-body">

    <?php require_once 'trainer-sidebar.php'; ?>

    <div class="dashboard-container">
        <main class="main-wrap">
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

            <!-- Toast -->
            <div class="toast" id="toast" style="display:none;">
                <span class="toast-icon" id="toastIcon">⚡</span>
                <div>
                    <div class="toast-title" id="toastTitle"></div>
                    <div class="toast-sub" id="toastSub"></div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 MEMBER PLANS SECTION
            ══════════════════════════════════════════ -->
            <div class="plans-page-content">

                <!-- Page Header -->
                <div class="mp-page-header">
                    <div>
                        <div class="mp-page-title">Member Plans</div>
                        <div class="mp-page-sub">View and manage diet plans assigned to your members</div>
                    </div>
                </div>

                <!-- Toolbar -->
                <div class="mp-toolbar">
                    <div class="mp-toolbar-left">
                        <div class="mp-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="mpSearchInput" placeholder="Search members or plans…">
                        </div>
                        <select class="mp-filter-select" id="mpGoalFilter">
                            <option value="">All Goals</option>
                            <option value="Weight Loss">Weight Loss</option>
                            <option value="Weight Gain">Weight Gain</option>
                            <option value="Muscle Gain">Muscle Gain</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mp-toolbar-right">
                        <div id="mpPerPageWrapper" style="display:flex;align-items:center;gap:8px;">
                            <span
                                style="font-family:'Rajdhani',sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--text-dim);">Show</span>
                            <select class="mp-filter-select" id="mpPerPage" style="min-width:70px;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Card -->
                <div class="mp-card">
                    <!-- Card Header -->
                    <div class="mp-card-header">
                        <div>
                            <div class="mp-card-title">
                                <i class="fas fa-utensils"></i>
                                Diet Plans Overview
                            </div>
                            <div class="mp-card-subtitle">All active member diet assignments</div>
                        </div>
                        <div class="mp-record-badge" id="mpRecordBadge">0 Records</div>
                    </div>

                    <!-- Table -->
                    <div class="mp-table-wrap">
                        <table class="mp-table" id="mpTable">
                            <thead>
                                <tr>
                                    <th class="sortable" data-col="memberName">
                                        Member <i class="fas fa-sort sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-col="planName">
                                        Plan Name <i class="fas fa-sort sort-icon"></i>
                                    </th>
                                    <th class="sortable col-goal" data-col="goal">
                                        Goal <i class="fas fa-sort sort-icon"></i>
                                    </th>
                                    <th class="sortable col-calories" data-col="calories">
                                        Calories <i class="fas fa-sort sort-icon"></i>
                                    </th>
                                    <th class="col-duration">Duration</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mpTableBody">
                                <!-- Rendered by JS -->
                            </tbody>
                        </table>

                        <!-- Empty state (hidden when rows exist) -->
                        <div class="mp-empty" id="mpEmptyState" style="display:none;">
                            <i class="fas fa-bowl-food"></i>
                            <h3>No Plans Found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mp-pagination">
                        <div class="mp-pag-info" id="mpPagInfo">Showing <span>0–0</span> of <span
                                id="mpTotalSpan">0</span></div>
                        <div class="mp-pag-controls" id="mpPagControls"></div>
                    </div>
                </div>

            </div>
            <!-- END MEMBER PLANS SECTION -->

        </main>
    </div>

    <!-- ══════════════════════════════════════════
         MEAL DETAIL MODAL
    ══════════════════════════════════════════ -->
    <div class="mp-modal-backdrop" id="mpModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="mpModalTitle">
        <div class="mp-modal" id="mpModal">
            <div class="mp-modal-header">
                <div class="mp-modal-icon"><i class="fas fa-utensils"></i></div>
                <div>
                    <div class="mp-modal-title" id="mpModalTitle">Daily Meal Plan</div>
                    <div class="mp-modal-sub" id="mpModalSub">Breakfast · Lunch · Dinner breakdown</div>
                </div>
                <button class="mp-modal-close" id="mpModalClose" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mp-modal-divider"></div>
            <div class="mp-modal-body" id="mpModalBody">
                <!-- Rendered by JS -->
            </div>
            <div class="mp-modal-footer">
                <button class="mp-btn-close-modal" id="mpModalFooterClose">Close</button>
            </div>
        </div>
    </div>

    <script src="./js/script.js"></script>
    <script>
        /* ══════════════════════════════════════════════════════
       MEMBER PLANS — DATA & UI CONTROLLER
    ══════════════════════════════════════════════════════ */

        const rawPlans = <?php echo json_encode($fetching_plans, JSON_PRETTY_PRINT); ?>;

        const AVATAR_COLORS = ["orange", "blue", "green", "yellow", "purple", "teal", "rose", "sky"];

        const plans = rawPlans.map((p, i) => ({
            // Keep originals
            id: p.id,
            diet_name: p.diet_name,
            member_id: p.member_id,
            fullname: p.fullname ?? 'Unknown Member',

            // Mapped fields JS expects
            memberName: p.fullname ?? 'Unknown Member',
            memberId: String(p.member_id ?? ''),
            planName: p.diet_name ?? '',
            goal: p.goal ?? '',
            calories: Number(p.calories) || 0,
            duration: p.duration ? `${p.duration} weeks` : 'N/A',
            avatarColor: AVATAR_COLORS[i % AVATAR_COLORS.length],

            // Flatten meal text into a simple modal-safe structure
            meals: {
                breakfast: { time: 'Morning', calories: 0, items: [{ name: p.breakfast ?? '—', cal: 0 }] },
                lunch: { time: 'Afternoon', calories: 0, items: [{ name: p.lunch ?? '—', cal: 0 }] },
                dinner: { time: 'Evening', calories: 0, items: [{ name: p.dinner ?? '—', cal: 0 }] },
            },

            notes: p.notes ?? '',
        }));


        // ── Modal state ──────────────────────────────────────
        const modal = {
            backdropEl: document.getElementById("mpModalBackdrop"),
            bodyEl: document.getElementById("mpModalBody"),
            titleEl: document.getElementById("mpModalTitle"),
            subEl: document.getElementById("mpModalSub"),
            currentPlan: null,

            open(plan) {
                this.currentPlan = plan;
                this.render(plan);
                this.backdropEl.classList.add("open");
                document.body.style.overflow = "hidden";
            },

            close() {
                this.backdropEl.classList.remove("open");
                document.body.style.overflow = "";
                this.currentPlan = null;
            },

            render(plan) {
                this.titleEl.textContent = plan.planName;
                this.subEl.textContent = `${plan.memberName}  ·  ${plan.goal}  ·  ${plan.duration}`;

                const totalCals =
                    plan.meals.breakfast.calories +
                    plan.meals.lunch.calories +
                    plan.meals.dinner.calories;

                const mealConfig = [
                    {
                        key: "breakfast",
                        label: "Breakfast",
                        time: plan.meals.breakfast.time,
                        calories: plan.meals.breakfast.calories,
                        items: plan.meals.breakfast.items,
                        icon: "☀️",
                        colorClass: "breakfast",
                    },
                    {
                        key: "lunch",
                        label: "Lunch",
                        time: plan.meals.lunch.time,
                        calories: plan.meals.lunch.calories,
                        items: plan.meals.lunch.items,
                        icon: "🍽️",
                        colorClass: "lunch",
                    },
                    {
                        key: "dinner",
                        label: "Dinner",
                        time: plan.meals.dinner.time,
                        calories: plan.meals.dinner.calories,
                        items: plan.meals.dinner.items,
                        icon: "🌙",
                        colorClass: "dinner",
                    },
                ];

                const memberRow = `
                <div class="mp-modal-member-row">
                    <div class="mp-avatar ${plan.avatarColor}" style="width:40px;height:40px;border-radius:10px;font-size:13px;">
                        ${plan.memberName
                        .split(" ")
                        .map((w) => w[0])
                        .join("")
                        .slice(0, 2)
                        .toUpperCase()}
                    </div>
                    <div class="mp-modal-member-info">
                        <div class="name">${plan.fullname}</div>
                        <div class="plan">${plan.diet_name}</div>
                    </div>
                    <div class="mp-modal-meta-chips">
                        <span class="mp-meta-chip"><i class="fas fa-fire"></i>&nbsp;${plan.calories.toLocaleString()} kcal</span>
                        <span class="mp-meta-chip"><i class="fas fa-clock"></i>&nbsp;${plan.duration}</span>
                    </div>
                </div>`;

                const mealCardsHTML = mealConfig
                    .map(
                        (m) => `
                <div class="mp-meal-card">
                    <div class="mp-meal-card-head">
                        <div class="mp-meal-icon ${m.colorClass}">${m.icon}</div>
                        <div>
                            <div class="mp-meal-label">${m.label}</div>
                            <div class="mp-meal-time">${m.time}</div>
                        </div>
                        <div class="mp-meal-cal-badge ${m.colorClass}">
                            ${m.calories.toLocaleString()}<span class="mp-meal-cal-unit">kcal</span>
                        </div>
                    </div>
                    <div class="mp-meal-card-body">
                        <ul class="mp-meal-items">
                            ${m.items
                                .map(
                                    (item) => `
                                <li>
                                    ${item.name}
                                    <span class="mp-item-cal">${item.cal} kcal</span>
                                </li>
                            `,
                                )
                                .join("")}
                        </ul>
                    </div>
                </div>
            `,
                    )
                    .join("");

                const totalRow = `
                <div class="mp-modal-total-row">
                    <div class="mp-total-label">
                        <i class="fas fa-calculator"></i>
                        Total Daily Intake
                    </div>
                    <div>
                        <span class="mp-total-val">${totalCals.toLocaleString()}</span>
                        <span class="mp-total-unit">kcal / day</span>
                    </div>
                </div>`;

                this.bodyEl.innerHTML =
                    memberRow + `<div class="mp-meal-grid">${mealCardsHTML}</div>` + totalRow;
            },
        };

        // ── Table Controller ──────────────────────────────────
        const table = {
            data: [...plans],
            filtered: [...plans],
            sortCol: null,
            sortDir: "asc",
            currentPage: 1,
            perPage: 10,

            GOAL_CLASS_MAP: {
                "Weight Loss": "mp-goal-loss",
                "Weight Gain": "mp-goal-gain",
                "Muscle Gain": "mp-goal-muscle",
                Maintenance: "mp-goal-maint",
            },

            AVATAR_COLORS: [
                "orange",
                "blue",
                "green",
                "yellow",
                "purple",
                "teal",
                "rose",
                "sky",
            ],

            init() {
                this.filtered = [...this.data];
                this.bindEvents();
                this.render();
            },

            bindEvents() {
                // Search
                document.getElementById("mpSearchInput").addEventListener("input", () => {
                    this.currentPage = 1;
                    this.applyFilters();
                });

                // Goal filter
                document.getElementById("mpGoalFilter").addEventListener("change", () => {
                    this.currentPage = 1;
                    this.applyFilters();
                });

                // Per-page
                document.getElementById("mpPerPage").addEventListener("change", (e) => {
                    this.perPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.render();
                });

                // Sortable headers
                document.querySelectorAll(".mp-table thead th.sortable").forEach((th) => {
                    th.addEventListener("click", () => {
                        const col = th.dataset.col;
                        if (this.sortCol === col) {
                            this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
                        } else {
                            this.sortCol = col;
                            this.sortDir = "asc";
                        }
                        this.applySort();
                        this.updateSortIndicators(th);
                        this.render();
                    });
                });

                // Modal close buttons
                document
                    .getElementById("mpModalClose")
                    .addEventListener("click", () => modal.close());
                document
                    .getElementById("mpModalFooterClose")
                    .addEventListener("click", () => modal.close());

                // Backdrop click closes modal
                document
                    .getElementById("mpModalBackdrop")
                    .addEventListener("click", (e) => {
                        if (e.target === e.currentTarget) modal.close();
                    });

                // ESC key
                document.addEventListener("keydown", (e) => {
                    if (e.key === "Escape") modal.close();
                });
            },

            applyFilters() {
                const search = document
                    .getElementById("mpSearchInput")
                    .value.trim()
                    .toLowerCase();
                const goal = document.getElementById("mpGoalFilter").value;

                this.filtered = this.data.filter((p) => {
                    const matchSearch =
                        !search ||
                        p.memberName.toLowerCase().includes(search) ||
                        p.planName.toLowerCase().includes(search) ||
                        p.memberId.toLowerCase().includes(search);
                    const matchGoal = !goal || p.goal === goal;
                    return matchSearch && matchGoal;
                });

                this.applySort();
                this.render();
            },

            applySort() {
                if (!this.sortCol) return;
                const dir = this.sortDir === "asc" ? 1 : -1;
                this.filtered.sort((a, b) => {
                    const av = a[this.sortCol],
                        bv = b[this.sortCol];
                    if (typeof av === "number") return (av - bv) * dir;
                    return String(av).localeCompare(String(bv)) * dir;
                });
            },

            updateSortIndicators(activeTh) {
                document.querySelectorAll(".mp-table thead th.sortable").forEach((th) => {
                    th.classList.remove("sort-asc", "sort-desc");
                    const icon = th.querySelector(".sort-icon");
                    if (icon) {
                        icon.className = "fas fa-sort sort-icon";
                    }
                });
                activeTh.classList.add(this.sortDir === "asc" ? "sort-asc" : "sort-desc");
                const icon = activeTh.querySelector(".sort-icon");
                if (icon) {
                    icon.className = `fas fa-sort-${this.sortDir === "asc" ? "up" : "down"} sort-icon`;
                }
            },

            render() {
                const tbody = document.getElementById("mpTableBody");
                const empty = document.getElementById("mpEmptyState");
                const badge = document.getElementById("mpRecordBadge");
                const pagInfo = document.getElementById("mpPagInfo");
                const pagCtrl = document.getElementById("mpPagControls");

                const total = this.filtered.length;
                const pages = Math.max(1, Math.ceil(total / this.perPage));
                this.currentPage = Math.min(this.currentPage, pages);

                const start = (this.currentPage - 1) * this.perPage;
                const end = Math.min(start + this.perPage, total);
                const slice = this.filtered.slice(start, end);

                badge.textContent = `${total} Record${total !== 1 ? "s" : ""}`;

                if (total === 0) {
                    tbody.innerHTML = "";
                    empty.style.display = "block";
                } else {
                    empty.style.display = "none";
                    tbody.innerHTML = slice
                        .map(
                            (p, i) => `
                    <tr style="animation-delay:${i * 0.04}s">
                        <td>
                            <div class="mp-member-cell">
                                <div class="mp-avatar ${p.avatarColor}">
                                    ${p.memberName
                                    .split(" ")
                                    .map((w) => w[0])
                                    .join("")
                                    .slice(0, 2)
                                    .toUpperCase()}
                                </div>
                                <div>
                                    <div class="mp-member-name">${p.memberName}</div>
                                    <div class="mp-member-id">${p.memberId}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="mp-plan-name">${p.planName}</span></td>
                        <td class="col-goal">
                            <span class="mp-goal-badge ${this.GOAL_CLASS_MAP[p.goal] || ""}">${p.goal}</span>
                        </td>
                        <td class="col-calories">
                            <span class="mp-calories">${p.calories.toLocaleString()}</span>
                            <span class="mp-calories-unit">kcal</span>
                        </td>
                        <td class="col-duration">
                            <div class="mp-duration">
                                <i class="fas fa-clock"></i>
                                ${p.duration}
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <button class="mp-action-btn" onclick="openPlanModal(${p.id})">
                                <i class="fas fa-eye"></i> View Meals
                            </button>
                        </td>
                    </tr>
                `,
                        )
                        .join("");
                }

                // Pagination info
                pagInfo.innerHTML =
                    total === 0
                        ? "No records"
                        : `Showing <span>${start + 1}–${end}</span> of <span>${total}</span>`;

                // Pagination buttons
                const maxBtns = 5;
                let btns = "";

                // Prev
                btns += `<button class="mp-pag-btn" onclick="table.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? "disabled" : ""}>
                        <i class="fas fa-chevron-left" style="font-size:10px;"></i>
                     </button>`;

                // Page numbers
                let pageStart = Math.max(1, this.currentPage - Math.floor(maxBtns / 2));
                let pageEnd = Math.min(pages, pageStart + maxBtns - 1);
                if (pageEnd - pageStart < maxBtns - 1)
                    pageStart = Math.max(1, pageEnd - maxBtns + 1);

                if (pageStart > 1) {
                    btns += `<button class="mp-pag-btn" onclick="table.goToPage(1)">1</button>`;
                    if (pageStart > 2)
                        btns += `<button class="mp-pag-btn" disabled style="opacity:0.3;cursor:default;">…</button>`;
                }

                for (let pg = pageStart; pg <= pageEnd; pg++) {
                    btns += `<button class="mp-pag-btn ${pg === this.currentPage ? "active" : ""}" onclick="table.goToPage(${pg})">${pg}</button>`;
                }

                if (pageEnd < pages) {
                    if (pageEnd < pages - 1)
                        btns += `<button class="mp-pag-btn" disabled style="opacity:0.3;cursor:default;">…</button>`;
                    btns += `<button class="mp-pag-btn" onclick="table.goToPage(${pages})">${pages}</button>`;
                }

                // Next
                btns += `<button class="mp-pag-btn" onclick="table.goToPage(${this.currentPage + 1})" ${this.currentPage === pages ? "disabled" : ""}>
                        <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                     </button>`;

                pagCtrl.innerHTML = btns;
            },

            goToPage(pg) {
                const pages = Math.max(1, Math.ceil(this.filtered.length / this.perPage));
                if (pg < 1 || pg > pages) return;
                this.currentPage = pg;
                this.render();
            },
        };

        // ── Global helpers (called from inline onclick) ──────
        function openPlanModal(id) {
            const plan = plans.find((p) => p.id === id);
            if (plan) modal.open(plan);
        }

        // ── Init ─────────────────────────────────────────────
        document.addEventListener("DOMContentLoaded", () => table.init());

    </script>

</body>

</html>