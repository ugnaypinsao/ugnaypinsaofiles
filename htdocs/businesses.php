<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Business Directory</title>
    <link rel="stylesheet" href="assets/css/res_business.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <h1>Local Business Directory</h1>
    </header>

    <main>
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search businesses...">
            <select id="category-filter">
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

        <div id="business-list" class="business-grid"></div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let businesses = [];

        // Load business data when page loads
        loadBusinessList();

        function loadBusinessList() {
            axios.get('php/getBusinesses.php')
                .then(response => {
                    const businessList = document.getElementById('business-list');
                    businessList.innerHTML = '';

                    if (response.data.success) {
                        businesses = response.data.businesses.filter(business => business.status !== 'deleted'); // Exclude deleted businesses
                        filterAndDisplayBusinesses();
                    }
                })
                .catch(error => {
                    console.error('Error loading businesses:', error);
                });
        }

        function filterAndDisplayBusinesses() {
            const searchQuery = document.getElementById('search-input').value.toLowerCase();
            const selectedCategory = document.getElementById('category-filter').value;

            const filteredBusinesses = businesses.filter(business => {
                const isCategoryMatch = selectedCategory === 'all' || business.category === selectedCategory;
                const isSearchMatch = business.business_name.toLowerCase().includes(searchQuery) ||
                                      business.owner_name.toLowerCase().includes(searchQuery) ||
                                      business.description.toLowerCase().includes(searchQuery);
                return isCategoryMatch && isSearchMatch;
            });

            displayBusinesses(filteredBusinesses);
        }

        function displayBusinesses(filteredBusinesses) {
            const businessList = document.getElementById('business-list');
            businessList.innerHTML = '';

            filteredBusinesses.forEach(business => {
                const businessCard = createBusinessCard(business);
                businessList.appendChild(businessCard);
            });
        }

        function createBusinessCard(business) {
            const card = document.createElement('div');
            card.className = 'business-card';

            const formattedCategory = business.category
                .replace(/_/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());

            var imageUrl = business.image || 'https://via.placeholder.com/300x200?text=No+Image';
            const phoneDisplay = business.phone || 'Not provided';
            const hoursDisplay = business.hours || 'Not specified';
            if (imageUrl.startsWith("../")) {
                imageUrl = imageUrl.substring(3); // Remove the first 3 characters
            }

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
                </div>
            `;

            return card;
        }

        // Event listeners for search input and category filter
        document.getElementById('search-input').addEventListener('input', filterAndDisplayBusinesses);
        document.getElementById('category-filter').addEventListener('change', filterAndDisplayBusinesses);
    </script>
</body>

</html>
