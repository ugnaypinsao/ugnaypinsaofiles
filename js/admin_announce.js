let announcements = JSON.parse(localStorage.getItem('announcements')) || [];
let currentEditId = null;

// Initialize file upload display
document.addEventListener('DOMContentLoaded', function() {
    renderAnnouncements();
    
    // File upload display for create form
    const imageInput = document.getElementById('image');
    const fileInfo = document.querySelector('#announcementForm .file-info');
    
    imageInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileInfo.textContent = this.files[0].name;
        } else {
            fileInfo.textContent = 'No file selected';
        }
    });
    
    // File upload display for edit form
    const editImageInput = document.getElementById('editImage');
    const editFileInfo = document.querySelector('#editForm .file-info');
    
    editImageInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            editFileInfo.textContent = this.files[0].name;
        } else {
            editFileInfo.textContent = 'No file selected';
        }
    });
});

function addAnnouncement() {
    const title = document.getElementById('title').value;
    const what = document.getElementById('what').value;
    const where = document.getElementById('where').value;
    const when = document.getElementById('when').value;
    const imageInput = document.getElementById('image');
    const content = document.getElementById('content').value;

    if (!title || !what || !where || !when) {
        showAlert('Please fill out all required fields.', 'error');
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
    
    showAlert('Announcement published successfully!', 'success');
}

function renderAnnouncements() {
    const adminDiv = document.getElementById('adminAnnouncements');
    adminDiv.innerHTML = '';

    if (announcements.length === 0) {
        adminDiv.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <h3>No Announcements Yet</h3>
                <p>Create your first announcement to get started.</p>
            </div>
        `;
        return;
    }

    announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

    announcements.forEach(announcement => {
        const formattedDate = new Date(announcement.date).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const eventDate = new Date(announcement.when).toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const adminCard = document.createElement('div');
        adminCard.className = 'announcement';
        
        adminCard.innerHTML = `
            ${announcement.image ? `
                <div class="announcement-image-container">
                    <img src="${announcement.image}" alt="${announcement.title}" class="announcement-image">
                </div>
            ` : ''}
            
            <div class="announcement-content">
                <h3 class="announcement-title">${announcement.title}</h3>
                
                <div class="announcement-meta">
                    <div class="announcement-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>${eventDate}</span>
                    </div>
                    <div class="announcement-meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${announcement.where}</span>
                    </div>
                </div>
                
                ${announcement.content ? `
                    <div class="announcement-details">
                        <p>${announcement.content}</p>
                    </div>
                ` : ''}
                
                <div class="announcement-footer">
                    <small>Posted: ${formattedDate}</small>
                    <div class="announcement-actions">
                        <button class="action-btn edit" onclick="editAnnouncement(${announcement.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="deleteAnnouncement(${announcement.id})" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
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
    
    // Format the datetime for the edit form
    const whenDate = new Date(announcement.when);
    const formattedWhen = whenDate.toISOString().slice(0, 16);
    document.getElementById('editWhen').value = formattedWhen;
    
    document.getElementById('editContent').value = announcement.content || '';
    document.querySelector('#editForm .file-info').textContent = 'No file selected';
    
    document.getElementById('editModal').classList.add('show');
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
        showAlert('Please fill out all required fields.', 'error');
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
        
        showAlert('Announcement updated successfully!', 'success');
    }
}

function deleteAnnouncement(id) {
    showConfirmDialog(
        'Delete Announcement',
        'Are you sure you want to delete this announcement? This action cannot be undone.',
        'Delete',
        'Cancel',
        () => {
            announcements = announcements.filter(ann => ann.id !== id);
            saveAnnouncements();
            renderAnnouncements();
            showAlert('Announcement deleted successfully!', 'success');
        }
    );
}

function saveAnnouncements() {
    localStorage.setItem('announcements', JSON.stringify(announcements));
}

function clearForm() {
    document.getElementById('announcementForm').reset();
    document.querySelector('#announcementForm .file-info').textContent = 'No file selected';
}

function closeModal() {
    document.getElementById('editModal').classList.remove('show');
    currentEditId = null;
    document.getElementById('editImage').value = '';
}

/* UI Helper Functions */
function showAlert(message, type = 'info') {
    // In a real app, you would implement a proper alert/notification system
    alert(`${type.toUpperCase()}: ${message}`);
}

function showConfirmDialog(title, message, confirmText, cancelText, onConfirm) {
    // In a real app, you would implement a proper confirmation dialog
    if (confirm(`${title}\n\n${message}`)) {
        onConfirm();
    }
}

// Event listeners
document.querySelector('.close-btn').addEventListener('click', closeModal);
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('editModal')) {
        closeModal();
    }
});
