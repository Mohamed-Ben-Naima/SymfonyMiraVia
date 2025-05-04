  // Gestion du header flottant
  window.addEventListener('scroll', function() {
    const header = document.querySelector('.floating-header');
    if (window.scrollY > 50) {
        
        header.style.backgroundColor = '#f5f5f9';
    } else {
        
        header.style.backgroundColor = '#f5f5f9';
    }
});

// Raccourci clavier CTRL+K pour la recherche
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-input').focus();
    }
});
  // Clock functionality
    const hour = document.getElementById('clock-hour'),
          minutes = document.getElementById('clock-minutes'),
          seconds = document.getElementById('clock-seconds');
    
    const clock = () => {
      let date = new Date();
      let hh = date.getHours() * 30,
          mm = date.getMinutes() * 6,
          ss = date.getSeconds() * 6;
      hour.style.transform = `rotateZ(${hh + mm / 12}deg)`;
      minutes.style.transform = `rotateZ(${mm}deg)`;
      seconds.style.transform = `rotateZ(${ss}deg)`;
    }
    setInterval(clock, 1000);
    
    const textHour = document.getElementById('text-hour'),
          textMinutes = document.getElementById('text-minutes'),
          dateDay = document.getElementById('date-day'),
          dateMonth = document.getElementById('date-month'),
          dateYear = document.getElementById('date-year');
    
    const clockText = () => {
      let date = new Date();
      let hh = date.getHours(),
          mm = date.getMinutes(),
          day = date.getDate(),
          month = date.getMonth(),
          year = date.getFullYear();
      if(hh == 0) hh = 12;
      if(hh < 10) hh = `0${hh}`;
      textHour.innerHTML = `${hh}:`;
      if(mm < 10) mm = `0${mm}`;
      textMinutes.innerHTML = mm;
      let months = ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'];
      dateDay.innerHTML = day;
      dateMonth.innerHTML = `${months[month]} `;
      dateYear.innerHTML = year;
    }
    setInterval(clockText, 1000);

    // Update current time
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
      document.getElementById('currentTime').textContent = timeString;
    }
    
    setInterval(updateTime, 1000);
    updateTime();
    

     function populateModal(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');

    // Populate modal fields
    document.getElementById('modal-client-name').textContent = cells[0].textContent;
    document.getElementById('modal-help-type').textContent = cells[1].textContent;
    document.getElementById('modal-description').textContent = cells[2].textContent;
    document.getElementById('modal-amount').textContent = cells[3].textContent;
    document.getElementById('modal-request-date').textContent = cells[4].textContent;
    document.getElementById('modal-status').textContent = cells[5].textContent;

    // Set proof link
    const proofLink = cells[6].querySelector('a');
    if (proofLink) {
      document.getElementById('modal-proof-link').href = proofLink.href;
      document.getElementById('modal-proof-link').textContent = proofLink.textContent;
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
  
    // Préparer les données pour le graphique
    const labels = months; // Inclure tous les mois
    const data = Array(12).fill(0); // Initialiser les données à 0 pour chaque mois
  
    // Remplir les données avec les valeurs disponibles
    donationsData.forEach(d => {
      data[d.month - 1] = d.total; // Met à jour les données pour le mois correspondant
    });
  
    // Initialiser Chart.js
    const ctx = document.getElementById('donationsChart').getContext('2d');
    const donationsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels, // Inclure tous les mois
        datasets: [{
          label: 'Dons financiers',
          data: data,
          borderColor: '#d6336c',
          backgroundColor: '#ffe2e6',
          tension: 0.2,
          fill: true,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: '#d6336c',
          pointRadius: 3,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          datalabels: {
            display: false, // Désactiver les étiquettes sur les points
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return (value / 1_000).toFixed(1) + ' M'; // Formater les ticks en millions
              }
            }
          },
          x: {
            ticks: {
              autoSkip: false, // Désactiver l'auto-saut pour afficher tous les mois
            }
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const yearSelector = document.getElementById("year-selector");
    const growthAmount = document.getElementById("growth-amount");
    const growthSummary = document.getElementById("growth-summary");
    const donationsBreakdown = document.getElementById("donations-breakdown");

    // Données des dons par année (remplies dynamiquement depuis le backend)
    const donationsData = JSON.parse('{{ donationsByYear|json_encode|e("js") }}');

    // Met à jour les données affichées en fonction de l'année sélectionnée
    yearSelector.addEventListener("change", function () {
        const selectedYear = this.value;

        // Met à jour le montant des dons dans le cercle
        growthAmount.textContent = donationsData[selectedYear]
            ? `${donationsData[selectedYear].toLocaleString("fr-FR", { minimumFractionDigits: 2 })} €`
            : "0,00 €";

        // Met à jour le résumé
        growthSummary.textContent = `Nombre de dons pour ${selectedYear} :`;

        // Met à jour la liste des détails des dons
        donationsBreakdown.innerHTML = `
            <div class="donation-item">
                <span class="icon">📆</span>
                <span class="year">${selectedYear}:</span>
                <span class="amount">${
                    donationsData[selectedYear]
                        ? `${donationsData[selectedYear].toLocaleString("fr-FR", { minimumFractionDigits: 2 })} €`
                        : "0,00 €"
                }</span>
            </div>
        `;
    });
});

document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("donationPieChart").getContext("2d");

  // Données des dons
  const acceptedDonations = parseInt(document.getElementById("accepted-donations").textContent.replace(/\s/g, '').replace(',', '')) || 0;
  const pendingDonations = parseInt(document.getElementById("pending-donations").textContent.replace(/\s/g, '').replace(',', '')) || 0;

  const totalDonations = acceptedDonations + pendingDonations;

  // Initialiser le graphique
  const donationPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      
      datasets: [
        {
          data: [acceptedDonations, pendingDonations], // Include only accepted and pending donations
          backgroundColor: ["#3498db", "#d6336c"], // Blue and yellow colors
          hoverOffset: 10,
        },
      ],
    },
    options: {
      responsive: true,
      cutout: "70%",
      plugins: {
        tooltip: {
          callbacks: {
            label: function (tooltipItem) {
              const label = tooltipItem.label || "";
              const value = tooltipItem.raw || 0;
              return `${label}: ${value.toLocaleString("fr-FR")} €`;
            },
          },
        },
        legend: {
          display: true,
          position: "top",
          labels: {
            usePointStyle: true, // Circular legend markers
            padding: 10,
          },
        },
      },
    },
  });

  // Gère les événements hover pour mettre à jour le texte au centre du cercle
  document.getElementById("donationPieChart").addEventListener("mousemove", function (event) {
    const points = donationPieChart.getElementsAtEventForMode(event, "nearest", { intersect: true }, true);

    if (points.length) {
      const firstPoint = points[0];
      const label = donationPieChart.data.labels[firstPoint.index];
      const value = donationPieChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];

      const percentage = ((value / totalDonations) * 100).toFixed(1);
      document.getElementById("growth-percentage").textContent = `${value.toLocaleString("fr-FR")} €`;
      document.getElementById("growth-status").textContent = label;
    }
  });
});