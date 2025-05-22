// vols.js
console.log('Fichier vols.js chargé et lu !'); // Vérifie si le fichier est lu
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab-button');
    const forms = document.querySelectorAll('.search-form');

    // --- Gestion des onglets ---
    const defaultFormId = 'flights-form';
    const defaultForm = document.getElementById(defaultFormId);
    if (defaultForm) {
        defaultForm.classList.add('active-form');
    }
    tabs.forEach(tab => {
        if (tab.getAttribute('data-form') === defaultFormId) {
            tab.classList.add('active');
        }
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            forms.forEach(f => {
                f.classList.remove('active-form');
                // Optionnel : réinitialiser l'animation pour la prochaine apparition
                // f.style.animation = 'none';
                // void f.offsetWidth; // Déclenche un reflow
                // f.style.animation = null;
            });
            this.classList.add('active');
            const targetFormId = this.getAttribute('data-form');
            const targetForm = document.getElementById(targetFormId);
            if (targetForm) {
                targetForm.classList.add('active-form');
            }
        });
    });

    // --- Initialisation de Flatpickr ---
    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        locale: "fr",
        allowInput: true,
        disableMobile: "true" // Pour forcer Flatpickr sur mobile également
    });

    // --- Fonction utilitaire pour positionner un popup ---
    function positionPopupUnderInput(popupElement, inputElement) {
        const inputRect = inputElement.getBoundingClientRect();
        const popupRect = popupElement.getBoundingClientRect(); // Obtenir les dimensions du popup
        let popupTop = window.scrollY + inputRect.bottom + 8; // 8px de marge
        let popupLeft = window.scrollX + inputRect.left;

        // Ajuster si le popup dépasse la largeur de la fenêtre
        if (popupLeft + popupRect.width > window.innerWidth - 10) { // 10px de marge de la fenêtre
            popupLeft = window.innerWidth - popupRect.width - 10;
        }
        // Assurer que popupLeft n'est pas négatif
        if (popupLeft < 10) {
            popupLeft = 10;
        }

        popupElement.style.top = `${popupTop}px`;
        popupElement.style.left = `${popupLeft}px`;
        // Le transform pour l'animation est géré par la classe .show et les styles CSS initiaux
        // Nous n'avons pas besoin de le modifier ici si l'animation est translateY/scale
    }

    // --- Gestion du Pop-up Voyageurs pour les Vols ---
    const travelersInputFlights = document.getElementById('travelers-flight-input');
    const travelersPopupFlights = document.getElementById('travelers-popup-flights');
    const doneTravelersFlightsBtn = document.getElementById('done-travelers-flights');
    const adultsFlightsCountInput = document.getElementById('adults-flights-count');
    const childrenFlightsCountInput = document.getElementById('children-flights-count');
    const cabinClassFlightsSelect = document.getElementById('cabin-class-flights');

    if (travelersInputFlights && travelersPopupFlights && doneTravelersFlightsBtn && adultsFlightsCountInput && childrenFlightsCountInput && cabinClassFlightsSelect) {
        travelersInputFlights.addEventListener('click', (event) => {
            event.stopPropagation();
            const isAlreadyShown = travelersPopupFlights.classList.contains('show');
            // Fermer tous les autres popups
            closeAllPopups(travelersPopupFlights);

            if (!isAlreadyShown) {
                positionPopupUnderInput(travelersPopupFlights, travelersInputFlights);
                travelersPopupFlights.classList.add('show');
            } else {
                travelersPopupFlights.classList.remove('show');
            }
        });

        doneTravelersFlightsBtn.addEventListener('click', () => {
            updateTravelersInputFlights();
            travelersPopupFlights.classList.remove('show');
        });

        travelersPopupFlights.querySelectorAll('.counter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const type = this.dataset.type;
                let countInput = (type === 'adults-flights') ? adultsFlightsCountInput : childrenFlightsCountInput;
                if (countInput) {
                    let currentValue = parseInt(countInput.value);
                    if (action === 'increment') {
                        if ((type === 'adults-flights' && currentValue < 9) || (type === 'children-flights' && currentValue < 9)) {
                           currentValue++;
                        }
                    } else if (action === 'decrement') {
                        if (type === 'adults-flights' && currentValue > 1) {
                            currentValue--;
                        } else if (type === 'children-flights' && currentValue > 0) {
                            currentValue--;
                        }
                    }
                    countInput.value = currentValue;
                }
            });
        });

        function updateTravelersInputFlights() {
            const adults = adultsFlightsCountInput.value;
            const children = childrenFlightsCountInput.value;
            const cabin = cabinClassFlightsSelect.value;
            let summary = `${adults} Adulte${adults > 1 ? 's' : ''}`;
            if (children > 0) {
                summary += `, ${children} Enfant${children > 1 ? 's' : ''}`;
            }
            summary += `, ${cabin}`;
            travelersInputFlights.value = summary;
        }
        updateTravelersInputFlights(); // Initialiser
    }

    // --- Gestion du Pop-up Hôtes et Chambres pour les Hôtels ---
    const guestsRoomsInputHotel = document.getElementById('guests-rooms-hotel-input');
    const guestsRoomsPopupHotel = document.getElementById('guests-rooms-popup-hotel');
    const doneGuestsRoomsHotelBtn = document.getElementById('done-guests-rooms-hotel');
    const adultsHotelCountInput = document.getElementById('adults-hotel-count');
    const childrenHotelCountInput = document.getElementById('children-hotel-count');
    const roomsHotelCountInput = document.getElementById('rooms-hotel-count');

    if (guestsRoomsInputHotel && guestsRoomsPopupHotel && doneGuestsRoomsHotelBtn && adultsHotelCountInput && childrenHotelCountInput && roomsHotelCountInput) {
        guestsRoomsInputHotel.addEventListener('click', (event) => {
            event.stopPropagation();
            const isAlreadyShown = guestsRoomsPopupHotel.classList.contains('show');
            closeAllPopups(guestsRoomsPopupHotel);

            if (!isAlreadyShown) {
                positionPopupUnderInput(guestsRoomsPopupHotel, guestsRoomsInputHotel);
                guestsRoomsPopupHotel.classList.add('show');
            } else {
                guestsRoomsPopupHotel.classList.remove('show');
            }
        });

        doneGuestsRoomsHotelBtn.addEventListener('click', () => {
            updateGuestsRoomsInputHotel();
            guestsRoomsPopupHotel.classList.remove('show');
        });

        guestsRoomsPopupHotel.querySelectorAll('.counter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const type = this.dataset.type;
                let countInput;
                switch (type) {
                    case 'adults-hotel': countInput = adultsHotelCountInput; break;
                    case 'children-hotel': countInput = childrenHotelCountInput; break;
                    case 'rooms-hotel': countInput = roomsHotelCountInput; break;
                }
                if (countInput) {
                    let currentValue = parseInt(countInput.value);
                    if (action === 'increment' && currentValue < 9) {
                        currentValue++;
                    } else if (action === 'decrement') {
                        if (((type === 'adults-hotel' || type === 'rooms-hotel') && currentValue > 1) || (type === 'children-hotel' && currentValue > 0)) {
                            currentValue--;
                        }
                    }
                    countInput.value = currentValue;
                }
            });
        });

        function updateGuestsRoomsInputHotel() {
            const adults = adultsHotelCountInput.value;
            const children = childrenHotelCountInput.value;
            const rooms = roomsHotelCountInput.value;
            let summary = `${adults} Adulte${adults > 1 ? 's' : ''}`;
            if (children > 0) {
                summary += `, ${children} Enfant${children > 1 ? 's' : ''}`;
            }
            summary += `, ${rooms} Chambre${rooms > 1 ? 's' : ''}`;
            guestsRoomsInputHotel.value = summary;
        }
        updateGuestsRoomsInputHotel(); // Initialiser
    }

    // --- Fonction pour fermer tous les popups ---
    function closeAllPopups(exceptThisOne = null) {
        if (travelersPopupFlights && travelersPopupFlights !== exceptThisOne) {
            travelersPopupFlights.classList.remove('show');
        }
        if (guestsRoomsPopupHotel && guestsRoomsPopupHotel !== exceptThisOne) {
            guestsRoomsPopupHotel.classList.remove('show');
        }
    }

    // --- Fermer les popups si on clique en dehors ---
    window.addEventListener('click', function(event) {
        let clickedOnFlightsInput = travelersInputFlights ? travelersInputFlights.contains(event.target) : false;
        let clickedOnHotelsInput = guestsRoomsInputHotel ? guestsRoomsInputHotel.contains(event.target) : false;

        if (travelersPopupFlights && travelersPopupFlights.classList.contains('show') &&
            !travelersPopupFlights.contains(event.target) && !clickedOnFlightsInput) {
            travelersPopupFlights.classList.remove('show');
        }

        if (guestsRoomsPopupHotel && guestsRoomsPopupHotel.classList.contains('show') &&
            !guestsRoomsPopupHotel.contains(event.target) && !clickedOnHotelsInput) {
            guestsRoomsPopupHotel.classList.remove('show');
        }
    });
});