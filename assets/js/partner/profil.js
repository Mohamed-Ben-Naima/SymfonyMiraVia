// Gestion du header flottant
window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});

// Avatar
document.querySelector(".user-avatar input[type='file']").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
      const img = document.querySelector(".user-avatar img");
      img.src = URL.createObjectURL(file);
    }
  });

  // Logo
  document.querySelector(".company-logo-wrapper input[type='file']").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
      const img = document.querySelector(".company-logo-wrapper img");
      img.src = URL.createObjectURL(file);
    }
  });