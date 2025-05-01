function generatePDF(demandeId) {
  const { jsPDF } = window.jspdf;

  // Sélectionnez la modale correspondant à la demande
  const modal = document.querySelector(`#viewDemandeModal${demandeId}`);
  if (!modal) {
    console.error(`Modale avec l'ID #viewDemandeModal${demandeId} introuvable.`);
    return;
  }

  // Fonction utilitaire pour trouver un élément `p` avec un texte spécifique
  function findParagraphWithText(modal, searchText) {
    const paragraphs = modal.querySelectorAll('p');
    for (let paragraph of paragraphs) {
      if (paragraph.textContent.includes(searchText)) {
        return paragraph;
      }
    }
    return null;
  }

  // Récupérer les informations
  const clientNomElement = findParagraphWithText(modal, "Nom du client :");
  const clientNom = clientNomElement ? clientNomElement.textContent.split(': ')[1] : 'Inconnu';

  const typeAideElement = findParagraphWithText(modal, "Type d'aide :");
  const typeAide = typeAideElement ? typeAideElement.textContent.split(': ')[1] : 'Non spécifié';

  const descriptionElement = modal.querySelector('.border');
  const description = descriptionElement ? descriptionElement.textContent : 'Aucune description';

  const montantDemandeElement = findParagraphWithText(modal, "Montant demandé :");
  const montantDemande = montantDemandeElement ? montantDemandeElement.textContent.split(': ')[1] : 'Non spécifié';

  const dateDemandeElement = findParagraphWithText(modal, "Date de demande :");
  const dateDemande = dateDemandeElement ? dateDemandeElement.textContent.split(': ')[1] : 'Non spécifiée';

  const statusElement = modal.querySelector('span.badge');
  const status = statusElement ? statusElement.textContent.trim() : 'Non spécifié';

  // Créez une instance de jsPDF
  const pdf = new jsPDF('p', 'mm', 'a4'); // Format portrait, unité millimètre, format A4

  // Dimensions du PDF
  const pageWidth = 160;
  const pageHeight = pdf.internal.pageSize.getHeight();
  const marginLeft = 10;
  const marginRight = 10;
  const lineHeight = 8;
  const sectionSpacing = 6; // Ajoute un espacement supplémentaire entre les sections
  let cursorY = 20;

  // Ajouter le logo
  const logo = new Image();
  logo.src = '/images/logo.png'; // Chemin vers le logo
  logo.crossOrigin = 'anonymous'; // Gérer les problèmes liés à CORS
  logo.onload = function () {
    const logoWidth = 40; // Largeur du logo en mm
    const logoHeight = 10; // Hauteur du logo en mm
    const logoX = (pdf.internal.pageSize.getWidth() - logoWidth) / 2; // Centrer le logo
    const logoY = 10; // Position verticale du logo

    pdf.addImage(logo, 'PNG', logoX, logoY, logoWidth, logoHeight);

    // Ajouter un margin-bottom de 15 mm après le logo
    cursorY = logoY + logoHeight + 25;

    // Ajouter le titre (Aligné à gauche)
    pdf.setFontSize(18);
    pdf.setFont('helvetica', 'bold');
    const title = 'Détails de la Demande';
    pdf.text(title, marginLeft, cursorY); // Aligné à gauche
    cursorY += lineHeight * 2;

    // Ajouter les informations client
    pdf.setFontSize(12);
    pdf.setFont('helvetica', 'normal');
    pdf.text(`Nom du client : ${clientNom}`, marginLeft, cursorY);
    cursorY += lineHeight + sectionSpacing;

    // Ajouter les détails de la demande
    pdf.text(`Type d'aide : ${typeAide}`, marginLeft, cursorY);
    cursorY += lineHeight + sectionSpacing;

    pdf.text('Description :', marginLeft, cursorY);
    cursorY += lineHeight;

    // Diviser le texte de la description en lignes adaptées à la largeur du PDF
    const descriptionLines = pdf.splitTextToSize(description, pageWidth - marginLeft - marginRight); // Largeur utilisable
    pdf.setFontSize(10);
    pdf.setFont('courier', 'normal');
    descriptionLines.forEach((line) => {
      if (cursorY + lineHeight > 297 - 10) { // Vérifier si on dépasse la page
        pdf.addPage();
        cursorY = 20;
      }
      pdf.text(line, marginLeft, cursorY);
      cursorY += lineHeight;
    });
    cursorY += sectionSpacing; // Ajoute un espace après la description

    // Ajouter les informations financières
    pdf.setFontSize(12);
    pdf.setFont('helvetica', 'normal');
    pdf.text(`Montant demandé : ${montantDemande}`, marginLeft, cursorY);
    cursorY += lineHeight + sectionSpacing;

    pdf.text(`Date de demande : ${dateDemande}`, marginLeft, cursorY);
    cursorY += lineHeight + sectionSpacing;

    // Ajouter le statut sans espace excessif
    pdf.text('Statut :', marginLeft, cursorY);
    const statusX = marginLeft + pdf.getTextWidth('Statut : ') + 2; // Ajuster la position pour éviter un grand espace
    pdf.text(status, statusX, cursorY);
    cursorY += lineHeight + sectionSpacing;

    // Ajouter le QR Code en bas de la page
    const qrCodeLink = 'https://miravia.space/';
    const qrText = 'Scannez ce QR Code pour visiter :';
    const qrTextWidth = pdf.getTextWidth(qrText); // Calculer la largeur du texte
    pdf.setFontSize(10);
    pdf.setFont('helvetica', 'normal');
    pdf.text(qrText, (pdf.internal.pageSize.getWidth() - qrTextWidth) / 2, pageHeight - 45); // Centrer le texte
    pdf.addImage(
      generateQRCode(qrCodeLink),
      'PNG',
      (pdf.internal.pageSize.getWidth() / 2) - 15, // Centrer le QR code horizontalement
      pageHeight - 35, // Position verticale du QR code
      30,
      30 // Taille du QR code
    );

    // Préparer le nom de fichier avec le nom du client (remplacement des caractères spéciaux par des traits d'union)
    const sanitizedClientNom = clientNom.replace(/[^a-zA-Z0-9]/g, '-');

    // Télécharger le fichier PDF
    pdf.save(`demande_${sanitizedClientNom}.pdf`);
  };

  logo.onerror = function () {
    console.error("Le logo n'a pas pu être chargé. Vérifiez le chemin du fichier.");
  };

  // Fonction pour générer un QR Code
  function generateQRCode(link) {
    const canvas = document.createElement('canvas');
    QRCode.toCanvas(canvas, link, { width: 150 });
    return canvas.toDataURL('image/png');
  }
}

  // Fonction pour générer un QR Code
  function generateQRCode(link) {
    const canvas = document.createElement('canvas');
    QRCode.toCanvas(canvas, link, { width: 150 });
    return canvas.toDataURL('image/png');
  }



