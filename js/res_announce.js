function renderAnnouncements() {
    const announcements = JSON.parse(localStorage.getItem('announcements')) || [];
    const viewerDiv = document.getElementById('viewerAnnouncements');
    viewerDiv.innerHTML = '';

    // Sort announcements by date (most recent first)
    announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

    announcements.forEach(announcement => {
        const viewerCard = document.createElement('div');
        viewerCard.className = 'announcement';

        const formattedDate = new Date(announcement.date).toLocaleString();
        const eventDate = new Date(announcement.when).toLocaleString();

        viewerCard.innerHTML = `
            ${announcement.image ? `<img src="${announcement.image}" alt="Announcement Image">` : ''}
            <h3>${announcement.title}</h3>
            <div class="announcement-details">
                <p><strong>What:</strong> ${announcement.what}</p>
                <p><strong>Where:</strong> ${announcement.where}</p>
                <p><strong>When:</strong> ${eventDate}</p>
            </div>
            ${announcement.content ? `<div class="announcement-content">${announcement.content}</div>` : ''}
            <div class="announcement-date">Posted on: ${formattedDate}</div>
        `;
        viewerDiv.appendChild(viewerCard);
    });
}

document.addEventListener('DOMContentLoaded', renderAnnouncements);
