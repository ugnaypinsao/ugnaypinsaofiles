let currentMessage = null; // Store the currently viewed message
let currentFilter = 'all'; // Store the current filter

// New messages popup functions
function showNewMessagesPopup(count) {
    const popup = document.getElementById("newMessagesPopup");
    const countElement = document.getElementById("newMessagesCount");
    
    countElement.textContent = `You have ${count} new message${count > 1 ? 's' : ''}!`;
    popup.style.display = "block";
}

function closeNewMessagesPopup() {
    document.getElementById("newMessagesPopup").style.display = "none";
}

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

    // Show messages in chronological order (newest first)
    const reversedMessages = [...messages].reverse();

    let filteredMessages = reversedMessages;
    if (currentFilter !== 'all') {
        if (currentFilter === 'unread') {
            filteredMessages = reversedMessages.filter(msg => !msg.read);
        } else {
            filteredMessages = reversedMessages.filter(msg => msg.status === currentFilter);
        }
    }

    if (filteredMessages.length === 0) {
        messageList.innerHTML = `<div class='empty-inbox'><p>No ${currentFilter.replace('-', ' ')} messages.</p></div>`;
        return;
    }

    filteredMessages.forEach((msg) => {
        const messageItem = createMessageItem(msg);
        messageList.appendChild(messageItem);
    });
}

function createMessageItem(msg) {
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
        msg.timestamp = new Date().toISOString();
    }

    // Format the timestamp
    const formattedTimestamp = msg.timestamp
        ? new Date(msg.timestamp).toLocaleString()
        : "Unknown Date";

    messageItem.innerHTML = `
      <div class="message-content">
        <span>${msg.from}</span>
        <p>${msg.text}</p>
        <small>${formattedTimestamp}</small>
        ${!msg.read ? '<span class="unread-mark">●</span>' : ''}
      </div>
      <div class="status-buttons">
        <button class="status-btn unresolved" onclick="setStatus(event, 'unresolved')">Unresolved</button>
        <button class="status-btn in-progress" onclick="setStatus(event, 'in-progress')">In Progress</button>
        <button class="status-btn resolved" onclick="setStatus(event, 'resolved')">Resolved</button>
      </div>
    `;
    
    // Add click event to the message content
    const messageContent = messageItem.querySelector('.message-content');
    messageContent.addEventListener("click", () => {
        markAsRead(msg);
        showDetail(msg);
    });
    
    return messageItem;
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
    event.stopPropagation();
    
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
        if (currentFilter !== 'all' && currentFilter !== 'unread' && status !== currentFilter) {
            messageItem.remove();
            
            // Show empty state if no messages left
            if (document.getElementById("messageList").children.length === 0) {
                document.getElementById("messageList").innerHTML = `<div class='empty-inbox'><p>No ${currentFilter.replace('-', ' ')} messages.</p></div>`;
            }
        }
    }
}

function updateStatusDisplay() {
    const statusDisplay = document.getElementById("messageStatus");
    if (currentMessage && currentMessage.status) {
        statusDisplay.textContent = `Status: ${currentMessage.status.replace('-', ' ').toUpperCase()}`;
        statusDisplay.className = `message-status ${currentMessage.status}`;
    } else {
        statusDisplay.textContent = '';
        statusDisplay.className = 'message-status';
    }
}

function markAsRead(msg) {
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const messageIndex = messages.findIndex(m => m.text === msg.text && m.from === msg.from);
    if (messageIndex !== -1) {
        messages[messageIndex].read = true;
        localStorage.setItem("messages", JSON.stringify(messages));
    }
}

