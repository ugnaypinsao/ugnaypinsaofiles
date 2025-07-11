<?php
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Message Archive</title>
  <link rel="stylesheet" href="../assets/css/archive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="archive-container">
    <div class="archive-header">
      <a href="index.php">
        <h1><i class="fas fa-archive"></i> Message Archive</h1>
      </a>
      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Admin</a>
    </div>

    <div class="search-container">
      <i class="fas fa-search search-icon"></i>
      <input type="text" id="searchInput" class="search-bar" placeholder="Search by name...">
    </div>

    <div class="no-results" id="noResults">
      <i class="fas fa-search-minus"></i>
      <p>No messages found matching your search</p>
    </div>

    <div class="archive-list" id="archiveList">
      <!-- Archived messages will be displayed here -->
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const archiveList = document.getElementById('archiveList');
      const searchInput = document.getElementById('searchInput');
      const noResults = document.getElementById('noResults');
      let allMessages = [];

      function loadArchive() {
        axios.get('../php/get_archives.php')
          .then(response => {
            if (response.data.success) {
              allMessages = response.data.data;
              renderMessages(allMessages);
            } else {
              console.error(response.data.error);
            }
          })
          .catch(err => {
            console.error('Fetch error:', err);
          });
      }

      function renderMessages(messages) {
        archiveList.innerHTML = '';
        if (messages.length === 0) {
          noResults.style.display = 'block';
          return;
        }
        noResults.style.display = 'none';
        messages.forEach((msg, i) => {
          archiveList.appendChild(createArchiveItem(msg, i));
        });
      }

      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        const filtered = allMessages.filter(msg =>
          (msg.email || '').toLowerCase().includes(query)
        );
        renderMessages(filtered);
      });

      loadArchive();
    });
  </script>
  <script>
    function createArchiveItem(msg, index) {
      const archiveItem = document.createElement("div");
      archiveItem.classList.add("archive-item");

      // Add different border colors based on index for visual variety
      const borderColors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];
      const borderColor = borderColors[index % borderColors.length];
      archiveItem.style.borderLeftColor = borderColor;

      const formattedTimestamp = msg.created_at ? new Date(msg.created_at).toLocaleString() : "Unknown";
      const deletedAt = msg.deleted_at ? new Date(msg.deleted_at).toLocaleString() : "Unknown";

      archiveItem.innerHTML = `
        <div class="archive-item-content">
            <span><i class="fas fa-user" style="color: ${borderColor};"></i> <strong>From:</strong> ${msg.email || 'Unknown'}</span>
            <p><i class="fas fa-comment" style="color: ${borderColor};"></i> <strong>Message:</strong> ${msg.message || 'No content'}</p>
            ${msg.deletion_reason ? `<p><i class="fas fa-exclamation-triangle" style="color: ${borderColor};"></i> <strong>Reason:</strong> ${msg.deletion_reason}</p>` : ''}
            <small><i class="fas fa-paper-plane"></i> <strong>Sent:</strong> ${formattedTimestamp}</small>
            <small><i class="fas fa-trash-alt"></i> <strong>Deleted:</strong> ${deletedAt}</small>
        </div>
    `;

      return archiveItem;
    }
  </script>
</body>

</html>