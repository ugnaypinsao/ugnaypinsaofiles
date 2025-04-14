document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const businessList = document.getElementById('business-list');
    
    // Event Listeners
    searchInput.addEventListener('input', renderBusinessList);
    categoryFilter.addEventListener('change', renderBusinessList);
    window.addEventListener('storage', function(e) {
        if (e.key === BusinessStorage.STORAGE_KEY) {
            renderBusinessList();
        }
    });

    // Initial render
    renderBusinessList();

    function renderBusinessList() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        
        const filteredBusinesses = BusinessStorage.getBusinesses().filter(business => {
            const matchesSearch = 
                (business.ownerName && business.ownerName.toLowerCase().includes(searchTerm)) || 
                business.name.toLowerCase().includes(searchTerm) || 
                (business.description && business.description.toLowerCase().includes(searchTerm));
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
        
        // Format category name by replacing underscores and capitalizing
        const formattedCategory = business.category
            .replace(/_/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
        
        const imageUrl = business.image || 'https://via.placeholder.com/300x200?text=No+Image';
        const phoneDisplay = business.phone || 'Not provided';
        const hoursDisplay = business.hours || 'Not specified';
        const ownerDisplay = business.ownerName || 'Not specified';
        
        card.innerHTML = `
            <img src="${imageUrl}" alt="${business.name}" class="business-image">
            <div class="business-info">
                <h3>${business.name}</h3>
                <p><strong>Owner:</strong> ${ownerDisplay}</p>
                <span class="category">${formattedCategory}</span>
                ${business.description ? `<p>${business.description}</p>` : ''}
                <p><i class="fas fa-map-marker-alt"></i> ${business.address}</p>
                <p class="contact"><i class="fas fa-phone"></i> ${phoneDisplay}</p>
                <p><i class="fas fa-clock"></i> ${hoursDisplay}</p>
            </div>
        `;
        
        return card;
    }
});