document.addEventListener('DOMContentLoaded', () => {
  const statusFilter = document.getElementById('statusFilter');
  const demandesTable = document.getElementById('demandesTable');
  const rows = Array.from(demandesTable.querySelectorAll('tr'));

  statusFilter.addEventListener('change', () => {
    const selectedStatus = statusFilter.value; // Statut sélectionné

    rows.forEach(row => {
      // Vérifiez si la ligne correspond au statut sélectionné
      const rowStatus = row.getAttribute('data-status');
      if (selectedStatus === '' || rowStatus === selectedStatus) {
        row.style.display = ''; // Affiche la ligne
      } else {
        row.style.display = 'none'; // Masque la ligne
      }
    });
  });
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

document.addEventListener('DOMContentLoaded', function() {
  // Écouteur pour le clic sur les icônes œil
  document.querySelectorAll('[data-bs-target^="#viewDemandeModal"]').forEach(icon => {
    icon.addEventListener('click', function() {
      const modalId = this.getAttribute('data-bs-target');
      const modal = new bootstrap.Modal(document.querySelector(modalId));
      modal.show();
    });
  });
});

document.addEventListener('hidden.bs.modal', function (event) {
  // Forcer la suppression de l'overlay de Bootstrap
  if (document.querySelector('.modal-backdrop')) {
    document.querySelector('.modal-backdrop').remove();
  }

  // Supprimer la classe "modal-open" de <body>, si elle est toujours présente
  document.body.classList.remove('modal-open');
  document.body.style.overflow = ''; // Réinitialiser le défilement
});
function openPdfPopup(pdfUrl) {
  // Ouvre une nouvelle fenêtre pour afficher le PDF
  const pdfWindow = window.open("", "_blank", "width=800,height=600");
  pdfWindow.document.write(`
      <html>
          <head>
              <title>Preuve PDF</title>
              <style>
                  body {
                      margin: 0;
                      padding: 0;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      height: 100vh;
                      }
              </style>
          </head>
          <body>
              <embed src="${pdfUrl}" type="application/pdf" style="width:90%; height:90%;" />
          </body>
      </html>
  `);
}
function filterTable() {
  const searchInput = document.getElementById('searchBar').value.toLowerCase();
  const tableRows = document.querySelectorAll('#demandesTable tr');

  tableRows.forEach(row => {
      // Extract text content from the relevant columns
      const clientName = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
      const typeAide = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
      const description = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
      const montant = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
      const dateDemande = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';

      // Check if the search input matches any column
      if (
          clientName.includes(searchInput) ||
          typeAide.includes(searchInput) ||
          description.includes(searchInput) ||
          montant.includes(searchInput) || // Include numeric values in 'Montant'
          dateDemande.includes(searchInput) // Include numeric values in 'Date de demande'
      ) {
          row.style.display = ''; // Show row
      } else {
          row.style.display = 'none'; // Hide row
      }
  });
}
