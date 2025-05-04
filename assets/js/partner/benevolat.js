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

function uploadImageAsync(inputElement, benevolatId) {
    const file = inputElement.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    fetch(`/dashboard/partenaire/benevolat/upload-image/${benevolatId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur lors du téléchargement de l\'image');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Mettre à jour l'aperçu de l'image
            const imagePreview = document.getElementById(`imagePreview${benevolatId}`);
            imagePreview.src = `/uploads/benevolat_images/${data.fileName}`;
        } else {
            throw new Error(data.error || 'Erreur inconnue');
        }
    })
    .catch(error => {
        console.error(error);
        alert('Une erreur est survenue lors du téléchargement de l\'image.');
    });
}

    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteBenevolatModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // Bouton qui déclenche la modale
            const button = event.relatedTarget;
    
            // Récupérer les données du bénévolat
            const benevolatId = button.getAttribute('data-benevolat-id');
            const title = button.getAttribute('data-benevolat-title');
            const date = button.getAttribute('data-benevolat-date');
            const location = button.getAttribute('data-benevolat-location');
    
            // Mettre à jour les éléments dans la modale
            document.getElementById('modalBenevolatId').value = benevolatId;
            document.getElementById('modalBenevolatTitle').textContent = title;
            document.getElementById('modalBenevolatDate').textContent = date;
            document.getElementById('modalBenevolatLocation').textContent = location;
    
            // Mettre à jour l'action du formulaire pour inclure l'ID du bénévolat
            const deleteForm = document.getElementById('deleteBenevolatForm');
            deleteForm.action = `/dashboard/partenaire/benevolats/supprimer/${benevolatId}`;
        });
    });