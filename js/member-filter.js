/* ============================================================
   Member Management — Search + Filter + Pagination
   Drop this into script.js or include as a separate file
   ============================================================ */

(function () {
  "use strict";

  /* ── DOM refs ─────────────────────────────────────────── */
  const filterSearch = document.querySelector(
    ".filter-bar .filter-search input",
  );
  const filterStatus = document.querySelectorAll(
    ".filter-bar .filter-select",
  )[0];
  const filterTrainer = document.querySelectorAll(
    ".filter-bar .filter-select",
  )[1];
  const tbody = document.querySelector(".members-table tbody");
  const pageInfo = document.querySelector(".page-info");

  if (!filterSearch || !filterStatus || !filterTrainer || !tbody) return;

  /* ── Collect all rows (on current page) ──────────────── */
  function getRows() {
    return Array.from(tbody.querySelectorAll("tr"));
  }

  /* ── Normalise text for comparison ───────────────────── */
  function norm(str) {
    return (str || "").trim().toLowerCase();
  }

  /* ── Read cell text helpers ──────────────────────────── */
  function memberText(row) {
    const name = row.querySelector(".member-name")?.textContent || "";
    const email = row.querySelector(".member-email")?.textContent || "";
    return norm(name + " " + email);
  }

  function statusText(row) {
    return norm(row.querySelector(".badge")?.textContent);
  }

  function trainerText(row) {
    return norm(row.querySelector(".trainer-assigned")?.textContent);
  }

  /* ── Core filter function ─────────────────────────────── */
  function applyFilters() {
    const query = norm(filterSearch.value);
    const statusVal = norm(filterStatus.value);
    const trainerVal = norm(filterTrainer.value);

    let visible = 0;
    const rows = getRows();

    rows.forEach((row) => {
      const matchSearch = !query || memberText(row).includes(query);
      const matchStatus = !statusVal || statusText(row).includes(statusVal);
      const matchTrainer = matchTrainer_fn(row, trainerVal);

      const show = matchSearch && matchStatus && matchTrainer;
      row.style.display = show ? "" : "none";
      if (show) visible++;
    });

    updatePageInfo(visible, rows.length);
    updatePaginationVisibility(visible < rows.length);
  }

  /* ── Trainer matching (handles "unassigned" / "none") ── */
  function matchTrainer_fn(row, trainerVal) {
    if (!trainerVal) return true; // "All Trainers"

    const txt = trainerText(row);

    if (trainerVal === "none" || trainerVal === "unassigned") {
      return txt.includes("n/a") || txt.includes("unassign");
    }

    return txt.includes(trainerVal);
  }

  /* ── Update the "Showing X–Y of Z members" label ──────── */
  function updatePageInfo(visible, total) {
    if (!pageInfo) return;

    if (visible === total) {
      /* No active filter — restore original server-side text */
      pageInfo.dataset.original =
        pageInfo.dataset.original || pageInfo.textContent;
      pageInfo.textContent = pageInfo.dataset.original;
    } else {
      pageInfo.dataset.original =
        pageInfo.dataset.original || pageInfo.textContent;
      pageInfo.textContent =
        visible === 0
          ? "No members match your filters"
          : `Showing ${visible} filtered result${visible !== 1 ? "s" : ""}`;
    }
  }

  /* ── Hide pagination when filtering (avoids confusion) ── */
  function updatePaginationVisibility(filtering) {
    const paginationBtns = document.querySelector(".page-btns");
    if (paginationBtns) {
      paginationBtns.style.opacity = filtering ? "0.35" : "";
      paginationBtns.style.pointerEvents = filtering ? "none" : "";
      paginationBtns.title = filtering ? "Clear filters to use pagination" : "";
    }
  }

  /* ── Debounce helper ─────────────────────────────────── */
  function debounce(fn, ms) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

  /* ── Wire up events ──────────────────────────────────── */
  filterSearch.addEventListener("input", debounce(applyFilters, 220));
  filterStatus.addEventListener("change", applyFilters);
  filterTrainer.addEventListener("change", applyFilters);

  /* ── Populate trainer dropdown from live table data ──── */
  (function populateTrainerDropdown() {
    const seen = new Set();
    let hasUnassigned = false;

    getRows().forEach((row) => {
      const txt = trainerText(row);
      if (txt.includes("n/a") || txt.includes("unassign")) {
        hasUnassigned = true;
      } else if (txt) {
        const raw = (
          row.querySelector(".trainer-assigned")?.textContent || ""
        ).trim();
        if (raw && raw.toLowerCase() !== "n/a") seen.add(raw);
      }
    });

    while (filterTrainer.options.length > 1) filterTrainer.remove(1);

    seen.forEach((name) => {
      const opt = document.createElement("option");
      opt.value = name.toLowerCase();
      opt.textContent = name;
      filterTrainer.appendChild(opt);
    });

    if (hasUnassigned) {
      const opt = document.createElement("option");
      opt.value = "none";
      opt.textContent = "Unassigned";
      filterTrainer.appendChild(opt);
    }
  })();
  (function persistFiltersInPaginationLinks() {
    function refreshLinks() {
      const q = filterSearch.value.trim();
      const status = filterStatus.value;
      const trainer = filterTrainer.value;

      document.querySelectorAll(".page-btns a").forEach((a) => {
        const url = new URL(a.href, location.href);
        if (q) url.searchParams.set("q", q);
        else url.searchParams.delete("q");
        if (status) url.searchParams.set("status", status);
        else url.searchParams.delete("status");
        if (trainer) url.searchParams.set("trainer", trainer);
        else url.searchParams.delete("trainer");
        a.href = url.toString();
      });
    }

    filterSearch.addEventListener("input", debounce(refreshLinks, 300));
    filterStatus.addEventListener("change", refreshLinks);
    filterTrainer.addEventListener("change", refreshLinks);
    const params = new URLSearchParams(location.search);
    if (params.get("q")) filterSearch.value = params.get("q");
    if (params.get("status")) filterStatus.value = params.get("status");
    if (params.get("trainer")) filterTrainer.value = params.get("trainer");
    if (params.get("q") || params.get("status") || params.get("trainer")) {
      applyFilters();
    }
  })();

  /* ── Also wire the topbar search box ─────────────────── */
  const topbarSearch = document.querySelector(".topbar .search-box input");
  if (topbarSearch) {
    topbarSearch.addEventListener(
      "input",
      debounce(function () {
        filterSearch.value = topbarSearch.value;
        applyFilters();
        /* Scroll to table */
        document
          .querySelector(".members-table")
          ?.scrollIntoView({ behavior: "smooth", block: "start" });
      }, 250),
    );
  }
})();
