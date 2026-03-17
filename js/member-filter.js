(function () {
  "use strict";

  /* ── Configuration ────────────────────────────────────── */
  const ROWS_PER_PAGE = 5;
  let currentPage = 1;
  let filteredRows = []; // To store rows that passed the search/filter

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
  const paginationContainer = document.querySelector(".page-btns");

  if (!filterSearch || !tbody || !paginationContainer) return;

  const allRows = Array.from(tbody.querySelectorAll("tr"));

  /* ── Core Logic ───────────────────────────────────────── */

  function applyFilters() {
    const query = filterSearch.value.trim().toLowerCase();
    const statusVal = filterStatus.value.toLowerCase();
    const trainerVal = filterTrainer.value.toLowerCase();

    // 1. Filter the rows
    filteredRows = allRows.filter((row) => {
      const nameEmail = (
        row.querySelector(".member-name")?.textContent +
        " " +
        row.querySelector(".member-email")?.textContent
      ).toLowerCase();
      const status =
        row.querySelector(".badge")?.textContent.toLowerCase() || "";
      const trainer =
        row.querySelector(".trainer-assigned")?.textContent.toLowerCase() || "";

      const matchSearch = !query || nameEmail.includes(query);
      const matchStatus = !statusVal || status.includes(statusVal);

      let matchTrainer = true;
      if (trainerVal) {
        if (trainerVal === "none" || trainerVal === "unassigned") {
          matchTrainer =
            trainer.includes("n/a") || trainer.includes("unassign");
        } else {
          matchTrainer = trainer.includes(trainerVal);
        }
      }

      return matchSearch && matchStatus && matchTrainer;
    });

    currentPage = 1; // Reset to page 1 on every new filter
    renderTable();
  }

  function renderTable() {
    const totalItems = filteredRows.length;
    const totalPages = Math.ceil(totalItems / ROWS_PER_PAGE);

    // 1. Hide all rows first
    allRows.forEach((row) => (row.style.display = "none"));

    // 2. Calculate slice
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageSlice = filteredRows.slice(start, end);

    // 3. Show only sliced rows
    pageSlice.forEach((row) => (row.style.display = ""));

    // 4. Update UI Components
    updatePaginationUI(totalPages);
    updatePageLabel(start + 1, Math.min(end, totalItems), totalItems);
  }

  function updatePaginationUI(totalPages) {
    paginationContainer.innerHTML = "";
    if (totalPages <= 1) return;

    // Previous Button
    const prevBtn = createPageBtn(
      '<i class="fa-solid fa-chevron-left"></i>',
      () => {
        if (currentPage > 1) {
          currentPage--;
          renderTable();
        }
      },
    );
    paginationContainer.appendChild(prevBtn);

    // Page Numbers
    for (let i = 1; i <= totalPages; i++) {
      const btn = createPageBtn(i, () => {
        currentPage = i;
        renderTable();
      });
      if (i === currentPage) btn.classList.add("active");
      paginationContainer.appendChild(btn);
    }

    // Next Button
    const nextBtn = createPageBtn(
      '<i class="fa-solid fa-chevron-right"></i>',
      () => {
        if (currentPage < totalPages) {
          currentPage++;
          renderTable();
        }
      },
    );
    paginationContainer.appendChild(nextBtn);
  }

  function createPageBtn(content, onClick) {
    const btn = document.createElement("button");
    btn.className = "page-btn";
    btn.innerHTML = content;
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      onClick();
      // Smooth scroll back to table top
      document
        .querySelector(".members-table")
        .scrollIntoView({ behavior: "smooth", block: "start" });
    });
    return btn;
  }

  function updatePageLabel(start, end, total) {
    if (total === 0) {
      pageInfo.textContent = "No members match your filters";
    } else {
      pageInfo.textContent = `Showing ${start}–${end} of ${total} members`;
    }
  }

  /* ── Event Listeners ──────────────────────────────────── */
  filterSearch.addEventListener("input", () => {
    applyFilters();
  });

  filterStatus.addEventListener("change", applyFilters);
  filterTrainer.addEventListener("change", applyFilters);

  // Initial Run
  applyFilters();
})();
