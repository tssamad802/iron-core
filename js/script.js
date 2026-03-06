/**
 * IronCore Gym — Admin Login Script
 * Handles: password toggle, demo credential fill,
 *          loading state, and toast notifications.
 */

"use strict";

/* ─────────────────────────────────────────────
   1. DOM REFERENCES
───────────────────────────────────────────── */
const loginForm = document.getElementById("loginForm");
const emailInput = document.getElementById("email");
const pwdInput = document.getElementById("password");
const loginBtn = document.getElementById("loginBtn");
const btnText = loginBtn.querySelector(".btn-text");
const btnSpinner = document.getElementById("btnSpinner");
const eyeToggle = document.getElementById("eyeToggle");
const toast = document.getElementById("toast");
const toastIcon = document.getElementById("toastIcon");
const toastTitle = document.getElementById("toastTitle");
const toastSub = document.getElementById("toastSub");
const demoBtns = document.querySelectorAll(".demo-btn");

/* ─────────────────────────────────────────────
   2. TOAST UTILITY
───────────────────────────────────────────── */
let toastTimer = null;

function showToast(icon, title, sub = "", duration = 3500) {
  toastIcon.textContent = icon;
  toastTitle.textContent = title;
  toastSub.textContent = sub;

  toast.style.display = "flex";
  void toast.offsetWidth;
  toast.classList.add("show");

  clearTimeout(toastTimer);
  toastTimer = setTimeout(hideToast, duration);
}

function hideToast() {
  toast.classList.remove("show");
  setTimeout(() => {
    toast.style.display = "none";
  }, 400);
}

toast.addEventListener("click", hideToast);

/* ─────────────────────────────────────────────
   3. PASSWORD VISIBILITY TOGGLE
───────────────────────────────────────────── */
eyeToggle.addEventListener("click", () => {
  const isPassword = pwdInput.type === "password";
  pwdInput.type = isPassword ? "text" : "password";
  eyeToggle.textContent = isPassword ? "🙈" : "👁";
  eyeToggle.title = isPassword ? "Hide password" : "Show password";
});

/* ─────────────────────────────────────────────
   4. FORM SUBMISSION HANDLER
───────────────────────────────────────────── */
loginForm.addEventListener("submit", () => {
  setLoadingState(true);
  showToast("🔐", "Authenticating...", "Verifying your credentials", 8000);
});

function setLoadingState(loading) {
  loginBtn.disabled = loading;
  btnText.style.display = loading ? "none" : "inline";
  btnSpinner.style.display = loading ? "inline" : "none";
}

/* ─────────────────────────────────────────────
   5. DEMO CREDENTIAL BUTTONS
───────────────────────────────────────────── */
demoBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    const email = btn.dataset.email;
    const pwd = btn.dataset.pwd;

    emailInput.value = email;
    pwdInput.value = pwd;

    pwdInput.type = "password";
    eyeToggle.textContent = "👁";

    const meta = roleLabels[email] || { label: "Demo User", icon: "👤" };
    showToast(
      meta.icon,
      `${meta.label} credentials loaded`,
      'Click "Access Dashboard" to sign in',
      3000,
    );
  });
});

/* ─────────────────────────────────────────────
   6. KEYBOARD SHORTCUT
───────────────────────────────────────────── */
document.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && document.activeElement !== loginBtn) {
    loginBtn.click();
  }
});

/* ─────────────────────────────────────────────
   7. PAGE LOAD: restore loading state guard
───────────────────────────────────────────── */
window.addEventListener("pageshow", () => {
  setLoadingState(false);
});

/* ─────────────────────────────────────────────
   8. LOGOUT
───────────────────────────────────────────── */
function doLogout() {
  const toast = document.getElementById("toast");
  const toastTitle = document.getElementById("toastTitle");
  const toastSub = document.getElementById("toastSub");
  toastTitle.innerText = "Logging Out...";
  toastSub.innerText = "Please wait a moment.";
  toast.style.display = "flex";

  setTimeout(function () {
    window.location.href = "./logout";
  }, 2000);
}

