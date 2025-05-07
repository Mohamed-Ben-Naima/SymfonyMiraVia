document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            // Remove active class from all tabs
            tabs.forEach((t) => t.classList.remove("active"));

            // Add active class to clicked tab
            tab.classList.add("active");

            // Hide all tab contents
            contents.forEach((content) => content.classList.remove("active"));

            // Show the content corresponding to the clicked tab
            const target = tab.dataset.tab;
            document.getElementById(target).classList.add("active");
        });
    });

    // Set the first tab and content as active by default
    tabs[0].classList.add("active");
    contents[0].classList.add("active");
});

// Gestion du header flottant
window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});