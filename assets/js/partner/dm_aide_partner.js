function generatePDF(index) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    // URL du logo
    const logoUrl = "{{ asset('images/logo.png') }}";

    // Charger le logo
    const logo = new Image();
    logo.crossOrigin = 'anonymous'; // Gérer les problèmes liés à CORS
    logo.onload = function () {
      const pageWidth = pdf.internal.pageSize.getWidth(); // Largeur de la page PDF
      const logoWidth = 60; // Largeur du logo
      const logoHeight = 20; // Hauteur du logo
      const xPosition = (pageWidth - logoWidth) / 2; // Calculer la position horizontale centrée

      // Ajouter le logo centré au PDF
      pdf.addImage(logo, 'PNG', xPosition, 10, logoWidth, logoHeight); // Position (x, y), Taille (largeur, hauteur)

      // Ajouter un titre ou un espace sous le logo
      pdf.setFontSize(16);

      // Récupérer le contenu du modal
      const modal = document.querySelector(`#viewDemandeModal${index} .modal-body`);
      
      // Ajouter le texte au PDF
      let y = 50; // Position verticale initiale après le logo et le titre
      pdf.text(`Client: ${modal.querySelector('p:nth-of-type(1)').innerText.split(': ')[1]}`, 10, y);
      y += 10;
      pdf.text(`Type d'aide: ${modal.querySelector('p:nth-of-type(2)').innerText.split(': ')[1]}`, 10, y);
      y += 10;
      pdf.text(`Description: ${modal.querySelector('p:nth-of-type(3)').innerText.split(': ')[1]}`, 10, y);
      y += 10;
      pdf.text(`Montant: ${modal.querySelector('p:nth-of-type(4)').innerText.split(': ')[1]}`, 10, y);
      y += 10;
      pdf.text(`Date de demande: ${modal.querySelector('p:nth-of-type(5)').innerText.split(': ')[1]}`, 10, y);
      y += 10;
      pdf.text(`Status: ${modal.querySelector('p:nth-of-type(6)').innerText.split(': ')[1]}`, 10, y);
      y += 20;
      pdf.text(`Preuve: ${modal.querySelector('p:nth-of-type(7)').innerText.split(': ')[1]}`, 10, y);
      y += 10;

      // Récupérer l'image (preuve)
      const imgElement = modal.querySelector('img');
      if (imgElement) {
          const imgSrc = imgElement.src; // URL de l'image

          // Charger l'image en Base64
          const img = new Image();
          img.crossOrigin = 'anonymous'; // Gérer les problèmes de politique CORS
          img.onload = function () {
              const canvas = document.createElement('canvas');
              const ctx = canvas.getContext('2d');
              canvas.width = img.width;
              canvas.height = img.height;
              ctx.drawImage(img, 0, 0);

              // Convertir l'image en Base64
              const imgData = canvas.toDataURL('image/png');

              // Ajouter l'image au PDF
              pdf.addImage(imgData, 'PNG', 10, y, 50, 50);

              // Télécharger le fichier PDF
              pdf.save(`demande_${index}.pdf`);
          };
          img.src = imgSrc;
      } else {
          // Si aucune image n'est disponible, générer le PDF sans image
          pdf.save(`demande_${index}.pdf`);
      }
    };

    // Définir la source du logo
    logo.src = logoUrl;


    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionnez toutes les modales
        const modals = document.querySelectorAll('.modal');
      
        // Ajoutez des écouteurs d'événements pour chaque modale
        modals.forEach(function (modal) {
          // Événement déclenché lorsque la modale est sur le point de s'afficher
          modal.addEventListener('show.bs.modal', function () {
            console.log(`La fenêtre modale avec l'ID "${modal.id}" est en train de s'afficher.`);
          });
      
          // Événement déclenché lorsque la modale est complètement affichée
          modal.addEventListener('shown.bs.modal', function () {
            console.log(`La fenêtre modale avec l'ID "${modal.id}" est affichée.`);
          });
      
          // Événement déclenché lorsque la modale est sur le point de se fermer
          modal.addEventListener('hide.bs.modal', function () {
            console.log(`La fenêtre modale avec l'ID "${modal.id}" est en train de se fermer.`);
          });
      
          // Événement déclenché lorsque la modale est complètement fermée
          modal.addEventListener('hidden.bs.modal', function () {
            console.log(`La fenêtre modale avec l'ID "${modal.id}" est fermée.`);
          });
        });
      });

      let demandeIdToDelete = null;

      function showConfirmationModal(demandeId) {
          // Save the ID of the demande to delete
          demandeIdToDelete = demandeId;
      
          // Show the confirmation modal
          const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
          confirmationModal.show();
      }
      
      document.getElementById('confirmDeleteButton').addEventListener('click', function () {
          if (demandeIdToDelete) {
              // Send a DELETE request to the server
              fetch(`/dashboard/partenaire/demande_aide/delete/${demandeIdToDelete}`, {
                  method: 'DELETE',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                      'Content-Type': 'application/json',
                  },
              })
              .then(response => {
                  if (response.ok) {
                      alert('Demande supprimée avec succès !');
                      location.reload(); // Reload the page to reflect the changes
                  } else {
                      alert('Une erreur s\'est produite lors de la suppression de la demande.');
                  }
              })
              .catch(error => {
                  console.error('Erreur:', error);
                  alert('Une erreur s\'est produite lors de la suppression de la demande.');
              });
          }
      });
      
    }