function goToLogin() {
  const toast = document.getElementById("toast");
  const toastTitle = document.getElementById("toastTitle");
  const toastSub = document.getElementById("toastSub");

  toastTitle.textContent = "Redirecting...";
  toastSub.textContent = "You will be redirected to Sign In shortly.";

  toast.style.display = "flex";

  toast.style.opacity = 0;
  let opacity = 0;
  const fadeIn = setInterval(() => {
    opacity += 0.05;
    toast.style.opacity = opacity;
    if (opacity >= 1) clearInterval(fadeIn);
  }, 20);

  setTimeout(() => {
    window.location.href = "./login";
  }, 2000);
}
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
  document.getElementById("overlay").classList.toggle("active");
}
function closeSidebar() {
  document.getElementById("sidebar").classList.remove("active");
  document.getElementById("overlay").classList.remove("active");
}
function openAssignModal(button) {
  const modal = document.getElementById("assignModal");
  const name = button.dataset.name;
  const user_id = button.dataset.id;
  document.getElementById("member_name").value = name;
  document.getElementById("userid").value = user_id;
  modal.classList.add("open");
  document.body.style.overflow = "hidden";
  console.log(name);
}
function closeModal() {
  document.getElementById("assignModal").classList.remove("open");
  document.body.style.overflow = "";
}

function handleBackdropClick(event) {
  if (event.target === document.getElementById("assignModal")) {
    closeModal();
  }
}

function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
  document.getElementById("overlay").classList.toggle("open");
}

function closeSidebar() {
  document.getElementById("sidebar").classList.remove("open");
  document.getElementById("overlay").classList.remove("open");
}

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeModal();
  }
});
function openQuickAssignModal() {
  const backdrop = document.getElementById('quickAssignModal');
  const box = document.getElementById('quickAssignBox');

  backdrop.style.opacity = '1';
  backdrop.style.pointerEvents = 'all';
  box.style.transform = 'translateY(0) scale(1)';

  document.getElementById('qa-member').value = '';
  document.getElementById('qa-trainer').value = '';
}

function closeQuickAssignModal() {
  const backdrop = document.getElementById('quickAssignModal');
  const box = document.getElementById('quickAssignBox');

  backdrop.style.opacity = '0';
  backdrop.style.pointerEvents = 'none';
  box.style.transform = 'translateY(28px) scale(0.97)';
}

function handleQuickAssignBackdrop(event) {
  if (event.target === document.getElementById('quickAssignModal')) {
    closeQuickAssignModal();
  }
}
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') closeQuickAssignModal();
});
/* ═══════════════════════════════════════════════════
 GYMFLOW — WORKOUT PLANS PAGE
 workout-plans.js — UI interactions only
═══════════════════════════════════════════════════ */

/* ── SIDEBAR TOGGLE (mobile) ── */
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  });
}

function closeSidebar() {
  sidebar && sidebar.classList.remove('open');
  overlay && overlay.classList.remove('open');
}

