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
    
    // Chart initialization
    const ctx = document.getElementById('donationsChart').getContext('2d');
    const donationsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
          label: 'Dons financiers',
          data: [1200, 1900, 1500, 2000, 2200, 2500, 2800, 2600, 3000, 3200, 3500, 4000],
          borderColor: '#d6336c',
          backgroundColor: '#ffe2e6',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
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