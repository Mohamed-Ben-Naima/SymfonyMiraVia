// Gestion du header flottant
window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});
  // Initialisation de AOS (Animation On Scroll)
  AOS.init({
    offset: 120, // Décalage de l'animation
    delay: 0, // Pas de délai
    duration: 1000, // Durée de l'animation
    once: true, // Animer une seule fois
  });
  
  // Loader disparition après chargement
  window.addEventListener("load", () => {
    document.getElementById("loader").style.display = "none";
  });
  
  // Animation lors du défilement pour la barre de navigation
  window.onscroll = function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }
  document.querySelector('.btn').addEventListener('click', function (e) {
      e.preventDefault();  // Empêche le comportement par défaut
      document.querySelector('#about').scrollIntoView({ behavior: 'smooth' });
  });