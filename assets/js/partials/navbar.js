document.addEventListener("DOMContentLoaded", () => {
    const burgerMenu = document.querySelector(".burger-menu");
    const mobileMenu = document.querySelector(".mobile-menu");

    if (burgerMenu && mobileMenu) {
        // Add event listener for toggling the mobile menu
        burgerMenu.addEventListener("click", () => {
            mobileMenu.classList.toggle("open");
            burgerMenu.classList.toggle("active");
        });
    } else {
        console.error("Burger menu or mobile menu not found in the DOM.");
    }
});