document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const adminBusinessList = document.getElementById('admin-business-list');
    const addBusinessBtn = document.getElementById('add-business-btn');
    const businessModal = document.getElementById('business-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const businessForm = document.getElementById('business-form');
    const modalTitle = document.getElementById('modal-title');
    const imageInput = document.getElementById('image');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image-btn');
    const logoutBtn = document.getElementById('logout-btn');
    
    // State
    let currentImageUrl = '';
    let isNewImageSelected = false;

    // Event Listeners
    addBusinessBtn.addEventListener('click', openAddBusinessModal);
    closeModalBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => e.target === businessModal && closeModal());
    imageInput.addEventListener('change', handleImagePreview);
    removeImageBtn.addEventListener('click', removeImage);
    businessForm.addEventListener('submit', handleFormSubmit);
    logoutBtn.addEventListener('click', () => window.location.href = '../residents/');

    // Initial render
    renderAdminBusinessList();

    // Functions
    function renderAdminBusinessList() {
        adminBusinessList.innerHTML = '';
        const businesses = BusinessStorage.getBusinesses();
        
        if (businesses.length === 0) {
            adminBusinessList.innerHTML = '<p class="no-results">No businesses found. Add your first business!</p>';
            return;
        }

        businesses.forEach(business => {
            adminBusinessList.appendChild(createAdminBusinessCard(business));
        });
    }

    function createAdminBusinessCard(business) {
        const card = document.createElement('div');
        card.className = 'business-card';
        
        const categoryLabel = business.category.charAt(0).toUpperCase() + business.category.slice(1);
        const imageUrl = business.image || 'https://via.placeholder.com/300x200?text=No+Image';
        
        card.innerHTML = `
            <img src="${imageUrl}" alt="${business.name}" class="business-image">
            <div class="business-info">
                <h3>${business.name}</h3>
                <span class="category">${categoryLabel}</span>
                <p>${business.description}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${business.address}</p>
                <p class="contact"><i class="fas fa-phone"></i> ${business.phone}</p>
                <p><i class="fas fa-clock"></i> ${business.hours}</p>
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

    function openAddBusinessModal() {
        modalTitle.textContent = 'Add New Business';
        businessForm.reset();
        document.getElementById('business-id').value = '';
        currentImageUrl = '';
        isNewImageSelected = false;
        imagePreviewContainer.style.display = 'none';
        businessModal.style.display = 'block';
    }

    function openEditBusinessModal(id) {
        const business = BusinessStorage.getBusinessById(id);
        if (!business) return;
        
        modalTitle.textContent = 'Edit Business';
        document.getElementById('business-id').value = business.id;
        document.getElementById('name').value = business.name;
        document.getElementById('description').value = business.description;
        document.getElementById('category').value = business.category;
        document.getElementById('address').value = business.address;
        document.getElementById('phone').value = business.phone;
        document.getElementById('hours').value = business.hours;
        
        currentImageUrl = business.image || '';
        isNewImageSelected = false;
        
        if (business.image) {
            imagePreview.src = business.image;
            imagePreviewContainer.style.display = 'block';
        } else {
            imagePreviewContainer.style.display = 'none';
        }
        
        imageInput.value = '';
        businessModal.style.display = 'block';
    }

    function closeModal() {
        businessModal.style.display = 'none';
    }

    function handleImagePreview() {
        if (!this.files || !this.files[0]) return;
        
        const file = this.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!validTypes.includes(file.type)) {
            alert('Please upload a JPEG, PNG, or GIF image');
            this.value = '';
            return;
        }
        
        if (file.size > maxSize) {
            alert('Image must be less than 2MB');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreviewContainer.style.display = 'block';
            isNewImageSelected = true;
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        imageInput.value = '';
        imagePreview.src = '';
        imagePreviewContainer.style.display = 'none';
        currentImageUrl = '';
        isNewImageSelected = true;
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        saveBusiness();
    }

    function saveBusiness() {
        const id = document.getElementById('business-id').value;
        const name = document.getElementById('name').value.trim();
        const description = document.getElementById('description').value.trim();
        const category = document.getElementById('category').value;
        const address = document.getElementById('address').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const hours = document.getElementById('hours').value.trim();

        if (!name || !description || !address || !phone || !hours) {
            alert('Please fill in all required fields');
            return;
        }

        // Determine image to use
        let image = '';
        if (isNewImageSelected) {
            // Use the new image if one was selected
            image = imagePreview.src || '';
        } else {
            // Otherwise keep the existing image (if editing)
            image = currentImageUrl || '';
        }

        const businessData = { name, description, category, address, phone, hours, image };

        try {
            if (id) {
                BusinessStorage.updateBusiness(parseInt(id), businessData);
            } else {
                BusinessStorage.addBusiness(businessData);
            }

            window.dispatchEvent(new Event('storage'));
            closeModal();
            renderAdminBusinessList();
        } catch (error) {
            console.error('Error saving business:', error);
            alert('An error occurred while saving the business');
        }
    }

    function deleteBusiness(id) {
        if (confirm('Are you sure you want to delete this business?')) {
            BusinessStorage.deleteBusiness(id);
            window.dispatchEvent(new Event('storage'));
            renderAdminBusinessList();
        }
    }
});