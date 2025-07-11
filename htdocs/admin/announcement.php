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
    <link rel="stylesheet" href="../assets/css/admin_announce.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <a href="index.php" class="text-decoration-none text-dark">
            <h1 class="page-title">Announcements Management</h1>
        </a>
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
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter announcement title" required>
                </div>
                <div class="form-group">
                    <label for="when" class="form-label">Event Date & Time *</label>
                    <input type="datetime-local" id="when" name="when" class="form-control" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="what" class="form-label">What *</label>
                    <input type="text" id="what" class="form-control" name="what" placeholder="Enter what's happening" required>
                </div>
                <div class="form-group">
                    <label for="where" class="form-label">Where *</label>
                    <input type="text" id="where" class="form-control" name="where" placeholder="Enter location" required>
                </div>
            </div>
            <div class="form-group">
                <label for="image" class="form-label">Featured Image</label>
                <input type="file" id="image" name="image" class="form-control form-control-file" accept="image/*">
                <p class="text-xs text-muted mt-1">Recommended size: 1200x630 pixels</p>
            </div>
            <div class="form-group">
                <label for="content" class="form-label">Additional Details</label>
                <textarea id="content" name="content" class="form-control" rows="5" placeholder="Enter additional details"></textarea>
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
                            <input type="text" id="editTitle" name="editTitle" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editWhen" class="form-label">Event Date & Time *</label>
                            <input type="datetime-local" id="editWhen" name="editWhen" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="editWhat" class="form-label">What *</label>
                            <input type="text" id="editWhat" name="editWhat" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editWhere" class="form-label">Where *</label>
                            <input type="text" id="editWhere" name="editWhere" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editContent" class="form-label">Additional Details</label>
                        <textarea id="editContent" name="editContent" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editImage" class="form-label">Update Featured Image</label>
                        <input type="file" id="editImage" name="editImage" class="form-control form-control-file" accept="image/*">
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let announcements = JSON.parse(localStorage.getItem('announcements')) || [];
        let currentEditId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderAnnouncements();
            updateAnnouncementCount();
        });

        async function addAnnouncement() {
            const title = document.getElementById('title').value;
            const what = document.getElementById('what').value;
            const where = document.getElementById('where').value;
            const when = document.getElementById('when').value;
            const content = document.getElementById('content').value;
            const imageFile = document.getElementById('image').files[0];

            if (!title || !what || !where || !when) {
                showToast('Please fill all required fields.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'create'); // Action type to create
            formData.append('title', title);
            formData.append('what', what);
            formData.append('where', where);
            formData.append('when', when);
            formData.append('content', content);

            if (imageFile) {
                formData.append('image', imageFile);
            }

            try {
                await axios.post('../php/announcement_api.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                showToast('Announcement added successfully!');
                renderAnnouncements();
                document.getElementById('announcementForm').reset();
            } catch (err) {
                showToast('Failed to add announcement.', 'danger');
            }
        }


        async function renderAnnouncements() {
            try {
                const response = await axios.get('../php/announcement_api.php');
                announcements = response.data;

                const adminDiv = document.getElementById('adminAnnouncements');
                adminDiv.innerHTML = '';

                if (!announcements.length) {
                    adminDiv.innerHTML = `
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-bullhorn fa-2x mb-2"></i>
                    <p>No announcements yet. Create your first announcement!</p>
                </div>`;
                    return;
                }
                var length = 0;
                announcements.forEach(announcement => {
                    if (announcement.status == 'deleted') {
                        return;
                    }


                    const card = document.createElement('div');
                    card.className = 'announcement-card';

                    const eventDate = new Date(announcement.when_).toLocaleString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const postDate = new Date(announcement.created_at).toLocaleString();

                    card.innerHTML = `
                <div class="announcement-header">
                    <div>
                        <h3>${announcement.title}</h3>
                        <div><i class="fas fa-calendar-alt"></i> ${eventDate}</div>
                        <div><i class="fas fa-map-marker-alt"></i> ${announcement.where_}</div>
                    </div>
                    <div class="announcement-actions">
                        <button onclick="editAnnouncement(${announcement.id})" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteAnnouncement(${announcement.id})" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                ${announcement.image_path ? `<img src="${announcement.image_path}" class="announcement-image">` : ''}
                <div>${announcement.content || 'No additional details'}</div>
                <div class="text-xs mt-2"><i class="fas fa-clock"></i> Posted on ${postDate}</div>
            `;

                    adminDiv.appendChild(card);

                    length+=1;
                });

                updateAnnouncementCount(length);
            } catch (error) {
                showToast('Failed to fetch announcements', 'danger');
            }
        }


        async function editAnnouncement(id) {
            const announcement = announcements.find(ann => ann.id === id);
            if (!announcement) return;

            currentEditId = id;

            document.getElementById('editTitle').value = announcement.title;
            document.getElementById('editWhat').value = announcement.what;
            document.getElementById('editWhere').value = announcement.where_;
            document.getElementById('editWhen').value = announcement.when;
            document.getElementById('editContent').value = announcement.content || '';
            document.getElementById('editImage').value = '';

            document.getElementById('editModal').classList.add('active');
        }


        async function saveEditedAnnouncement() {
            if (currentEditId === null) return;

            const title = document.getElementById('editTitle').value;
            const what = document.getElementById('editWhat').value;
            const where = document.getElementById('editWhere').value;
            const when = document.getElementById('editWhen').value;
            const content = document.getElementById('editContent').value;
            const imageFile = document.getElementById('editImage').files[0];

            if (!title || !what || !where || !when) {
                showToast('Please fill out all required fields.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'edit'); // Action type to edit
            formData.append('id', currentEditId);
            formData.append('title', title);
            formData.append('what', what);
            formData.append('where', where);
            formData.append('when', when);
            formData.append('content', content);

            if (imageFile) {
                formData.append('image', imageFile);
            }

            try {
                await axios.post('../php/announcement_api.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                showToast('Announcement updated successfully!', 'success');
                renderAnnouncements();
                closeModal();
            } catch (err) {
                showToast('Failed to update announcement.', 'danger');
            }
        }

        async function deleteAnnouncement(id) {
            if (!confirm('Are you sure?')) return;
            const formData = new FormData();
            formData.append('action', 'delete'); // Action type to delete
            formData.append('id', id);

            try {
                await axios.post('../php/announcement_api.php', formData);
                showToast('Deleted successfully');
                renderAnnouncements();
            } catch {
                showToast('Delete failed', 'danger');
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

        function updateAnnouncementCount(length) {
            const countElement = document.getElementById('announcementCount');
            if (countElement) {
                countElement.textContent = `Showing ${length} announcement${length !== 1 ? 's' : ''}`;
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

        function toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        }
    </script>
</body>

</html>