const BusinessStorage = {
    STORAGE_KEY: 'localBusinessDirectory',

    getBusinesses: function() {
        const businessesJson = localStorage.getItem(this.STORAGE_KEY);
        return businessesJson ? JSON.parse(businessesJson) : [];
    },

    getBusinessById: function(id) {
        const businesses = this.getBusinesses();
        return businesses.find(b => b.id === id);
    },

    addBusiness: function(business) {
        const businesses = this.getBusinesses();
        const newId = businesses.length > 0 ? Math.max(...businesses.map(b => b.id)) + 1 : 1;
        const newBusiness = { ...business, id: newId };
        businesses.push(newBusiness);
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(businesses));
        return newBusiness;
    },

    updateBusiness: function(id, updatedBusiness) {
        const businesses = this.getBusinesses();
        const index = businesses.findIndex(b => b.id === id);
        if (index !== -1) {
            businesses[index] = { ...updatedBusiness, id };
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(businesses));
            return businesses[index];
        }
        return null;
    },

    deleteBusiness: function(id) {
        const businesses = this.getBusinesses().filter(b => b.id !== id);
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(businesses));
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = BusinessStorage;
}