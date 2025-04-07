document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const businessList = document.getElementById('business-list');
    
    // Event Listeners
    searchInput.addEventListener('input', renderBusinessList);
    categoryFilter.addEventListener('change', renderBusinessList);
    window.addEventListener('storage', renderBusinessList);

    // Initial render
    renderBusinessList();

    function renderBusinessList() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        
        const filteredBusinesses = BusinessStorage.getBusinesses().filter(business => {
            const matchesSearch = business.name.toLowerCase().includes(searchTerm) || 
                                business.description.toLowerCase().includes(searchTerm);
            const matchesCategory = selectedCategory === 'all' || business.category === selectedCategory;
            return matchesSearch && matchesCategory;
        });
        
        businessList.innerHTML = '';
        
        if (filteredBusinesses.length === 0) {
            businessList.innerHTML = '<p class="no-results">No businesses found matching your criteria.</p>';
            return;
        }
        
        filteredBusinesses.forEach(business => {
            businessList.appendChild(createBusinessCard(business));
        });
    }
    
    function createBusinessCard(business) {
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
            </div>
        `;
        
        return card;
    }
});