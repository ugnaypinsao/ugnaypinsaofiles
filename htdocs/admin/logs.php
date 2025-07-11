<?php
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>System Logs</title>
  <link rel="stylesheet" href="../assets/css/archive.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
  <div class="archive-container">
    <div class="archive-header">
      <a href="index.php">
        <h1><i class="fas fa-clipboard-list"></i> System Logs</h1>
      </a>

      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Admin</a>
    </div>

    <div class="search-container">
      <i class="fas fa-search search-icon"></i>
      <input type="text" id="searchInput" class="search-bar" placeholder="Search logs...">

    </div>
    <select id="tableFilter" class="filter-dropdown search-bar" style="margin-bottom: 2%; width:25%">
      <option value="">All Tables</option>
    </select>

    <div class="no-results" id="noResults">
      <i class="fas fa-search-minus"></i>
      <p>No logs found matching your search</p>
    </div>

    <div class="archive-list" id="archiveList">
      <!-- Logs will be dynamically inserted here -->
    </div>
    <div class="pagination" id="paginationControls"></div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <!-- <script>
    document.addEventListener('DOMContentLoaded', () => {
      const archiveList = document.getElementById('archiveList');
      const searchInput = document.getElementById('searchInput');
      const tableFilter = document.getElementById('tableFilter');
      const noResults = document.getElementById('noResults');
      let allLogs = [];

      function loadLogs() {
        axios.get('../php/get_logs.php')
          .then(response => {
            if (response.data.success) {
              allLogs = response.data.data;
              populateTableFilter(allLogs);
              renderLogs(allLogs);
            } else {
              console.error(response.data.error);
            }
          })
          .catch(err => {
            console.error('Fetch error:', err);
          });
      }

      function populateTableFilter(logs) {
        const tables = [...new Set(logs.map(log => log.table_name).filter(Boolean))];
        tables.sort();
        tables.forEach(name => {
          const option = document.createElement('option');
          option.value = name;
          option.textContent = name;
          tableFilter.appendChild(option);
        });
      }

      function renderLogs(logs) {
        archiveList.innerHTML = '';
        if (logs.length === 0) {
          noResults.style.display = 'block';
          return;
        }
        noResults.style.display = 'none';
        logs.forEach((log, i) => {
          archiveList.appendChild(createLogItem(log, i));
        });
      }

      function createLogItem(log, index) {
        const item = document.createElement("div");
        item.classList.add("archive-item");

        const borderColors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];
        const borderColor = borderColors[index % borderColors.length];
        item.style.borderLeftColor = borderColor;

        const timestamp = log.created_at ?
          new Date(log.created_at).toLocaleString() : "Unknown Date";

        item.innerHTML = `
        <div class="archive-item-content">
          <p><strong><i class="fas fa-user" style="color: ${borderColor};"></i> User:</strong> ${log.user_name} (ID: ${log.user_id})</p>
          <p><strong><i class="fas fa-tasks" style="color: ${borderColor};"></i> Action:</strong> ${log.action} on <em>${log.table_name}</em> ${log.record_id ? `(ID: ${log.record_id})` : ''}</p>
          ${log.description ? `<p><strong><i class="fas fa-info-circle" style="color: ${borderColor};"></i> Description:</strong> ${log.description}</p>` : ''}
          ${log.reason ? `<p><strong><i class="fas fa-exclamation-circle" style="color: ${borderColor};"></i> Reason:</strong> ${log.reason}</p>` : ''}
          <p><strong><i class="fas fa-clock"></i> Timestamp:</strong> ${timestamp}</p>
          <p><strong><i class="fas fa-clipboard-list" style="color: ${borderColor};"></i> Log Message:</strong> ${log.log_message}</p>
        </div>
      `;
        return item;
      }

      function applyFilters() {
        const query = searchInput.value.toLowerCase();
        const selectedTable = tableFilter.value;

        const filtered = allLogs.filter(log => {
          const matchesSearch =
            (log.user_name || '').toLowerCase().includes(query) ||
            (log.table_name || '').toLowerCase().includes(query) ||
            (log.action || '').toLowerCase().includes(query) ||
            (log.log_message || '').toLowerCase().includes(query);

          const matchesTable = selectedTable === '' || log.table_name === selectedTable;

          return matchesSearch && matchesTable;
        });

        renderLogs(filtered);
      }

      searchInput.addEventListener('input', applyFilters);
      tableFilter.addEventListener('change', applyFilters);

      loadLogs();
    });
  </script> -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const archiveList = document.getElementById('archiveList');
      const searchInput = document.getElementById('searchInput');
      const tableFilter = document.getElementById('tableFilter');
      const noResults = document.getElementById('noResults');
      const paginationControls = document.getElementById('paginationControls');

      let allLogs = [];
      let currentPage = 1;
      const logsPerPage = 10;

      function loadLogs() {
        axios.get('../php/get_logs.php')
          .then(response => {
            if (response.data.success) {
              allLogs = response.data.data;
              populateTableFilter(allLogs);
              applyFilters();
            } else {
              console.error(response.data.error);
            }
          })
          .catch(err => {
            console.error('Fetch error:', err);
          });
      }

      function populateTableFilter(logs) {
        const tables = [...new Set(logs.map(log => log.table_name).filter(Boolean))];
        tables.sort();
        tables.forEach(name => {
          const option = document.createElement('option');
          option.value = name;
          option.textContent = name;
          tableFilter.appendChild(option);
        });
      }

      function paginateLogs(logs, page) {
        const start = (page - 1) * logsPerPage;
        return logs.slice(start, start + logsPerPage);
      }

      function renderPagination(totalLogs) {
        const totalPages = Math.ceil(totalLogs / logsPerPage);
        paginationControls.innerHTML = '';

        if (totalPages <= 1) return;

        const createButton = (label, page, disabled = false, isActive = false) => {
          const button = document.createElement('button');
          button.textContent = label;
          button.disabled = disabled;
          if (isActive) button.classList.add('active');

          button.addEventListener('click', () => {
            if (!disabled && page !== currentPage) {
              currentPage = page;
              applyFilters();
              // Optional: scroll to top
              window.scrollTo({
                top: archiveList.offsetTop - 50,
                behavior: 'smooth'
              });
            }
          });

          return button;
        };

        // Previous
        paginationControls.appendChild(createButton('« Prev', currentPage - 1, currentPage === 1));

        // Page numbers
        // for (let i = 1; i <= totalPages; i++) {
        //   paginationControls.appendChild(createButton(i, i, false, i === currentPage));
        // }

        // Next
        paginationControls.appendChild(createButton('Next »', currentPage + 1, currentPage === totalPages));
      }



      function renderLogs(logs) {
        archiveList.innerHTML = '';
        if (logs.length === 0) {
          noResults.style.display = 'block';
          paginationControls.innerHTML = '';
          return;
        }
        noResults.style.display = 'none';

        const paginated = paginateLogs(logs, currentPage);
        paginated.forEach((log, i) => {
          archiveList.appendChild(createLogItem(log, i));
        });

        renderPagination(logs.length);
      }

      function createLogItem(log, index) {
        const item = document.createElement("div");
        item.classList.add("archive-item");

        const borderColors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];
        const borderColor = borderColors[index % borderColors.length];
        item.style.borderLeftColor = borderColor;

        const timestamp = log.created_at ? new Date(log.created_at).toLocaleString() : "Unknown Date";

        item.innerHTML = `
        <div class="archive-item-content">
          <p><strong><i class="fas fa-user" style="color: ${borderColor};"></i> User:</strong> ${log.user_name} (ID: ${log.user_id})</p>
          <p><strong><i class="fas fa-tasks" style="color: ${borderColor};"></i> Action:</strong> ${log.action} on <em>${log.table_name}</em> ${log.record_id ? `(ID: ${log.record_id})` : ''}</p>

          ${log.reason ? `<p><strong><i class="fas fa-exclamation-circle" style="color: ${borderColor};"></i> Reason:</strong> ${log.reason}</p>` : ''}
          <p><strong><i class="fas fa-clock"></i> Timestamp:</strong> ${timestamp}</p>
          <p><strong><i class="fas fa-clipboard-list" style="color: ${borderColor};"></i> Log Message:</strong> ${log.log_message}</p>
        </div>
      `;
        return item;
      }

      function applyFilters() {
        const query = searchInput.value.toLowerCase();
        const selectedTable = tableFilter.value;

        const filtered = allLogs.filter(log => {
          const matchesSearch =
            (log.user_name || '').toLowerCase().includes(query) ||
            (log.table_name || '').toLowerCase().includes(query) ||
            (log.action || '').toLowerCase().includes(query) ||
            (log.log_message || '').toLowerCase().includes(query);
          const matchesTable = selectedTable === '' || log.table_name === selectedTable;
          return matchesSearch && matchesTable;
        });

        const maxPage = Math.ceil(filtered.length / logsPerPage);
        if (currentPage > maxPage) currentPage = 1;

        renderLogs(filtered);
      }

      searchInput.addEventListener('input', () => {
        currentPage = 1;
        applyFilters();
      });

      tableFilter.addEventListener('change', () => {
        currentPage = 1;
        applyFilters();
      });

      loadLogs();
    });
  </script>
  <style>
    .pagination {
      display: flex;
      justify-content: center;
      margin: 20px 0;
      gap: 5px;
      flex-wrap: wrap;
    }

    .pagination button {
      padding: 6px 12px;
      border: 1px solid #ccc;
      background-color: white;
      cursor: pointer;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .pagination button:hover:not(:disabled) {
      background-color: #eee;
    }

    .pagination button.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }

    .pagination button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  </style>
</body>

</html>