/* ── PLAN CATEGORY TABS ── */
const tabs = document.querySelectorAll('.wp-tab');
const cards = document.querySelectorAll('.wp-plan-card:not(.wp-new-plan-card)');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    const filter = tab.dataset.tab;
    cards.forEach(card => {
      if (filter === 'all' || card.dataset.category === filter) {
        card.style.display = '';
        card.style.animation = 'fadeUp 0.3s ease both';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

/* ── SEARCH FILTER ── */
const searchInput = document.getElementById('wpSearch');
if (searchInput) {
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase().trim();
    cards.forEach(card => {
      const name = card.querySelector('.wp-plan-name')?.textContent.toLowerCase() || '';
      const desc = card.querySelector('.wp-plan-desc')?.textContent.toLowerCase() || '';
      const match = name.includes(query) || desc.includes(query);
      card.style.display = match ? '' : 'none';
    });
  });
}

/* ── PLAN CARD DROPDOWN MENU ── */
document.addEventListener('click', (e) => {
  // Close all dropdowns if clicked outside
  if (!e.target.closest('.wp-plan-menu-btn')) {
    document.querySelectorAll('.wp-plan-dropdown').forEach(d => d.classList.remove('open'));
  }
});

function togglePlanMenu(btn) {
  const dropdown = btn.querySelector('.wp-plan-dropdown');
  // Close others first
  document.querySelectorAll('.wp-plan-dropdown').forEach(d => {
    if (d !== dropdown) d.classList.remove('open');
  });
  dropdown.classList.toggle('open');
}



/* ── EXERCISE LIST ── */
let exerciseCount = 2;

document.getElementById('btnAddExercise')?.addEventListener('click', () => {
  exerciseCount++;
  const list = document.getElementById('exerciseList');
  const row = document.createElement('div');
  row.className = 'wp-exercise-row';
  row.style.animation = 'fadeUp 0.25s ease both';
  row.innerHTML = `
        <div class="wp-ex-num">${exerciseCount}</div>
        <div class="wp-ex-fields">
            <input class="form-input wp-ex-input" type="text" placeholder="Exercise name">
            <input class="form-input wp-ex-input-sm" type="text" placeholder="Sets">
            <input class="form-input wp-ex-input-sm" type="text" placeholder="Reps">
            <input class="form-input wp-ex-input-sm" type="text" placeholder="Rest (s)">
        </div>
        <button class="wp-ex-remove" onclick="removeExercise(this)" title="Remove">
            <i class="fas fa-times"></i>
        </button>`;
  list.appendChild(row);
  row.querySelector('input').focus();
  renumberExercises();
});

function removeExercise(btn) {
  const row = btn.closest('.wp-exercise-row');
  row.style.opacity = '0';
  row.style.transform = 'translateX(10px)';
  row.style.transition = 'all 0.2s ease';
  setTimeout(() => {
    row.remove();
    renumberExercises();
  }, 200);
}  

/* ── MODAL ── */
  function openModal() {
    document.getElementById('planModal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    document.getElementById('planModal').classList.remove('open');
    document.body.style.overflow = '';
  }
  function handleBackdrop(e) {
    if (e.target === document.getElementById('planModal')) closeModal();
  }

  /* ── CATEGORY ── */
  function selectCat(el) {
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
  }

  /* ── LEVEL ── */
  function selectLevel(el) {
    document.querySelectorAll('.level-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
  }

  /* ── STATUS ── */
  function selectStatus(el) {
    document.querySelectorAll('.status-opt').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
  }

  /* ── DAYS ── */
  document.getElementById('daysPicker').addEventListener('click', e => {
    const btn = e.target.closest('.day-btn');
    if (btn) btn.classList.toggle('active');
  });

  /* ── EXERCISES ── */
  function addExercise() {
    const list = document.getElementById('exerciseList');
    const num = list.children.length + 1;
    const row = document.createElement('div');
    row.className = 'ex-row';
    row.innerHTML = `
      <div class="ex-num">${num}</div>
      <div class="ex-fields">
        <input class="form-input ex-input" type="text" placeholder="Exercise name">
        <input class="form-input ex-input-sm" type="text" placeholder="Sets">
        <input class="form-input ex-input-sm" type="text" placeholder="Reps">
        <input class="form-input ex-input-sm" type="text" placeholder="Rest">
      </div>
      <button class="ex-remove" onclick="removeEx(this)"><i class="fas fa-times"></i></button>`;
    list.appendChild(row);
    row.querySelector('input').focus();
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function removeEx(btn) {
    const row = btn.closest('.ex-row');
    row.style.opacity = '0';
    row.style.transform = 'translateX(10px)';
    row.style.transition = 'all 0.2s ease';
    setTimeout(() => {
      row.remove();
      document.querySelectorAll('.ex-num').forEach((el, i) => el.textContent = i + 1);
    }, 200);
  }

  /* ── SAVE ── */
  function savePlan() {
    const name = document.getElementById('planName').value.trim();
    if (!name) {
      showToast('⚠️', 'Plan name required', 'Please enter a name for your workout plan.', 3000);
      document.getElementById('planName').focus();
      return;
    }
    closeModal();
    showToast('⚡', 'Plan Created!', `"${name}" has been saved to your library.`);
  }

  /* ── TOAST ── */
  let _toastTimer;
  function showToast(icon, title, sub = '', duration = 3500) {
    const t = document.getElementById('toast');
    document.getElementById('toastIcon').textContent = icon;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastSub').textContent = sub;
    t.style.display = 'flex';
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => {
      t.style.opacity = '0';
      t.style.transform = 'translateY(10px)';
      t.style.transition = 'all 0.3s ease';
      setTimeout(() => { t.style.display = 'none'; t.style.opacity = ''; t.style.transform = ''; t.style.transition = ''; }, 300);
    }, duration);
  }

  /* ── KEYBOARD ── */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });

  // auto open for demo
  setTimeout(openModal, 300);