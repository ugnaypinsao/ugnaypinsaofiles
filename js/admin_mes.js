let currentMessage = null; // Store the currently viewed message
let currentFilter = 'all'; // Store the current filter
let isViewingArchive = false; // Track if we're viewing archive

function loadMessages() {
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const messageList = document.getElementById("messageList");
    const messageDetail = document.getElementById("messageDetail");

    // Hide detail view by default
    messageDetail.style.display = "none";

    if (isViewingArchive) {
        loadArchiveMessages();
        return;
    }

    if (messages.length === 0) {
        messageList.innerHTML = "<div class='empty-inbox'><p>No messages yet.</p></div>";
        return;
    }

    messageList.innerHTML = ""; // Clear existing messages

    // Show messages in chronological order (newest first)
    const reversedMessages = [...messages].reverse();

    let filteredMessages = reversedMessages;
    if (currentFilter !== 'all') {
        filteredMessages = reversedMessages.filter(msg => msg.status === currentFilter);
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

function loadArchiveMessages() {
    const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
    const archiveList = document.getElementById("archiveList");
    
    if (archive.length === 0) {
        archiveList.innerHTML = "<div class='empty-inbox'><p>No archived messages.</p></div>";
        return;
    }

    archiveList.innerHTML = ""; // Clear existing archive items

    // Show archive in chronological order (newest first)
    const reversedArchive = [...archive].reverse();

    reversedArchive.forEach((msg, displayIndex) => {
        const originalIndex = archive.length - 1 - displayIndex;
        const archiveItem = createArchiveItem(msg, originalIndex);
        archiveList.appendChild(archiveItem);
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

function createArchiveItem(msg, originalIndex) {
    const archiveItem = document.createElement("div");
    archiveItem.classList.add("archive-item");
    
    // Format the timestamp
    const formattedTimestamp = msg.timestamp
        ? new Date(msg.timestamp).toLocaleString()
        : "Unknown Date";

    const deletedAt = msg.deletedAt ? new Date(msg.deletedAt).toLocaleString() : "Unknown";

    archiveItem.innerHTML = `
        <div class="archive-item-content">
            <span><strong>From:</strong> ${msg.from}</span>
            <p><strong>Message:</strong> ${msg.text}</p>
            <small><strong>Sent:</strong> ${formattedTimestamp}</small>
            <small><strong>Deleted:</strong> ${deletedAt}</small>
        </div>
        <div class="archive-item-actions">
            <button class="restore-btn" onclick="restoreMessage(event, ${originalIndex})">Restore</button>
            <button class="delete-permanently-btn" onclick="deletePermanently(event, ${originalIndex})">Delete Permanently</button>
        </div>
    `;
    
    return archiveItem;
}

function openArchiveModal() {
    isViewingArchive = true;
    document.getElementById("archiveModal").style.display = "block";
    loadArchiveMessages();
}

function closeArchiveModal() {
    isViewingArchive = false;
    document.getElementById("archiveModal").style.display = "none";
    loadMessages();
}

function restoreMessage(event, originalIndex) {
    event.stopPropagation();
    
    const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
    if (originalIndex >= 0 && originalIndex < archive.length) {
        const messageToRestore = archive[originalIndex];
        
        // Add back to messages
        const messages = JSON.parse(localStorage.getItem("messages")) || [];
        messages.push(messageToRestore);
        localStorage.setItem("messages", JSON.stringify(messages));
        
        // Remove from archive
        archive.splice(originalIndex, 1);
        localStorage.setItem("messageArchive", JSON.stringify(archive));
        
        // Reload both views
        loadArchiveMessages();
        if (!isViewingArchive) {
            loadMessages();
        }
        
        // Show success message
        alert("Message restored successfully!");
    }
}

function deletePermanently(event, originalIndex) {
    event.stopPropagation();
    
    if (confirm("Are you sure you want to permanently delete this message? This cannot be undone.")) {
        const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
        if (originalIndex >= 0 && originalIndex < archive.length) {
            archive.splice(originalIndex, 1);
            localStorage.setItem("messageArchive", JSON.stringify(archive));
            loadArchiveMessages();
            alert("Message permanently deleted!");
        }
    }
}

// ... (rest of the functions remain exactly the same as in the previous version) ...

function setFilter(filter) {
    isViewingArchive = false;
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
        messages[messageIndex].read = true; // Mark the message as read
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
        : "Unknown Date"; // Placeholder if timestamp is missing or invalid

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

function deleteMessage() {
    if (!currentMessage) return;

    if (confirm("Are you sure you want to delete this message? It will be moved to archive.")) {
        const messages = JSON.parse(localStorage.getItem("messages")) || [];
        const updatedMessages = messages.filter(
            m => m.text !== currentMessage.text || m.from !== currentMessage.from
        );
        
        // Add to archive
        const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
        currentMessage.deletedAt = new Date().toISOString();
        archive.push(currentMessage);
        localStorage.setItem("messageArchive", JSON.stringify(archive));
        
        // Update messages
        localStorage.setItem("messages", JSON.stringify(updatedMessages));
        closeDetail(); // Return to the inbox after deletion
    }
}

function replyViaEmail() {
    if (!currentMessage) return;
    
    // Extract email and name from the message
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    const senderEmail = currentMessage.from.match(emailRegex);
    const senderName = currentMessage.from.replace(emailRegex, '').trim() || "Valued Customer";
    
    if (!senderEmail) {
        alert("No valid email address found for this sender.");
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

// Load messages on page load
document.addEventListener('DOMContentLoaded', () => {
    // Set up filter button event listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter) {
            btn.addEventListener('click', () => setFilter(btn.dataset.filter));
        }
    });
    
    // Set up archive button listener
    document.getElementById("viewArchiveBtn").addEventListener('click', openArchiveModal);
    
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