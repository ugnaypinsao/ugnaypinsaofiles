// Announcement Viewer with Search Functionality
function renderAnnouncements(filterText = '') {
    const announcements = JSON.parse(localStorage.getItem('announcements')) || [];
    const viewerDiv = document.getElementById('viewerAnnouncements');
    viewerDiv.innerHTML = '';

    // Sort announcements by date (most recent first)
    announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

    // Filter announcements based on search text
    const filteredAnnouncements = announcements.filter(announcement => {
        if (!filterText) return true;
        
        const searchText = filterText.toLowerCase();
        return (
            announcement.title.toLowerCase().includes(searchText) ||
            announcement.what.toLowerCase().includes(searchText) ||
            announcement.where.toLowerCase().includes(searchText) ||
            announcement.content.toLowerCase().includes(searchText) ||
            announcement.date.toLowerCase().includes(searchText) ||
            announcement.when.toLowerCase().includes(searchText)
        );
    });

    if (filteredAnnouncements.length === 0) {
        viewerDiv.innerHTML = '<div class="no-results">No announcements found matching your search.</div>';
        return;
    }

    filteredAnnouncements.forEach(announcement => {
        const viewerCard = document.createElement('div');
        viewerCard.className = 'announcement';

        const formattedDate = new Date(announcement.date).toLocaleString();
        const eventDate = new Date(announcement.when).toLocaleString();

        viewerCard.innerHTML = `
            <div class="image-container">
                ${announcement.image ? `<img src="${announcement.image}" alt="Announcement Image" class="announcement-image">` : ''}
            </div>
            <h3>${announcement.title}</h3>
            <div class="announcement-details">
                <p><strong>What:</strong> ${announcement.what}</p>
                <p><strong>Where:</strong> ${announcement.where}</p>
                <p><strong>When:</strong> ${eventDate}</p>
            </div>
            ${announcement.content ? `<div class="announcement-content">${announcement.content}</div>` : ''}
            <div class="announcement-date">
                <i class="far fa-calendar-alt"></i> Posted on: ${formattedDate}
            </div>
        `;
        viewerDiv.appendChild(viewerCard);
    });

    // Initialize image modal functionality for the rendered announcements
    initImageModal();
}

function initImageModal() {
    // Get the modal
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("expandedImg");
    const closeBtn = document.getElementsByClassName("close")[0];
    
    // When any image in announcements is clicked
    document.querySelectorAll('.announcement-image').forEach(img => {
        img.addEventListener('click', function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            document.body.style.overflow = "hidden";
        });
    });
    
    // When the user clicks on (x), close the modal
    closeBtn.onclick = function() { 
        modal.style.display = "none";
        document.body.style.overflow = "auto";
    }
    
    // When the user clicks anywhere outside the image, close the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }
}

function initSearch() {
    const searchInput = document.querySelector('.search-bar input');
    const searchButton = document.querySelector('.search-bar button');
    
    // Search on button click
    searchButton.addEventListener('click', function() {
        renderAnnouncements(searchInput.value.trim());
    });
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            renderAnnouncements(searchInput.value.trim());
        }
    });
    
    // Clear search when input is empty
    searchInput.addEventListener('input', function() {
        if (this.value.trim() === '') {
            renderAnnouncements();
        }
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    renderAnnouncements();
    initSearch();
});