function showDetail(msg) {
    const messageDetail = document.getElementById("messageDetail");
    const messageList = document.getElementById("messageList");

    // Store the currently viewed message
    currentMessage = msg;

    // Format the timestamp
    const formattedTimestamp = msg.timestamp
        ? new Date(msg.timestamp).toLocaleString()
        : "Unknown Date";

    document.getElementById("messageSender").textContent = `From: ${msg.from}`;
    document.getElementById("messageText").textContent = msg.text;
    document.getElementById("messageTimestamp").textContent = `Sent: ${formattedTimestamp}`;
    
    // Update status display
    updateStatusDisplay();

    // Show detail view and hide list
    messageDetail.style.display = "block";
    messageList.style.display = "none";
}

function closeDetail() {
    const messageDetail = document.getElementById("messageDetail");
    const messageList = document.getElementById("messageList");

    // Show list and hide detail view
    messageDetail.style.display = "none";
    messageList.style.display = "block";

    // Reload messages to update read/unread status
    loadMessages();
}

function showDeleteDialog() {
    document.getElementById("deleteModal").style.display = "block";
}

function cancelDelete() {
    document.getElementById("deleteModal").style.display = "none";
    document.getElementById("deleteReason").value = "";
}

function confirmDelete() {
    const reason = document.getElementById("deleteReason").value.trim();
    if (!reason) {
        showNewMessagesPopup("Please provide a reason for deletion");
        return;
    }

    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const updatedMessages = messages.filter(
        m => m.text !== currentMessage.text || m.from !== currentMessage.from
    );
    
    // Add to archive with deletion reason
    const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
    currentMessage.deletedAt = new Date().toISOString();
    currentMessage.deleteReason = reason;
    archive.push(currentMessage);
    localStorage.setItem("messageArchive", JSON.stringify(archive));
    
    // Update messages
    localStorage.setItem("messages", JSON.stringify(updatedMessages));
    cancelDelete();
    closeDetail();
}

function replyViaEmail() {
    if (!currentMessage) return;
    
    // Extract email and name from the message
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    const senderEmail = currentMessage.from.match(emailRegex);
    const senderName = currentMessage.from.replace(emailRegex, '').trim() || "Valued Customer";
    
    if (!senderEmail) {
        showNewMessagesPopup("No valid email address found for this sender");
        return;
    }
    
    // Create the email template
    const emailBody = `
Dear ${senderName},

Thank you for your message.

Regarding your inquiry:
${currentMessage.text}

[Please type your reply here]

Best regards,
Pinsao Secretary
`.trim();
    
    // Create a mailto link
    const subject = encodeURIComponent("Re: Your Message");
    const body = encodeURIComponent(emailBody);
    const mailtoLink = `https://mail.google.com/mail/?view=cm&fs=1&to=${senderEmail[0]}&su=${subject}&body=${body}`;
    
    // Open Gmail compose window
    window.open(mailtoLink, '_blank');
}

function checkNewMessages() {
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const lastMessageCount = parseInt(localStorage.getItem("lastMessageCount")) || 0;
    
    // Check if the number of messages has increased
    if (messages.length > lastMessageCount) {
        const newMessageCount = messages.length - lastMessageCount;
        showNewMessagesPopup(newMessageCount);
        
        // Update the last message count
        localStorage.setItem("lastMessageCount", messages.length);
        
        // Reload messages to show the new ones
        loadMessages();
    }
}

// Load messages on page load
document.addEventListener('DOMContentLoaded', () => {
    // Initialize last message count
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    localStorage.setItem("lastMessageCount", messages.length);
    
    // Set up filter button event listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter) {
            btn.addEventListener('click', () => setFilter(btn.dataset.filter));
        }
    });
    
    // Set up popup OK button
    document.getElementById("popupOkButton").addEventListener("click", closeNewMessagesPopup);
    
    // Close popup when clicking outside
    window.addEventListener("click", function(event) {
        const popup = document.getElementById("newMessagesPopup");
        if (event.target === popup) {
            closeNewMessagesPopup();
        }
    });
    
    loadMessages();
    
    // Check for new messages periodically
    setInterval(checkNewMessages, 5000); // Check every 5 seconds
});