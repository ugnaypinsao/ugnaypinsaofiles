function loadArchive() {
    const archive = JSON.parse(localStorage.getItem("messageArchive")) || [];
    const archiveList = document.getElementById("archiveList");
    
    if (archive.length === 0) {
        archiveList.innerHTML = "<div class='empty-archive'><p>No archived messages.</p></div>";
        return;
    }

    archiveList.innerHTML = "";

    // Show newest first
    [...archive].reverse().forEach((msg) => {
        const archiveItem = createArchiveItem(msg);
        archiveList.appendChild(archiveItem);
    });
}

function createArchiveItem(msg) {
    const archiveItem = document.createElement("div");
    archiveItem.classList.add("archive-item");
    
    const formattedTimestamp = msg.timestamp ? new Date(msg.timestamp).toLocaleString() : "Unknown";
    const deletedAt = msg.deletedAt ? new Date(msg.deletedAt).toLocaleString() : "Unknown";

    archiveItem.innerHTML = `
        <div class="archive-item-content">
            <span><strong>From:</strong> ${msg.from}</span>
            <p><strong>Message:</strong> ${msg.text}</p>
            <small><strong>Sent:</strong> ${formattedTimestamp}</small>
            <small><strong>Deleted:</strong> ${deletedAt}</small>
            ${msg.deleteReason ? `<p><strong>Reason:</strong> ${msg.deleteReason}</p>` : ''}
        </div>
    `;
    
    return archiveItem;
}

document.addEventListener('DOMContentLoaded', loadArchive); 