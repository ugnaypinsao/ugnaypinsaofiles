<?php 
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Announcements</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_announce.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 class="page-title">Announcements Management</h1>
    </div>

    <!-- Create Announcement Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Create New Announcement</h2>
        </div>
        <form id="announcementForm">
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" id="title" class="form-control" placeholder="Enter announcement title" required>
                </div>
                <div class="form-group">
                    <label for="when" class="form-label">Event Date & Time *</label>
                    <input type="datetime-local" id="when" class="form-control" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="what" class="form-label">What *</label>
                    <input type="text" id="what" class="form-control" placeholder="Enter what's happening" required>
                </div>
                <div class="form-group">
                    <label for="where" class="form-label">Where *</label>
                    <input type="text" id="where" class="form-control" placeholder="Enter location" required>
                </div>
            </div>
            <div class="form-group">
                <label for="image" class="form-label">Featured Image</label>
                <input type="file" id="image" class="form-control form-control-file" accept="image/*">
                <p class="text-xs text-muted mt-1">Recommended size: 1200x630 pixels</p>
            </div>
            <div class="form-group">
                <label for="content" class="form-label">Additional Details</label>
                <textarea id="content" class="form-control" rows="5" placeholder="Enter additional details"></textarea>
            </div>
            <button type="button" onclick="addAnnouncement()" class="btn btn-primary w-full">
                <i class="fas fa-paper-plane mr-2"></i> Publish Announcement
            </button>
        </form>
    </div>

    <!-- Current Announcements -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Current Announcements</h2>
            <span class="text-sm text-muted" id="announcementCount">Loading...</span>
        </div>
        <div id="adminAnnouncements"></div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Announcement</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="editTitle" class="form-label">Title *</label>
                            <input type="text" id="editTitle" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editWhen" class="form-label">Event Date & Time *</label>
                            <input type="datetime-local" id="editWhen" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="editWhat" class="form-label">What *</label>
                            <input type="text" id="editWhat" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editWhere" class="form-label">Where *</label>
                            <input type="text" id="editWhere" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editContent" class="form-label">Additional Details</label>
                        <textarea id="editContent" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editImage" class="form-label">Update Featured Image</label>
                        <input type="file" id="editImage" class="form-control form-control-file" accept="image/*">
                        <p class="text-xs text-muted mt-1">Leave blank to keep current image</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-outline">
                    Cancel
                </button>
                <button type="button" onclick="saveEditedAnnouncement()" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
        let announcements = JSON.parse(localStorage.getItem('announcements')) || [];
        let currentEditId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderAnnouncements();
            updateAnnouncementCount();
        });

        function addAnnouncement() {
            const title = document.getElementById('title').value;
            const what = document.getElementById('what').value;
            const where = document.getElementById('where').value;
            const when = document.getElementById('when').value;
            const imageInput = document.getElementById('image');
            const content = document.getElementById('content').value;

            if (!title || !what || !where || !when) {
                showToast('Please fill out all required fields.', 'warning');
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
            updateAnnouncementCount();
            
            showToast('Announcement published successfully!', 'success');
        }

        function renderAnnouncements() {
            const adminDiv = document.getElementById('adminAnnouncements');
            adminDiv.innerHTML = '';

            if (announcements.length === 0) {
                adminDiv.innerHTML = `
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-bullhorn fa-2x mb-2"></i>
                        <p>No announcements yet. Create your first announcement!</p>
                    </div>
                `;
                return;
            }

            announcements.sort((a, b) => new Date(b.date) - new Date(a.date));

            announcements.forEach(announcement => {
                const adminCard = document.createElement('div');
                adminCard.className = 'announcement-card';

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

                adminCard.innerHTML = `
                    <div class="announcement-header">
                        <div>
                            <h3 class="announcement-title">${announcement.title}</h3>
                            <div class="announcement-meta">
                                <span><i class="fas fa-calendar-alt mr-1"></i> ${eventDate}</span>
                                <span><i class="fas fa-map-marker-alt mr-1"></i> ${announcement.where}</span>
                            </div>
                        </div>
                        <div class="announcement-actions">
                            <button onclick="editAnnouncement(${announcement.id})" class="btn btn-warning btn-sm btn-icon">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteAnnouncement(${announcement.id})" class="btn btn-danger btn-sm btn-icon">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    ${announcement.image ? `
                        <img src="${announcement.image}" alt="Announcement Image" class="announcement-image">
                    ` : ''}
                    <div class="announcement-content">${announcement.content || '<span class="text-muted">No additional details provided</span>'}</div>
                    <div class="announcement-footer">
                        <span class="text-xs"><i class="fas fa-clock mr-1"></i> Posted on ${formattedDate}</span>
                        <span class="text-xs"><i class="fas fa-tag mr-1"></i> ${announcement.what}</span>
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
            document.getElementById('editWhen').value = announcement.when;
            document.getElementById('editContent').value = announcement.content || '';
            document.getElementById('editImage').value = '';
            
            document.getElementById('editModal').classList.add('active');
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
                showToast('Please fill out all required fields.', 'warning');
                return;
            }

            const announcement = announcements.find(ann => ann.id === currentEditId);
            if (announcement) {
                announcement.title = title;
                announcement.what = what;
                announcement.where = where;
                announcement.when = when;
                announcement.content = content;
                
                if (imageInput.files[0]) {
                    announcement.image = URL.createObjectURL(imageInput.files[0]);
                }
                
                saveAnnouncements();
                renderAnnouncements();
                closeModal();
                
                showToast('Announcement updated successfully!', 'success');
            }
        }

        function deleteAnnouncement(id) {
            if (confirm("Are you sure you want to delete this announcement? This action cannot be undone.")) {
                announcements = announcements.filter(ann => ann.id !== id);
                saveAnnouncements();
                renderAnnouncements();
                updateAnnouncementCount();
                
                showToast('Announcement deleted successfully!', 'success');
            }
        }

        function saveAnnouncements() {
            localStorage.setItem('announcements', JSON.stringify(announcements));
        }

        function clearForm() {
            document.getElementById('announcementForm').reset();
        }

        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
            currentEditId = null;
            document.getElementById('editImage').value = '';
        }

        function updateAnnouncementCount() {
            const countElement = document.getElementById('announcementCount');
            if (countElement) {
                countElement.textContent = `Showing ${announcements.length} announcement${announcements.length !== 1 ? 's' : ''}`;
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-message">${message}</div>
                <button class="toast-close">&times;</button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
            
            toast.querySelector('.toast-close').addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            });
        }

        // Event listeners
        document.querySelector('.modal-close').addEventListener('click', closeModal);
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('editModal')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
