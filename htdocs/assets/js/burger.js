document.addEventListener('DOMContentLoaded', () => {
    const burgerButton = document.getElementById('burger-button');
    const burgerMenu = document.getElementById('burger-menu');

    burgerButton.addEventListener('click', () => {
        // Toggle the active class to slide the menu
        burgerMenu.classList.toggle('active');
    });
});
