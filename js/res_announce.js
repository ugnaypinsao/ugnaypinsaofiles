function renderAnnouncements() {
    const announcements = JSON.parse(localStorage.getItem('announcements')) || [];
    const viewerDiv = document.getElementById('viewerAnnouncements');
    viewerDiv.innerHTML = '';

    // Sort announcements by date (most recent first)
    announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

    announcements.forEach(announcement => {
        const viewerCard = document.createElement('div');
        viewerCard.className = 'announcement';

        const formattedDate = new Date(announcement.date).toLocaleString(); // Format date and time

        viewerCard.innerHTML = `
            ${announcement.image ? `<img src="${announcement.image}" alt="Announcement Image">` : ''}
            <h3>${announcement.title}</h3>
            <p>${announcement.content}</p>
            <small>Posted on: ${formattedDate}</small>
        `;
        viewerDiv.appendChild(viewerCard);
    });
}

document.addEventListener('DOMContentLoaded', renderAnnouncements);
