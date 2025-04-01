let announcements = JSON.parse(localStorage.getItem('announcements')) || [];
let currentEditId = null;

function addAnnouncement() {
    const title = document.getElementById('title').value;
    const what = document.getElementById('what').value;
    const where = document.getElementById('where').value;
    const when = document.getElementById('when').value;
    const imageInput = document.getElementById('image');
    const content = document.getElementById('content').value;

    if (!title || !what || !where || !when) {
        alert("Please fill out all required fields.");
        return;
    }

    const image = imageInput.files[0];
    const imageUrl = image ? URL.createObjectURL(image) : null;

    const announcement = {
        id: Date.now(),
        title,
        what,
        where,
        when,
        content,
        image: imageUrl,
        date: new Date().toISOString()
    };

    announcements.push(announcement);
    saveAnnouncements();
    renderAnnouncements();
    clearForm();
}

function renderAnnouncements() {
    const adminDiv = document.getElementById('adminAnnouncements');
    adminDiv.innerHTML = '';

    announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

    announcements.forEach(announcement => {
        const adminCard = document.createElement('div');
        adminCard.className = 'announcement';

        const formattedDate = new Date(announcement.date).toLocaleString();
        const eventDate = new Date(announcement.when).toLocaleString();

        adminCard.innerHTML = `
            ${announcement.image ? `<img src="${announcement.image}" alt="Announcement Image" style="max-width: 100%; border-radius: 5px; margin-bottom: 15px;">` : ''}
            <h3>${announcement.title}</h3>
            <div class="announcement-details">
                <p><strong>What:</strong> ${announcement.what}</p>
                <p><strong>Where:</strong> ${announcement.where}</p>
                <p><strong>When:</strong> ${eventDate}</p>
            </div>
            ${announcement.content ? `<div class="content-container"></div>` : ''}
            <small>Posted on: ${formattedDate}</small>
            <div class="admin-actions">
                <button onclick="editAnnouncement(${announcement.id})">Edit</button>
                <button onclick="deleteAnnouncement(${announcement.id})">Delete</button>
            </div>
        `;
        
        if (announcement.content) {
            const contentContainer = adminCard.querySelector('.content-container');
            contentContainer.textContent = announcement.content;
        }
        
        adminDiv.appendChild(adminCard);
    });
}

function editAnnouncement(id) {
    const announcement = announcements.find(ann => ann.id === id);
    if (!announcement) return;

    currentEditId = id;
    
    document.getElementById('editTitle').value = announcement.title;
    document.getElementById('editWhat').value = announcement.what;
    document.getElementById('editWhere').value = announcement.where;
    document.getElementById('editWhen').value = announcement.when;
    document.getElementById('editContent').value = announcement.content || '';
    document.getElementById('editImage').value = '';
    
    document.getElementById('editModal').style.display = 'flex';
}

function saveEditedAnnouncement() {
    if (currentEditId === null) return;

    const title = document.getElementById('editTitle').value;
    const what = document.getElementById('editWhat').value;
    const where = document.getElementById('editWhere').value;
    const when = document.getElementById('editWhen').value;
    const content = document.getElementById('editContent').value;
    const imageInput = document.getElementById('editImage');

    if (!title || !what || !where || !when) {
        alert("Please fill out all required fields.");
        return;
    }

    const announcement = announcements.find(ann => ann.id === currentEditId);
    if (announcement) {
        announcement.title = title;
        announcement.what = what;
        announcement.where = where;
        announcement.when = when;
        announcement.content = content;
        
        // Only update image if a new one was selected
        if (imageInput.files[0]) {
            announcement.image = URL.createObjectURL(imageInput.files[0]);
        }
        
        saveAnnouncements();
        renderAnnouncements();
        closeModal();
    }
}

function deleteAnnouncement(id) {
    if (confirm("Are you sure you want to delete this announcement?")) {
        announcements = announcements.filter(ann => ann.id !== id);
        saveAnnouncements();
        renderAnnouncements();
    }
}

function saveAnnouncements() {
    localStorage.setItem('announcements', JSON.stringify(announcements));
}

function clearForm() {
    document.getElementById('title').value = '';
    document.getElementById('what').value = '';
    document.getElementById('where').value = '';
    document.getElementById('when').value = '';
    document.getElementById('image').value = '';
    document.getElementById('content').value = '';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    currentEditId = null;
    document.getElementById('editImage').value = '';
}

// Event listeners
document.querySelector('.close-btn').addEventListener('click', closeModal);
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('editModal')) {
        closeModal();
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', renderAnnouncements);