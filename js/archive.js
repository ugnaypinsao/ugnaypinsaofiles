function loadArchive() {
    const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
    const archiveList = document.getElementById("archiveList");
    const noResults = document.getElementById("noResults");
    const searchInput = document.getElementById("searchInput");
    
    function displayMessages(messages) {
        archiveList.innerHTML = "";
        
        if (messages.length === 0) {
            noResults.style.display = "block";
            archiveList.style.display = "none";
            return;
        }
        
        noResults.style.display = "none";
        archiveList.style.display = "grid";
        
        // Show newest first
        [...messages].reverse().forEach((msg, index) => {
            const archiveItem = createArchiveItem(msg, index);
            archiveList.appendChild(archiveItem);
        });
    }

    // Initial load
    if (archive.length === 0) {
        archiveList.innerHTML = `
            <div class='empty-archive'>
                <i class="fas fa-inbox" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 1rem;"></i>
                <p>No archived messages found.</p>
            </div>`;
        noResults.style.display = "none";
        return;
    }

    displayMessages(archive);

    // Search functionality
    searchInput.addEventListener("input", (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredMessages = archive.filter(msg => 
            msg.from && msg.from.toLowerCase().includes(searchTerm)
        );
        displayMessages(filteredMessages);
    });
}

function createArchiveItem(msg, index) {
    const archiveItem = document.createElement("div");
    archiveItem.classList.add("archive-item");
    
    // Add different border colors based on index for visual variety
    const borderColors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];
    const borderColor = borderColors[index % borderColors.length];
    archiveItem.style.borderLeftColor = borderColor;

    const formattedTimestamp = msg.timestamp ? new Date(msg.timestamp).toLocaleString() : "Unknown";
    const deletedAt = msg.deletedAt ? new Date(msg.deletedAt).toLocaleString() : "Unknown";

    archiveItem.innerHTML = `
        <div class="archive-item-content">
            <span><i class="fas fa-user" style="color: ${borderColor};"></i> <strong>From:</strong> ${msg.from || 'Unknown'}</span>
            <p><i class="fas fa-comment" style="color: ${borderColor};"></i> <strong>Message:</strong> ${msg.text || 'No content'}</p>
            ${msg.deleteReason ? `<p><i class="fas fa-exclamation-triangle" style="color: ${borderColor};"></i> <strong>Reason:</strong> ${msg.deleteReason}</p>` : ''}
            <small><i class="fas fa-paper-plane"></i> <strong>Sent:</strong> ${formattedTimestamp}</small>
            <small><i class="fas fa-trash-alt"></i> <strong>Deleted:</strong> ${deletedAt}</small>
        </div>
    `;
    
    return archiveItem;
}

document.addEventListener('DOMContentLoaded', loadArchive);