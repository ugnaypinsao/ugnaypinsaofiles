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
    <title>Business Directory Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_business.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <h1>
            <a href="index.php">Business Directory Admin</a>
        </h1>
        <nav>
            <button hidden id="logout-btn"></button>
        </nav>
    </header>

    <main>
        <div class="admin-controls">
            <button id="add-business-btn">Add New Business</button>
        </div>

        <div id="admin-business-list" class="business-grid"></div>

        <div id="business-modal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2 id="modal-title">Add New Business</h2>
                <form id="business-form" enctype="multipart/form-data">
                    <input type="hidden" name="business-id" id="business-id">
                    <div class="form-group">
                        <label for="ownerName">Business Owner Name *</label>
                        <input type="text" id="ownerName" name="ownerName" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Business Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="all">All Categories</option>
                            <option value="eatery">Eatery</option>
                            <option value="retail">Retail</option>
                            <option value="cafe">Cafe/Coffee Shop</option>
                            <option value="catering">Catering</option>
                            <option value="convenience">Convenience Store</option>
                            <option value="eloading">E-loading</option>
                            <option value="dealer">Dealer</option>
                            <option value="minimart">Minimart</option>
                            <option value="water_delivery">Water Delivery</option>
                            <option value="water_refilling">Water Refilling</option>
                            <option value="messengerial">Messengerial</option>
                            <option value="aggregates">Aggregates</option>
                            <option value="auto_detailing">Auto Detailing</option>
                            <option value="tailoring">Tailoring</option>
                            <option value="laundry">Laundry</option>
                            <option value="woodwork">Woodwork</option>
                            <option value="printing">Printing and Publication</option>
                            <option value="construction">Construction Services</option>
                            <option value="lpg">Distributor of LPG</option>
                            <option value="internet">Internet</option>
                            <option value="concrete">Installation of Concrete</option>
                            <option value="manufacture">Manufacture</option>
                            <option value="credit_coop">Credit Cooperative</option>
                            <option value="buy_sell">Buy and Sell</option>
                            <option value="bookkeeping">Book Keeping</option>
                            <option value="english_tutor">English Tutor</option>
                            <option value="pvc">Installation of PVC</option>
                            <option value="gasoline">Gasoline</option>
                            <option value="architectural">Architectural Design</option>
                            <option value="drygoods">Wholesale of Drygoods</option>
                            <option value="art_galleries">Art Galleries</option>
                            <option value="apartment">Apartment</option>
                            <option value="salon">Salon</option>
                            <option value="transient">Transient</option>
                            <option value="boarding_house">Boarding House</option>
                            <option value="real_estate">Real Estate</option>
                            <option value="sub_lessor">Sub Lessor</option>
                            <option value="hotel_motel">Hotel/Motel</option>
                            <option value="boarding">Boarding</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="hours">Operating Hours</label>
                        <input type="text" id="hours" name="hours" placeholder="e.g. 9AM-5PM Mon-Fri">
                    </div>
                    <div class="form-group">
                        <label for="image">Business Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <div id="image-preview-container" style="display: none;">
                            <img style="width: 100%;" id="image-preview" src="#" alt="Image Preview">
                            <button type="button" id="remove-image-btn">Remove Image</button>
                        </div>
                    </div>
                    <button type="submit" id="save-business">Save Business</button>
                </form>

            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('add-business-btn').addEventListener('click', function() {
            document.getElementById('business-modal').style.display = 'block';
        });

        // Submit business form
        document.getElementById('business-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = document.getElementById('business-form');
            const formData = new FormData(form);
            const businessId = document.getElementById('business-id').value;

            const imageInput = document.getElementById('image');
            if (imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }
            console.log(businessId)
            const url = businessId ? '../php/editBusiness.php' : '../php/addBusiness.php';

            axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        alert(response.data.message);
                        document.getElementById('business-modal').style.display = 'none';
                        form.reset();
                        document.getElementById('image-preview-container').style.display = 'none';
                        document.getElementById('image-preview').src = '#';
                        loadBusinessList();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('An error occurred. Please try again.');
                });
        });


        // Preview image
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');

            if (file) {
                preview.src = URL.createObjectURL(file);
                container.style.display = 'block';
            } else {
                preview.src = '#';
                container.style.display = 'none';
            }
        });

        // Remove image
        document.getElementById('remove-image-btn').addEventListener('click', function() {
            const imageInput = document.getElementById('image');
            imageInput.value = '';
            document.getElementById('image-preview').src = '#';
            document.getElementById('image-preview-container').style.display = 'none';
        });

        // Close modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('business-modal').style.display = 'none';
            document.getElementById('business-form').reset();
            document.getElementById('image-preview-container').style.display = 'none';
            document.getElementById('image-preview').src = '#';
        });

        function loadBusinessList() {
            axios.get('../php/getBusinesses.php')
                .then(response => {
                    const businessList = document.getElementById('admin-business-list');
                    businessList.innerHTML = '';

                    if (response.data.success) {
                        response.data.businesses.forEach(business => {
                            if (business.status !== 'deleted') { // Exclude deleted businesses
                                const businessCard = createAdminBusinessCard(business);
                                businessList.appendChild(businessCard);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading businesses:', error);
                });
        }



        function createAdminBusinessCard(business) {
            const card = document.createElement('div');
            card.className = 'business-card';

            // Format category name by replacing underscores and capitalizing
            const formattedCategory = business.category
                .replace(/_/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());

            const imageUrl = business.image || 'https://via.placeholder.com/300x200?text=No+Image';
            const phoneDisplay = business.phone || 'Not provided';
            const hoursDisplay = business.hours || 'Not specified';

            card.innerHTML = `
            <img src="${imageUrl}" alt="${business.name}" class="business-image">
            <div class="business-info">
                <h3>${business.business_name}</h3>
                <p><strong>Owner:</strong> ${business.owner_name}</p>
                <span class="category">${formattedCategory}</span>
                ${business.description ? `<p>${business.description}</p>` : ''}
                <p><i class="fas fa-map-marker-alt"></i> ${business.address}</p>
                <p class="contact"><i class="fas fa-phone"></i> ${phoneDisplay}</p>
                <p><i class="fas fa-clock"></i> ${hoursDisplay}</p>
                <div class="admin-actions">
                    <button class="edit-btn" data-id="${business.id}">Edit</button>
                    <button class="delete-btn" data-id="${business.id}">Delete</button>
                </div>
            </div>
        `;

            card.querySelector('.edit-btn').addEventListener('click', () => openEditBusinessModal(business.id));
            card.querySelector('.delete-btn').addEventListener('click', () => deleteBusiness(business.id));

            return card;
        }

        // Initial load
        loadBusinessList();

        function openEditBusinessModal(businessId) {
            axios.get(`../php/getBusinessById.php?id=${businessId}`)
                .then(response => {
                    if (response.data.success) {
                        const business = response.data.business;
                        document.getElementById('modal-title').textContent = 'Edit Business';
                        document.getElementById('business-id').value = business.id;
                        document.getElementById('ownerName').value = business.owner_name;
                        document.getElementById('name').value = business.business_name;
                        document.getElementById('description').value = business.description || '';
                        document.getElementById('category').value = business.category;
                        document.getElementById('address').value = business.address;
                        document.getElementById('phone').value = business.phone || '';
                        document.getElementById('hours').value = business.hours || '';

                        if (business.image) {
                            document.getElementById('image-preview').src = business.image;
                            document.getElementById('image-preview-container').style.display = 'block';
                        } else {
                            document.getElementById('image-preview-container').style.display = 'none';
                            document.getElementById('image-preview').src = '#';
                        }

                        document.getElementById('business-modal').style.display = 'block';
                    } else {
                        alert('Business not found.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching business data:', error);
                });
        }

        function deleteBusiness(businessId) {
            const reason = prompt("Please enter the reason for deleting this business:");

            if (reason) {
                // Send request to delete business
                axios.post('../php/deleteBusiness.php', {
                        'business-id': businessId,
                        'reason_for_delete': reason
                    })
                    .then(response => {
                        if (response.data.success) {
                            alert(response.data.message);
                            loadBusinessList(); // Reload business list after deletion
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting business:', error);
                        alert('An error occurred. Please try again.');
                    });
            } else {
                alert('Deletion reason is required.');
            }
        }
    </script>

</body>

</html>