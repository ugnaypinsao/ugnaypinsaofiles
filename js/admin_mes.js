let currentMessage = null; // Store the currently viewed message
let currentFilter = 'all'; // Store the current filter

function loadMessages() {
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const messageList = document.getElementById("messageList");
    const messageDetail = document.getElementById("messageDetail");

    // Hide detail view by default
    messageDetail.style.display = "none";

    if (messages.length === 0) {
        messageList.innerHTML = "<div class='empty-inbox'><p>No messages yet.</p></div>";
        return;
    }

    messageList.innerHTML = ""; // Clear existing messages

    // Reverse the messages array to show the last message first
    const reversedMessages = messages.reverse();

    let filteredMessages = reversedMessages;
    if (currentFilter !== 'all') {
        filteredMessages = reversedMessages.filter(msg => msg.status === currentFilter);
    }

    if (filteredMessages.length === 0) {
        messageList.innerHTML = `<div class='empty-inbox'><p>No ${currentFilter.replace('-', ' ')} messages.</p></div>`;
        return;
    }

    filteredMessages.forEach((msg, index) => {
        const messageItem = document.createElement("div");
        messageItem.classList.add("message-item");
        
        // Add status class if it exists
        if (msg.status) {
            messageItem.classList.add(msg.status);
        }

        // Add a class for unread messages
        if (!msg.read) {
            messageItem.classList.add("unread");
        }

        // Add a default timestamp if missing
        if (!msg.timestamp) {
            msg.timestamp = new Date().toISOString(); // Generate a new timestamp
        }

        // Format the timestamp
        const formattedTimestamp = msg.timestamp
            ? new Date(msg.timestamp).toLocaleString()
            : "Unknown Date"; // Placeholder if timestamp is missing or invalid

        messageItem.innerHTML = `
          <span>${msg.from}</span>
          <p>${msg.text}</p>
          <small>${formattedTimestamp}</small>
          ${!msg.read ? '<span class="unread-mark">●</span>' : ''} <!-- Unread indicator -->
          <div class="status-buttons">
            <button class="status-btn unresolved" onclick="setStatus(event, 'unresolved')">Unresolved</button>
            <button class="status-btn in-progress" onclick="setStatus(event, 'in-progress')">In Progress</button>
            <button class="status-btn resolved" onclick="setStatus(event, 'resolved')">Resolved</button>
          </div>
        `;
        messageItem.addEventListener("click", (e) => {
            // Don't trigger if clicking on status buttons
            if (!e.target.classList.contains('status-btn')) {
                markAsRead(msg); // Mark the message as read when clicked
                showDetail(msg);
            }
        });
        messageList.appendChild(messageItem);
    });
}

function setFilter(filter) {
    currentFilter = filter;
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    loadMessages();
}

function setStatus(event, status) {
    event.stopPropagation(); // Prevent triggering the message click event
    
    const messageItem = event.target.closest('.message-item');
    const messageText = messageItem.querySelector('p').textContent;
    const messageFrom = messageItem.querySelector('span').textContent;
    
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const messageIndex = messages.findIndex(m => m.text === messageText && m.from === messageFrom);
    
    if (messageIndex !== -1) {
        // Remove all status classes
        messageItem.classList.remove('unresolved', 'in-progress', 'resolved');
        // Add the new status class
        messageItem.classList.add(status);
        
        // Update the message status in storage
        messages[messageIndex].status = status;
        localStorage.setItem("messages", JSON.stringify(messages));
        
        // If this is the current message being viewed, update its status display
        if (currentMessage && currentMessage.text === messageText && currentMessage.from === messageFrom) {
            currentMessage.status = status;
            updateStatusDisplay();
        }
        
        // If we're filtering and the new status doesn't match the filter, remove the message
        if (currentFilter !== 'all' && status !== currentFilter) {
            messageItem.remove();
            
            // Show empty state if no messages left
            if (document.getElementById("messageList").children.length === 0) {
                document.getElementById("messageList").innerHTML = `<div class='empty-inbox'><p>No ${currentFilter.replace('-', ' ')} messages.</p></div>`;
            }
        }
    }
}

// ... (rest of the existing JavaScript code remains the same) ...

// Load messages on page load
document.addEventListener('DOMContentLoaded', () => {
    // Set up filter button event listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => setFilter(btn.dataset.filter));
    });
    
    loadMessages();
    
    // Check for new messages periodically
    setInterval(() => {
        const messages = JSON.parse(localStorage.getItem("messages")) || [];
        const lastMessageCount = localStorage.getItem("lastMessageCount") || 0;
        
        // Check if the number of messages has increased
        if (messages.length > lastMessageCount) {
            loadMessages();
            alert("You have new messages!");
            // Update the last message count
            localStorage.setItem("lastMessageCount", messages.length);
        }
    }, 3000);
});