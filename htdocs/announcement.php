<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | Pinsao Proper</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/res_announce.css">
    <style>
        /* Grid system for footer */
        .grid {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .grid-pad {
            padding: 20px 15px 0 15px;
        }

        .col-1-2 {
            width: 50%;
            padding: 0 15px;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .col-1-2 {
                width: 100%;
            }
        }

        /* Image modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            overflow: auto;
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 90%;
            max-width: 1200px;
            max-height: 90vh;
            object-fit: contain;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* Search bar styles */
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .search-bar {
            width: 100%;
            max-width: 600px;
            display: flex;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .search-bar input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            outline: none;
            font-size: 1rem;
        }

        .search-bar button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-bar button:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-bullhorn"></i>
                <span>Pinsao Proper</span>
            </div>
        </div>
    </header>

    <main class="content-wrapper">
        <div class="page-header">
            <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
        </div>

        <div class="search-container">
            <div class="search-bar">
                <input type="text" placeholder="Search announcements..." id="searchInput">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="announcements-container">
            <div id="viewerAnnouncements">
                <!-- Announcements will be dynamically loaded here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="wrap">
        <div class="grid grid-pad">
            <div class="col-1-2">
                <div class="content">
                    <div class="footer-widget">
                        <h3>About</h3>
                        <div class="textwidget">
                            <p>This website is designed to highlight the barangay officials, showcase the vision,
                                mission, and core values of Pinsao Proper, and serve as a platform for its residents.
                            </p><br>
                            <p>&copy; 2024 Pinsao Proper. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1-2">
                <div class="content">
                    <div class="footer-widget">
                        <h3>More info</h3>
                        <div class="textwidget">
                            <p>Pinsao Proper, Baguio City, Benguet, Cordillera Administrative Region (CAR), Philippines.</p>
                            <br>
                            <p>ZIP CODE: 2600</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer -->

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="expandedImg">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            renderAnnouncements();
        });

        let allAnnouncements = []; // This will store all the announcements data

        async function renderAnnouncements() {
            try {
                const response = await axios.get('php/announcement_api.php');
                allAnnouncements = response.data; // Store the fetched data

                const announcementsContainer = document.getElementById('viewerAnnouncements');
                announcementsContainer.innerHTML = '';

                if (!allAnnouncements.length) {
                    announcementsContainer.innerHTML = `
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-bullhorn fa-2x mb-2"></i>
                    <p>No announcements yet.</p>
                </div>`;
                    return;
                }

                allAnnouncements.forEach(announcement => {
                    if (announcement.status === 'deleted') {
                        return;
                    }

                    const card = document.createElement('div');
                    card.className = 'announcement';

                    const eventDate = new Date(announcement.when_).toLocaleString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const postDate = new Date(announcement.created_at).toLocaleString();
                    var imageUrl = announcement.image_path || 'https://static.vecteezy.com/system/resources/previews/001/760/457/non_2x/megaphone-loudspeaker-making-announcement-vector.jpg';

                    if (imageUrl.startsWith("../")) {
                        imageUrl = imageUrl.substring(3); // Remove the first 3 characters
                    }
                    card.innerHTML = `
                <h3>${announcement.title}</h3>
                <div class="image-container">
                    <img src="${imageUrl}" alt="Announcement" class="announcement-image" onclick="openModal('${announcement.image_path}')">
                </div>
                <div class="announcement-details">
                    <p><strong>Date:</strong> ${eventDate}</p>
                    <p><strong>Author:</strong> ${announcement.author || 'Barangay Office'}</p>
                </div>
                <div class="announcement-content">
                    ${announcement.content || 'No content available.'}
                </div>
                <div class="announcement-date">
                    <i class="far fa-calendar-alt"></i> Posted on: ${postDate}
                </div>
                `;

                    announcementsContainer.appendChild(card);
                });
            } catch (error) {
                console.error('Failed to load announcements:', error);
            }
        }

        // Search announcements dynamically
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();

            // Filter the stored announcements based on the search query
            const filteredAnnouncements = allAnnouncements.filter(announcement => {
                const title = announcement.title.toLowerCase();
                const content = announcement.content.toLowerCase();
                return title.includes(searchQuery) || content.includes(searchQuery);
            });

            // Re-render the filtered announcements
            const announcementsContainer = document.getElementById('viewerAnnouncements');
            announcementsContainer.innerHTML = ''; // Clear current content
            if (filteredAnnouncements.length === 0) {
                announcementsContainer.innerHTML = `
            <div class="p-4 text-center text-muted">
                <i class="fas fa-bullhorn fa-2x mb-2"></i>
                <p>No announcements found matching your search.</p>
            </div>`;
            } else {
                filteredAnnouncements.forEach(announcement => {


                     if (announcement.status === 'deleted') {
                        return;
                    }

                    const card = document.createElement('div');
                    card.className = 'announcement';

                    const eventDate = new Date(announcement.when_).toLocaleString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const postDate = new Date(announcement.created_at).toLocaleString();

                    var imageUrl = announcement.image_path || 'https://static.vecteezy.com/system/resources/previews/001/760/457/non_2x/megaphone-loudspeaker-making-announcement-vector.jpg';

                    if (imageUrl.startsWith("../")) {
                        imageUrl = imageUrl.substring(3); // Remove the first 3 characters
                    }

                    card.innerHTML = `
                <h3>${announcement.title}</h3>
                <div class="image-container">
                    <img src="${imageUrl}" alt="Announcement" class="announcement-image" onclick="openModal('${announcement.image_path}')">
                </div>
                <div class="announcement-details">
                    <p><strong>Date:</strong> ${eventDate}</p>
                    <p><strong>Author:</strong> ${announcement.author || 'Barangay Office'}</p>
                </div>
                <div class="announcement-content">
                    ${announcement.content || 'No content available.'}
                </div>
                <div class="announcement-date">
                    <i class="far fa-calendar-alt"></i> Posted on: ${postDate}
                </div>
                `;

                    announcementsContainer.appendChild(card);
                });
            }
        });

        // Image Modal
        function openModal(imagePath) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('expandedImg');
            modal.style.display = 'block';
            modalImage.src = imagePath;
        }

        document.querySelector('.close').onclick = function() {
            document.getElementById('imageModal').style.display = 'none';
        };
    </script>

</body>

</html>