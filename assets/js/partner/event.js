// Gestion du header flottant
window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});

document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function (event) {
        const file = event.target.files[0];
        const eventId = this.id.replace('imageName', ''); // Extraire l'ID de l'événement
        const formData = new FormData();
        formData.append('imageName', file);

        // Envoi du fichier via AJAX
        fetch(`/dashboard/partenaire/evenement/upload-image/${eventId}`, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Met à jour l'image actuelle dans l'interface utilisateur
                const imgElement = document.querySelector(`#editEventModal${eventId} img`);
                imgElement.src = data.imagePath; // Chemin de la nouvelle image
            } else {
                alert(data.error || 'Erreur lors du téléchargement de l\'image.');
            }
        })
    });
});

// Écouteur d'événement pour remplir la modale de confirmation
const deleteEventModal = document.getElementById('deleteEventModal');
deleteEventModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; // Bouton qui déclenche la modale
    const eventId = button.getAttribute('data-event-id');
    const eventTitle = button.getAttribute('data-event-title');
    const eventDate = button.getAttribute('data-event-date');
    const eventLocation = button.getAttribute('data-event-location');

    // Remplir les informations dans la modale
    const modalTitle = deleteEventModal.querySelector('#eventTitle');
    const modalDate = deleteEventModal.querySelector('#eventDate');
    const modalLocation = deleteEventModal.querySelector('#eventLocation');
    const confirmDeleteButton = deleteEventModal.querySelector('#confirmDeleteButton');

    modalTitle.textContent = eventTitle;
    modalDate.textContent = eventDate;
    modalLocation.textContent = eventLocation;

    // Ajouter une action pour le bouton "Confirmer"
    confirmDeleteButton.onclick = function () {
        // Redirection vers la route de suppression
        window.location.href = `/dashboard/partenaire/evenement/supprimer/${eventId}`;
    };
});

// Script pour mettre à jour l'aperçu de l'image lorsqu'un fichier est sélectionné
document.getElementById('imageName').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const icon = document.getElementById('imageIcon');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result; // Mettre à jour l'aperçu
            preview.classList.remove('d-none'); // Afficher l'image
            icon.classList.add('d-none'); // Masquer l'icône
        };
        reader.readAsDataURL(file);
    } else {
        // Réinitialiser l'aperçu si aucun fichier n'est sélectionné
        preview.src = "https://via.placeholder.com/300x200?text=Aperçu+Image";
        preview.classList.add('d-none'); // Masquer l'image
        icon.classList.remove('d-none'); // Afficher l'icône
    }
});