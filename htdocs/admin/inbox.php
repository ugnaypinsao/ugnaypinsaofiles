<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Inbox</title>
  <link rel="stylesheet" href="../assets/css/admin_mes.css">
</head>

<body>
  <div class="inbox-container">
    <div class="inbox-header">
      <div><a href="index.php" class="text-decoration-none text-dark">Admin Inbox</a></div>
      <div class="filter-options">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="unread">Unread</button>
        <button class="filter-btn" data-filter="unresolved">Unresolved</button>
        <button class="filter-btn" data-filter="in progress">In Progress</button>
        <button class="filter-btn" data-filter="resolved">Resolved</button>
      </div>
    </div>
    <div class="message-list" id="messageList">
      <!-- Messages will be dynamically added here -->
    </div>
    <div class="message-detail" id="messageDetail">
      <h3 id="messageSender"></h3>
      <p id="messageText"></p>
      <small id="messageTimestamp"></small>
      <div class="message-status" id="messageStatus"></div>
      <div class="detail-buttons">
        <button onclick="closeDetail()">Back to Inbox</button>
        <button onclick="replyViaEmail()" class="reply-button">Reply via Email</button>
        <button onclick="showDeleteDialog()" class="delete-button">Delete Message</button>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Dialog -->
  <!-- Delete Confirmation Dialog -->
  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <h3>Delete Message</h3>
      <p>Please provide a reason for deleting this message:</p>
      <textarea id="deleteReason" placeholder="Enter reason for deletion..." rows="4"></textarea>
      <div class="modal-buttons">
        <button onclick="confirmDelete()" class="confirm-btn">Confirm Delete</button>
        <button onclick="cancelDelete()" class="cancel-btn">Cancel</button>
      </div>
    </div>
  </div>


  <!-- New Messages Popup -->
  <div id="newMessagesPopup" class="modal">
    <div class="modal-content">
      <h3>New Messages</h3>
      <p id="newMessagesCount"></p>
      <button id="popupOkButton">OK</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <script>
    // Function to show the delete confirmation dialog
    // Function to show the delete confirmation dialog
    function showDeleteDialog(event) {
      const messageItem = event.target.closest('.message-item');
      const messageId = messageItem.getAttribute('data-message-id'); // Get the message ID from the clicked message

      // Set the message ID in the detail view (if you need to use it later)
      document.getElementById('messageDetail').setAttribute('data-message-id', messageId);

      // Show the modal
      document.getElementById('deleteModal').style.display = 'block';
    }


    // Function to hide the delete confirmation dialog
    function cancelDelete() {
      document.getElementById('deleteModal').style.display = 'none'; // Hide the modal
    }

    // Function to confirm deletion
    function confirmDelete() {
      const reason = document.getElementById('deleteReason').value; // Get the reason for deletion
      const messageId = getMessageIdFromUI(); // Get the message ID that is set in the messageDetail element

      // Ensure a reason is provided
      if (!reason.trim()) {
        alert('Please provide a reason for deletion');
        return;
      }

      // Send the delete request to update the status to 'deleted'
      axios.post('../php/deleteMessage.php', {
          id: messageId,
          reason: reason
        })
        .then(response => {
          if (response.data.success) {
            alert('Message marked as deleted');
            loadMessages(); // Refresh the message list after marking as deleted
            cancelDelete(); // Close the modal
          } else {
            alert('Failed to mark the message as deleted');
          }
        })
        .catch(error => {
          console.error('Error marking message as deleted:', error);
          alert('An error occurred while marking the message as deleted.');
        });
    }

    // Function to fetch the message ID (you'll need to pass the ID or use a suitable selector)
    function getMessageIdFromUI() {
      const messageDetail = document.getElementById('messageDetail');
      return messageDetail.getAttribute('data-message-id'); // Correctly fetch the message ID
    }


    // Add event listeners to filter buttons
    document.querySelectorAll('.filter-btn').forEach(button => {
      button.addEventListener('click', handleFilterClick);
    });

    // Handler function for filter buttons
    function handleFilterClick(event) {
      const selectedFilter = event.target.getAttribute('data-filter');

      // Skip if it's already the current filter
      if (selectedFilter === currentFilter) return;

      currentFilter = selectedFilter;

      // Update active button styling
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      event.target.classList.add('active');

      applyFilter(currentFilter); // <- Add this instead
    }



    // Set default filter to 'all'
    let currentFilter = 'all';
    loadMessages()
    // Add event listeners to filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
      button.addEventListener('click', function() {
        // Update the current filter and refresh the message list
        currentFilter = button.getAttribute('data-filter');

        // Set active class to the clicked button and remove it from others
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        loadMessages(); // Reload messages based on the selected filter
      });
    });

    function loadMessages() {
      axios.get('../php/getMessages.php')
        .then(response => {
          const messages = response.data.messages || [];
          const messageList = document.getElementById("messageList");
          const messageDetail = document.getElementById("messageDetail");

          messageDetail.style.display = "none";
          messageList.innerHTML = ""; // Clear previous content

          if (messages.length === 0) {
            messageList.innerHTML = "<div class='empty-inbox'><p>No messages yet.</p></div>";
            return;
          }

          const reversedMessages = [...messages].reverse();
          let unreadCount = 0;

          reversedMessages.forEach((msg) => {
            if (msg.status == 'Unread') unreadCount++; // Count unread
            const messageItem = createMessageItem(msg);
            messageList.appendChild(messageItem);
          });

          applyFilter(currentFilter); // Hide/show based on current filter

          // Show popup if there are unread messages
          if (unreadCount > 0) {
            document.getElementById("newMessagesCount").textContent =
              `You have ${unreadCount} unread message${unreadCount > 1 ? 's' : ''}.`;
            document.getElementById("newMessagesPopup").style.display = "block";
          } else {
            document.getElementById("newMessagesPopup").style.display = "none";
          }
        })
        .catch(error => {
          console.error('Error fetching messages:', error);
          alert('Failed to load messages. Please try again later.');
        });
    }

    document.getElementById("popupOkButton").addEventListener("click", () => {
      document.getElementById("newMessagesPopup").style.display = "none";
    });



    function createMessageItem(msg) {
      const messageItem = document.createElement('div');
      messageItem.classList.add('message-item');
      messageItem.setAttribute('data-timestamp', msg.created_at);
      messageItem.setAttribute('data-status', msg.status.toLowerCase());
      messageItem.setAttribute('data-message-id', msg.id); // Add the message ID here

      const formattedTimestamp = msg.created_at ?
        new Date(msg.created_at).toLocaleString() :
        "Unknown Date";

      messageItem.innerHTML = `
    <div class="message-content">
      <span>${msg.email}</span>
      <h4>${msg.subject}</h4>
      <p>${msg.message}</p>
      <small>${formattedTimestamp}</small>
      <strong><small>Current Status: ${msg.status}</small></strong>
      ${msg.status === 'Unread' ? '<span class="unread-mark">●</span>' : ''}
    </div>
    <div class="status-buttons">
      <button class="status-btn unresolved" onclick="setStatus(event, 'Unresolved')">Unresolved</button>
      <button class="status-btn in-progress" onclick="setStatus(event, 'In Progress')">In Progress</button>
      <button class="status-btn resolved" onclick="setStatus(event, 'Resolved')">Resolved</button>
      <button class="delete-btn status-btn unresolved" onclick="showDeleteDialog(event)">❌</button>
    </div>
  `;

      return messageItem;
    }



    function applyFilter(filter) {
      const messageItems = document.querySelectorAll('.message-item');

      messageItems.forEach(item => {

        const status = item.getAttribute('data-status');
        const isRead = item.getAttribute('data-read') === 'true';
        item.style.display = 'block';
        if (filter === 'all') {
          item.style.display = 'block';
        } else {
          console.log(status)
          console.log(filter)
          item.style.display = (status === filter) ? 'block' : 'none';
        }
      });
    }

    function setStatus(event, newStatus) {
      const messageItem = event.target.closest('.message-item');
      const email = messageItem.querySelector("span").textContent;
      const timestamp = messageItem.getAttribute('data-timestamp'); // use raw timestamp!

      axios.post('../php/updateStatus.php', {
          email: email,
          timestamp: timestamp,
          status: newStatus
        })
        .then(response => {
          if (response.data.success) {
            loadMessages(); // Refresh to show new status
          } else {
            alert('Failed to update status.');
          }
        })
        .catch(error => {
          console.error('Error updating status:', error);
          alert('An error occurred while updating status.');
        });
    }
  </script>
</body>

</html>