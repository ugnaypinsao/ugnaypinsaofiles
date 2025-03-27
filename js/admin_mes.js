let currentMessage = null; // Store the currently viewed message

function loadMessages() {
    const messages = JSON.parse(localStorage.getItem("messages")) || [];
    const messageList = document.getElementById("messageList");
    const messageDetail = document.getElementById("messageDetail");

    // Hide detail view by default
    messageDetail.style.display = "none";

    if (messages.length === 0) {
        messageList.innerHTML = "<p style='text-align: center;'>No messages yet.</p>";
        return;
    }

    messageList.innerHTML = ""; // Clear existing messages

    // Reverse the messages array to show the last message first
    const reversedMessages = messages.reverse();

    reversedMessages.forEach((msg, index) => {
        const messageItem = document.createElement("div");
        messageItem.classList.add("message-item");

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
        `;
        messageItem.addEventListener("click", () => {
            markAsRead(msg); // Mark the message as read when clicked
            showDetail(msg);
        });
        messageList.appendChild(messageItem);
    });
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

    if (confirm("Are you sure you want to delete this message?")) {
        const messages = JSON.parse(localStorage.getItem("messages")) || [];
        const updatedMessages = messages.filter(
            m => m.text !== currentMessage.text || m.from !== currentMessage.from
        );
        localStorage.setItem("messages", JSON.stringify(updatedMessages));
        closeDetail(); // Return to the inbox after deletion
    }
}

// Load messages on page load
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
