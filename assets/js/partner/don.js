// Gestion du header flottant
window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});
// Fonction de filtrage de la table
document.getElementById('searchBar').addEventListener('input', function() {
    const searchInput = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#donsTable tr');

    tableRows.forEach(row => {
      const rowText = row.textContent.toLowerCase(); // Récupère tout le texte de la ligne
      if (rowText.includes(searchInput)) {
        row.style.display = ''; // Affiche la ligne
      } else {
        row.style.display = 'none'; // Cache la ligne
      }
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#donsTable tr');
  
    // Ajouter un écouteur d'événement pour le changement de statut
    statusFilter.addEventListener('change', () => {
      const selectedStatus = statusFilter.value.toLowerCase(); // Statut sélectionné
  
      tableRows.forEach(row => {
        const rowStatus = row.getAttribute('data-status').toLowerCase(); // Statut de la ligne
  
        // Vérifiez si le statut de la ligne correspond au statut sélectionné
        if (selectedStatus === '' || rowStatus === selectedStatus) {
          row.style.display = ''; // Affiche la ligne
        } else {
          row.style.display = 'none'; // Masque la ligne
        }
      });
    });
  });