// Select the menu toggle button and main menu
const menuToggle = document.querySelector('.menu-toggle');
const mainMenu = document.querySelector('.main-menu');

// Add click event listener to toggle menu visibility
menuToggle.addEventListener('click', () => {
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

    // Toggle the 'show' class to display or hide the menu
    mainMenu.classList.toggle('show');
    menuToggle.setAttribute('aria-expanded', !isExpanded);
});
