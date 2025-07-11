function renderAnnouncements() {
    const announcements = JSON.parse(localStorage.getItem('announcements')) || [];
    const viewerDiv = document.getElementById('viewerAnnouncements');
    viewerDiv.innerHTML = '';

    announcements.forEach(announcement => {
        const viewerCard = document.createElement('div');
        viewerCard.className = 'announcement';

        viewerCard.innerHTML = `
                ${announcement.image ? `<img src="${announcement.image}" alt="Announcement Image">` : ''}
                <h3>${announcement.title}</h3>
                <p>${announcement.content}</p>
            `;
        viewerDiv.appendChild(viewerCard);
    });
}

document.addEventListener('DOMContentLoaded', renderAnnouncements);
