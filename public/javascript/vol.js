document.addEventListener('DOMContentLoaded', function() {
    // --- Main UI Element Cache (Consolidated) ---
    const mainHeading = document.querySelector('.main-heading');
    const topNavButtons = document.querySelectorAll('.top-navigation .nav-button');
    const searchFormContents = document.querySelectorAll('.search-form-content');
    const formLoader = document.getElementById('form-loader');
    const resultsLoader = document.getElementById('results-loader');
    const resultsArea = document.getElementById('results-area');
    const specificResultsContents = document.querySelectorAll('.search-results-specific-content');

    const fromInput = document.getElementById('from');
    const toInput = document.getElementById('to');
    const swapButtons = document.querySelectorAll('.swap-button');

    const passengersInput = document.getElementById('passengers-input');
    const passengersPopover = document.getElementById('passengers-popover');
    const popoverDoneBtn = document.getElementById('popover-done-btn');
    const adultsCountEl = document.getElementById('adults-count');
    const childrenCountEl = document.getElementById('children-count');
    const cabinClassSelect = document.getElementById('cabin-class-select');

    const roundTripRadio = document.getElementById('round-trip');
    const oneWayRadio = document.getElementById('one-way');
    const multiDestRadio = document.getElementById('multi-destination');
    const returnDateInput = document.getElementById('return-date');
    const formOptionsCheckboxes = document.getElementById('form-options-checkboxes');

    const searchFlightsBtn = document.getElementById('search-flights-btn');
    // const searchHotelsBtn = document.getElementById('search-hotels-btn'); // Uncomment if used
    // const searchCarsBtn = document.getElementById('search-cars-btn');     // Uncomment if used

    const departDateEl = document.getElementById('depart-date');
    const checkinDateEl = document.getElementById('checkin-date');
    const checkoutDateEl = document.getElementById('checkout-date');
    const pickupDateEl = document.getElementById('pickup-date');
    const dropoffDateEl = document.getElementById('dropoff-date');

    const summaryFilterBtns = document.querySelectorAll('#flights-results-content .summary-filter-btn');
    const hotelTopFilterBtns = document.querySelectorAll('#hotels-results-content .hotel-top-filters button');
    const favoriteIcons = document.querySelectorAll('.favorite-icon-modern, .hotel-card-favorite, .car-card-favorite');

    const filterStopsDirect = document.getElementById('filter-stops-direct');
    const filterStops1Stop = document.getElementById('filter-stops-1stop');
    const filterStops2Plus = document.getElementById('filter-stops-2plusstops');
    
    let allFlightOffers = [];
    const flightCardsContainer = document.getElementById('flight-cards-container');
    const resultsCountEl = document.querySelector('#flights-results-content .results-count');

    // --- MIRABOT CHATBOT UI ELEMENTS (Consolidated) ---
    const askMiraBotButton = document.getElementById('ask-mirabot-btn');
    const miraBotChatWidget = document.getElementById('mirabot-chat-widget');
    const miraBotChatCloseButton = document.getElementById('mirabot-chat-close-btn');
    const miraBotChatInput = document.getElementById('mirabot-chat-input');
    const miraBotSendButton = document.getElementById('mirabot-chat-send-btn');
    const miraBotChatBody = document.querySelector('#mirabot-chat-widget .mirabot-chat-body');
    const miraBotMicButton = document.getElementById('mirabot-chat-mic-btn'); // Ensure this is declared

    // --- MIRABOT CHATBOT FUNCTIONALITY (Original Structure with Additions) ---
    if (askMiraBotButton && miraBotChatWidget && miraBotChatCloseButton && miraBotChatInput && miraBotSendButton && miraBotChatBody) {
        
        // --- MIRABOT CHATBOT CORE FUNCTIONS (Defined inside this 'if' block as per your original structure) ---
        function addMessageToMiraBotChat(message, sender) {
            // Ensure miraBotChatBody is still valid (it should be, given the outer 'if')
            if (!miraBotChatBody) { 
                console.error("MiraBot chat body not available when trying to add message.");
                return; 
            }

            const messageDiv = document.createElement('div');
            messageDiv.classList.add('mirabot-chat-message');
            
            const tempDiv = document.createElement('div');
            tempDiv.textContent = message;
            let sanitizedMessage = tempDiv.innerHTML.replace(/\n/g, '<br>');

            if (sender === 'user') {
                messageDiv.classList.add('mirabot-chat-message-user');
                messageDiv.innerHTML = `<div class="mirabot-message-content user"><p>${sanitizedMessage}</p></div>`;
            } else if (sender === 'assistant-thinking') {
                messageDiv.classList.add('mirabot-chat-message-assistant', 'mirabot-chat-message-assistant-thinking');
                messageDiv.innerHTML = `
                    <span class="mirabot-message-avatar"><i class="fa-solid fa-fan"></i></span> 
                    <div class="mirabot-message-content"><p>${sanitizedMessage}</p></div>`;
            } else { // assistant (final reply)
                messageDiv.classList.add('mirabot-chat-message-assistant');
                messageDiv.innerHTML = `
                    <span class="mirabot-message-avatar"><i class="fa-solid fa-fan"></i></span>
                    <div class="mirabot-message-content"><p>${sanitizedMessage}</p></div>`;
            }
            miraBotChatBody.appendChild(messageDiv);
            miraBotChatBody.scrollTop = miraBotChatBody.scrollHeight;
        }
        
        async function handleMiraBotSendMessage() {
            // Ensure elements are still valid
            if (!miraBotChatInput || !miraBotChatBody) {
                 console.error("MiraBot chat input or body not available for sending message.");
                 return;
            }
            const userMessage = miraBotChatInput.value.trim();
            if (!userMessage) return;

            addMessageToMiraBotChat(userMessage, 'user');
            miraBotChatInput.value = '';
            miraBotChatInput.style.height = 'auto';
            miraBotChatInput.focus();

            addMessageToMiraBotChat('MiraBot is thinking...', 'assistant-thinking');

            try {
                const response = await fetch('/api/chatbot/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: userMessage })
                });
                
                const thinkingMessage = miraBotChatBody.querySelector('.mirabot-chat-message-assistant-thinking');
                if(thinkingMessage) thinkingMessage.remove();

                if (!response.ok) {
                    let errorMsg = `HTTP error! Status: ${response.status}`;
                    try {
                        const errorData = await response.json();
                        errorMsg = errorData.reply || `Error: ${response.statusText}`;
                    } catch (e) { 
                        errorMsg = `Error: ${response.statusText} (Could not parse error response)`;
                    }
                    throw new Error(errorMsg);
                }

                const data = await response.json();
                const botReply = data.reply || "Sorry, I didn't get a valid response.";
                
                // 1. Write MiraBot's response to the chat window
                addMessageToMiraBotChat(botReply, 'assistant');
                
                // 2. Speak MiraBot's response (TTS Integration)
                if (botReply && 'speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(botReply);
                    utterance.lang = 'fr-FR'; // ** IMPORTANT: Set to MiraBot's response language **

                    const trySpeak = () => {
                        const voices = window.speechSynthesis.getVoices();
                        if (voices.length > 0) {
                            const desiredVoice = voices.find(voice => voice.lang === utterance.lang);
                            if (desiredVoice) utterance.voice = desiredVoice;
                            window.speechSynthesis.speak(utterance);
                            if (window.speechSynthesis.onvoiceschanged === trySpeakWithListener) {
                                window.speechSynthesis.onvoiceschanged = null;
                            }
                        } else {
                           // If voices are not loaded yet, the browser will likely use a default
                           // or wait for the onvoiceschanged listener.
                           window.speechSynthesis.speak(utterance); // Attempt with default
                        }
                    };
                     const trySpeakWithListener = () => { trySpeak(); };

                    if (window.speechSynthesis.getVoices().length === 0) {
                        window.speechSynthesis.onvoiceschanged = trySpeakWithListener;
                        // Some browsers might need this initial speak call if onvoiceschanged is slow
                        // or if default voice is acceptable. Test what works best.
                         window.speechSynthesis.speak(utterance); 
                    } else {
                        trySpeak();
                    }
                } else if (botReply && !('speechSynthesis' in window)) {
                    console.warn('Speech Synthesis API not supported. MiraBot cannot speak.');
                }

            } catch (error) {
                console.error('Error sending/receiving message from MiraBot:', error);
                const thinkingMessageOnError = miraBotChatBody.querySelector('.mirabot-chat-message-assistant-thinking');
                if(thinkingMessageOnError) thinkingMessageOnError.remove();
                addMessageToMiraBotChat(`Sorry, I encountered an error: ${error.message}. Please try again.`, 'assistant');
            }
        }

        // --- MIRABOT CHATBOT UI EVENT LISTENERS (Original Text Input Part) ---
        askMiraBotButton.addEventListener('click', function () {
            miraBotChatWidget.classList.toggle('active');
            if (miraBotChatWidget.classList.contains('active')) {
                miraBotChatInput.focus();
            }
        });

        miraBotChatCloseButton.addEventListener('click', function () {
            miraBotChatWidget.classList.remove('active');
        });

        miraBotChatInput.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && miraBotChatWidget.classList.contains('active')) {
                miraBotChatWidget.classList.remove('active');
            }
        });
        
        miraBotSendButton.addEventListener('click', handleMiraBotSendMessage);

        miraBotChatInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                handleMiraBotSendMessage();
            }
        });

        // --- MIRABOT SPEECH RECOGNITION FUNCTIONALITY (Integrated Here) ---
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        let recognition; // Declare here, initialize if API supported

        if (SpeechRecognition && miraBotMicButton) { // Check for miraBotMicButton as well
            try {
                recognition = new SpeechRecognition();
                recognition.continuous = false;
                recognition.lang = 'fr-FR'; // ** IMPORTANT: Set to user's speaking language **
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                let isRecording = false;

                miraBotMicButton.addEventListener('click', () => {
                    if (!recognition) {
                        console.error("Speech recognition not initialized properly.");
                        return;
                    }
                    if (!isRecording) {
                        try {
                            recognition.start();
                            // UI changes and isRecording = true will be handled in onstart
                        } catch (e) {
                            console.error("Error trying to start speech recognition:", e);
                            isRecording = false; // Reset state
                            if (miraBotMicButton.querySelector('i')) {
                                miraBotMicButton.classList.remove('recording');
                                miraBotMicButton.querySelector('i').classList.remove('fa-stop');
                                miraBotMicButton.querySelector('i').classList.add('fa-microphone');
                            }
                            if (miraBotChatInput) miraBotChatInput.placeholder = "Écrivez votre question";
                            alert("Could not start voice recognition. Please ensure microphone permission is granted and no other app is using the mic.");
                        }
                    } else {
                        recognition.stop();
                        // UI changes and isRecording = false will be handled in onend
                    }
                });

                recognition.onstart = function() {
                    isRecording = true;
                    if (miraBotMicButton.querySelector('i')) {
                        miraBotMicButton.classList.add('recording');
                        miraBotMicButton.querySelector('i').classList.remove('fa-microphone');
                        miraBotMicButton.querySelector('i').classList.add('fa-stop');
                    }
                    if (miraBotChatInput) miraBotChatInput.placeholder = "Listening...";
                    console.log("Voice recognition active.");
                };

                recognition.onresult = function(event) {
                    const speechResult = event.results[0][0].transcript;
                    console.log('Speech received: ' + speechResult);
                    if (miraBotChatInput) miraBotChatInput.value = speechResult;
                    handleMiraBotSendMessage(); // Call the existing send message function
                };

                recognition.onspeechend = function() {
                    console.log("User stopped speaking.");
                    // recognition.stop(); // Often good to let onend handle the final stop
                };

                recognition.onend = function() {
                    console.log("Voice recognition ended.");
                    isRecording = false;
                    if (miraBotMicButton.querySelector('i')) {
                        miraBotMicButton.classList.remove('recording');
                        miraBotMicButton.querySelector('i').classList.remove('fa-stop');
                        miraBotMicButton.querySelector('i').classList.add('fa-microphone');
                    }
                    if (miraBotChatInput) miraBotChatInput.placeholder = "Écrivez votre question";
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error, event.message);
                    let errorMessage = 'Speech recognition error: ' + event.error;
                    if (event.error === 'no-speech') {
                        errorMessage = 'No speech was detected. Please try again.';
                    } else if (event.error === 'audio-capture') {
                        errorMessage = 'Microphone problem. Ensure it is enabled and permission is granted.';
                        alert(errorMessage);
                    } else if (event.error === 'not-allowed') {
                        errorMessage = 'Permission to use microphone was denied. Please enable it in your browser settings for this site.';
                        alert(errorMessage);
                    } else if (event.error === 'network') {
                        errorMessage = 'A network error occurred. Please check your connection.';
                        addMessageToMiraBotChat(errorMessage, 'assistant'); // Show in chat
                    }
                    // The onend event usually fires after an error, resetting the UI.
                };
            } catch (e) {
                console.error("Failed to initialize SpeechRecognition object:", e);
                if (miraBotMicButton) {
                    miraBotMicButton.disabled = true;
                    miraBotMicButton.title = "Voice input initialization failed.";
                }
            }
        } else {
            if (!SpeechRecognition) console.warn('Speech Recognition API not supported by this browser.');
            if (miraBotMicButton) {
                miraBotMicButton.disabled = true;
                miraBotMicButton.title = "Voice input not supported or mic button missing.";
                if(!miraBotMicButton) console.warn("MiraBot microphone button (mirabot-chat-mic-btn) not found.");
            }
        }
    // End of the main 'if (askMiraBotButton && ...)' block
    } else { 
        console.warn("Core MiraBot UI elements not found. Full chatbot functionality, including voice, will be unavailable.");
    }


    // --- YOUR ORIGINAL PAGE-SPECIFIC JAVASCRIPT FUNCTIONS AND EVENT LISTENERS ---
    // (All the code from "// --- UI Helper: Prepare Results Area ---" down to the end of your file)

    // --- UI Helper: Prepare Results Area ---
    function prepareResultsAreaForSearch(searchTypeString) {
        if (specificResultsContents) {
            specificResultsContents.forEach(el => {
                if (!el.classList.contains('d-none')) {
                    el.classList.add('d-none');
                }
            });
        }
        if (resultsLoader) resultsLoader.classList.remove('d-none');
        if (formLoader) formLoader.classList.add('d-none');
    }

    // --- Event Listeners for Top Navigation ---
    if (topNavButtons.length > 0) {
        topNavButtons.forEach(button => {
            button.addEventListener('click', function() {
                topNavButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                if (mainHeading) mainHeading.textContent = this.dataset.heading;

                const targetFormId = this.dataset.form;
                if (searchFormContents) {
                    searchFormContents.forEach(form => {
                        form.classList.toggle('d-none', form.id !== targetFormId);
                    });
                }
                
                if (specificResultsContents) specificResultsContents.forEach(el => el.classList.add('d-none'));
                if (resultsLoader) resultsLoader.classList.add('d-none');

                const searchFormWrapper = document.querySelector('.search-form-wrapper');
                if(searchFormWrapper && searchFormWrapper.scrollIntoView) {
                    searchFormWrapper.scrollIntoView({ behavior: 'smooth', block: 'start'});
                }
            });
        });
    }

    // --- Swap Button Logic ---
    if (swapButtons.length && fromInput && toInput) {
        swapButtons.forEach(button => {
            button.addEventListener('click', () => {
                [fromInput.value, toInput.value] = [toInput.value, fromInput.value];
            });
        });
    }

    // --- Passengers Popover Logic ---
    if (passengersInput && passengersPopover && popoverDoneBtn && adultsCountEl && childrenCountEl && cabinClassSelect) {
        let adults = 1; let children = 0;
        if (adultsCountEl) adultsCountEl.textContent = adults; // Check if element exists
        if (childrenCountEl) childrenCountEl.textContent = children; // Check

        passengersInput.addEventListener('click', (event) => { 
            event.stopPropagation(); 
            passengersPopover.classList.toggle('d-none'); 
        });

        const adultsPlusBtn = document.getElementById('adults-plus');
        const adultsMinusBtn = document.getElementById('adults-minus');
        const childrenPlusBtn = document.getElementById('children-plus');
        const childrenMinusBtn = document.getElementById('children-minus');

        if(adultsPlusBtn && adultsCountEl) adultsPlusBtn.addEventListener('click', () => { adults++; adultsCountEl.textContent = adults; });
        if(adultsMinusBtn && adultsCountEl) adultsMinusBtn.addEventListener('click', () => { if (adults > 1) { adults--; adultsCountEl.textContent = adults; }});
        if(childrenPlusBtn && childrenCountEl) childrenPlusBtn.addEventListener('click', () => { children++; childrenCountEl.textContent = children; });
        if(childrenMinusBtn && childrenCountEl) childrenMinusBtn.addEventListener('click', () => { if (children > 0) { children--; childrenCountEl.textContent = children; }});
        
        popoverDoneBtn.addEventListener('click', () => {
            const selClassOpt = cabinClassSelect.options[cabinClassSelect.selectedIndex];
            const selClassTxt = selClassOpt ? selClassOpt.text : "Économie";
            let pTxt = `${adults} adulte${adults > 1 ? 's' : ''}`;
            if (children > 0) { pTxt += `, ${children} enfant${children > 1 ? 's' : ''}`; }
            pTxt += `, ${selClassTxt}`;
            passengersInput.value = pTxt;
            passengersPopover.classList.add('d-none');
        });
        document.addEventListener('click', (event) => {
            if (passengersPopover && !passengersPopover.classList.contains('d-none') && 
                passengersInput && !passengersInput.contains(event.target) && 
                !passengersPopover.contains(event.target)) {
                passengersPopover.classList.add('d-none');
            }
        });
    }

    // --- Flight Trip Type Logic ---
    function updateFormForFlightTripType() {
        if (!oneWayRadio || !roundTripRadio || !returnDateInput) {
            return;
        }
        const isOneWay = oneWayRadio.checked;
        const isMultiDest = multiDestRadio ? multiDestRadio.checked : false;
        const shouldDisableReturnDate = isOneWay || isMultiDest;

        returnDateInput.disabled = shouldDisableReturnDate;
        if (shouldDisableReturnDate) returnDateInput.value = '';

        const returnDateGroup = returnDateInput.closest('.input-group.dates');
        if (returnDateGroup) {
            returnDateGroup.style.opacity = shouldDisableReturnDate ? '0.5' : '1';
            returnDateGroup.style.pointerEvents = shouldDisableReturnDate ? 'none' : 'auto';
        }

        if (formOptionsCheckboxes) formOptionsCheckboxes.classList.toggle('d-none', isMultiDest);
    }

    const tripTypeRadios = [oneWayRadio, roundTripRadio, multiDestRadio].filter(Boolean);
    if (tripTypeRadios.length > 0 && returnDateInput) {
        tripTypeRadios.forEach(radio => {
            if (radio) radio.addEventListener('change', updateFormForFlightTripType);
        });
        updateFormForFlightTripType(); 
    }

    // --- Flight Search Button Handler (Main Page Search) ---
    if (searchFlightsBtn) {
        searchFlightsBtn.addEventListener('click', async () => {
            if (typeof prepareResultsAreaForSearch === "function") prepareResultsAreaForSearch('flights'); 
            if (flightCardsContainer) flightCardsContainer.innerHTML = ''; 
            if(resultsCountEl) resultsCountEl.textContent = 'Recherche en cours...';

            const origin = fromInput ? fromInput.value : null;
            const destination = toInput ? toInput.value : null;
            const departureDate = departDateEl ? departDateEl.value : null;
            const isOneWayFlight = oneWayRadio ? oneWayRadio.checked : false;
            let returnDateValue = null;
            if (!isOneWayFlight && returnDateInput && !returnDateInput.disabled) {
                returnDateValue = returnDateInput.value;
            }
            const adults = adultsCountEl ? (parseInt(adultsCountEl.textContent, 10) || 1) : 1;

            if (!origin || !destination || !departureDate) {
                alert('Veuillez sélectionner une origine, une destination et une date de départ.');
                if (resultsLoader) resultsLoader.classList.add('d-none');
                if (resultsCountEl) resultsCountEl.textContent = 'Veuillez compléter votre recherche.';
                return;
            }
            if (!isOneWayFlight && !returnDateValue) {
                alert('Veuillez sélectionner une date de retour pour un vol aller-retour.');
                if (resultsLoader) resultsLoader.classList.add('d-none');
                if (resultsCountEl) resultsCountEl.textContent = 'Date de retour manquante.';
                return;
            }

            const params = new URLSearchParams({
                origin: origin,
                destination: destination,
                departureDate: departureDate,
                adults: adults.toString()
            });
            if (returnDateValue) params.append('returnDate', returnDateValue);
            
            const apiUrl = `/api/search-flights?${params.toString()}`;

            try {
                const response = await fetch(apiUrl);
                const resultsData = await response.json(); 

                if (resultsLoader) resultsLoader.classList.add('d-none');
                const targetResultsContentElement = document.getElementById('flights-results-content');

                if (response.ok && resultsData && !resultsData.error && Array.isArray(resultsData) && resultsData.length > 0) {
                    allFlightOffers = resultsData;
                    if (typeof applyFlightFiltersAndRender === "function") applyFlightFiltersAndRender(); 
                    if (targetResultsContentElement) targetResultsContentElement.classList.remove('d-none');
                } else {
                    allFlightOffers = [];
                    if(resultsCountEl) resultsCountEl.textContent = '0 résultat';
                    const errorMessage = resultsData.error ? (resultsData.details || resultsData.error) : 'Aucun vol trouvé pour ces critères.';
                    if (flightCardsContainer) flightCardsContainer.innerHTML = `<p style="text-align:center; padding: 20px;">${errorMessage}</p>`;
                    if (targetResultsContentElement) targetResultsContentElement.classList.remove('d-none'); 
                    if (resultsData.error) console.error('API Error:', resultsData.details || resultsData.error);
                }
            } catch (error) {
                allFlightOffers = [];
                console.error('Failed to fetch flight offers:', error);
                if (resultsLoader) resultsLoader.classList.add('d-none');
                if (resultsCountEl) resultsCountEl.textContent = 'Erreur';
                if (flightCardsContainer) flightCardsContainer.innerHTML = `<p style="text-align:center; padding: 20px;">Une erreur est survenue lors de la recherche. Veuillez réessayer.</p>`;
                const targetResultsContentElement = document.getElementById('flights-results-content');
                if (targetResultsContentElement) targetResultsContentElement.classList.remove('d-none');
            }
            
            if (resultsArea && resultsArea.scrollIntoView) {
                resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- Function to Apply Filters and Render Flight Cards ---
    function applyFlightFiltersAndRender() {
        if (!allFlightOffers || !Array.isArray(allFlightOffers)) { // Ensure allFlightOffers is an array
            if(resultsCountEl) resultsCountEl.textContent = '0 résultat';
            if (flightCardsContainer) flightCardsContainer.innerHTML = '<p style="text-align:center; padding: 20px;">Aucune donnée de vol à filtrer.</p>';
            return;
        }

        let filteredOffers = allFlightOffers.filter(offer => {
            let stopsMatch = false;
            const outboundSegments = offer.outbound && offer.outbound.segments ? offer.outbound.segments.length : 0;
            const isDirectOffer = outboundSegments === 1;

            const directChecked = filterStopsDirect ? filterStopsDirect.checked : false;
            const oneStopChecked = filterStops1Stop ? filterStops1Stop.checked : false;
            const twoPlusStopsChecked = filterStops2Plus ? filterStops2Plus.checked : false;
            
            const noStopFilterSelected = !directChecked && !oneStopChecked && !twoPlusStopsChecked;

            if (noStopFilterSelected || (directChecked && oneStopChecked && twoPlusStopsChecked) ) {
                stopsMatch = true;
            } else {
                if (directChecked && isDirectOffer) stopsMatch = true;
                if (oneStopChecked && outboundSegments === 2) stopsMatch = true;
                if (twoPlusStopsChecked && outboundSegments > 2) stopsMatch = true;
            }
            if (!stopsMatch) return false;
            return true;
        });

        if (resultsCountEl) resultsCountEl.textContent = `${filteredOffers.length} résultat(s) affiché(s)`;
        if (typeof renderFlightCards === "function") renderFlightCards(filteredOffers, flightCardsContainer);
    }

    // --- Add Event Listeners to Flight Filter Inputs ---
    if (filterStopsDirect) filterStopsDirect.addEventListener('change', applyFlightFiltersAndRender);
    if (filterStops1Stop) filterStops1Stop.addEventListener('change', applyFlightFiltersAndRender);
    if (filterStops2Plus) filterStops2Plus.addEventListener('change', applyFlightFiltersAndRender);

    // --- Helper Function to Render Flight Cards ---
    function renderFlightCards(offers, container) {
        if (!container) return;
        container.innerHTML = ''; 

        if (!offers || offers.length === 0) {
            container.innerHTML = '<p style="text-align:center; padding: 20px;">Aucune offre de vol disponible pour ces filtres.</p>';
            return;
        }

        offers.forEach(offer => {
            let outboundHtml = '';
            if (offer.outbound && offer.outbound.segments && offer.outbound.segments.length > 0) {
                const firstOutSegment = offer.outbound.segments[0];
                const lastOutSegment = offer.outbound.segments[offer.outbound.segments.length - 1];
                const airlineName = firstOutSegment.carrier || 'Compagnie';
                const airlineLogoUrl = `https://logo.clearbit.com/${airlineName.toLowerCase().replace(/[^a-z0-9]/gi, '').replace(/\s+/g, '')}.com`;

                outboundHtml = `
                    <div class="flight-segment-modern">
                        <span class="airline-logo-modern"><img src="${airlineLogoUrl}" alt="${airlineName}" onerror="this.style.display='none'; this.parentElement.innerHTML='${airlineName.substring(0,10)}';"></span>
                        <div class="time-airport-group">
                            <span class="time-modern">${new Date(firstOutSegment.departureTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})}</span>
                            <span class="airport-code-modern">${firstOutSegment.departureAirport}</span>
                        </div>
                        <div class="journey-info-modern">
                            <span class="duration-modern">${formatDuration(offer.outbound.duration)}</span>
                            <div class="journey-line">${generateJourneyLine(offer.outbound.segments.length)}</div>
                            <span class="stops-modern">${offer.outbound.stopsDisplay || ((offer.outbound.segments.length -1) < 0 ? 'Direct' : (offer.outbound.segments.length -1) + ' escale(s)')}</span>
                        </div>
                        <div class="time-airport-group" style="text-align: right;">
                            <span class="time-modern">${new Date(lastOutSegment.arrivalTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})}</span>
                            <span class="airport-code-modern">${lastOutSegment.arrivalAirport}</span>
                        </div>
                    </div>`;
            }

            let inboundHtml = '';
            if (offer.inbound && offer.inbound.segments && offer.inbound.segments.length > 0) {
                const firstInSegment = offer.inbound.segments[0];
                const lastInSegment = offer.inbound.segments[offer.inbound.segments.length - 1];
                const airlineNameIn = firstInSegment.carrier || 'Compagnie';
                const airlineLogoUrlIn = `https://logo.clearbit.com/${airlineNameIn.toLowerCase().replace(/[^a-z0-9]/gi, '').replace(/\s+/g, '')}.com`;

                inboundHtml = `
                    <div class="flight-segment-modern">
                        <span class="airline-logo-modern"><img src="${airlineLogoUrlIn}" alt="${airlineNameIn}" onerror="this.style.display='none'; this.parentElement.innerHTML='${airlineNameIn.substring(0,10)}';"></span>
                        <div class="time-airport-group">
                            <span class="time-modern">${new Date(firstInSegment.departureTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})}</span>
                            <span class="airport-code-modern">${firstInSegment.departureAirport}</span>
                        </div>
                        <div class="journey-info-modern">
                            <span class="duration-modern">${formatDuration(offer.inbound.duration)}</span>
                            <div class="journey-line">${generateJourneyLine(offer.inbound.segments.length)}</div>
                            <span class="stops-modern">${offer.inbound.stopsDisplay || ((offer.inbound.segments.length -1) < 0 ? 'Direct' : (offer.inbound.segments.length -1) + ' escale(s)')}</span>
                        </div>
                        <div class="time-airport-group" style="text-align: right;">
                            <span class="time-modern">${new Date(lastInSegment.arrivalTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false})}</span>
                            <span class="airport-code-modern">${lastInSegment.arrivalAirport}</span>
                        </div>
                    </div>`;
            }

            const cardHtml = `
                <article class="flight-card-modern" data-flight-id="${offer.id || ''}">
                    <div class="flight-details-modern">
                        ${outboundHtml}
                        ${inboundHtml}
                    </div>
                    <div class="flight-action-modern">
                        <span class="favorite-icon-modern" role="button" data-flight-id="${offer.id || ''}">♡</span>
                        <div class="offers-price-modern">
                            <span class="price-modern">${parseFloat(offer.price || 0).toFixed(2)} ${offer.currency || 'USD'}</span>
                        </div>
                        <button class="view-btn-modern">Voir l'offre <span class="arrow-icon">→</span></button>
                        <div class="flight-meta-info-modern">
                            <span><i class="fas fa-suitcase icon"></i> Bagages à vérifier</span>
                        </div>
                    </div>
                </article>`;
            if (container) container.insertAdjacentHTML('beforeend', cardHtml);
        });
        
        if (container) {
            container.querySelectorAll('.favorite-icon-modern').forEach(icon => {
                if (!icon.dataset.listenerAttachedF) { 
                    icon.addEventListener('click', function(e) {
                        e.stopPropagation();
                        this.classList.toggle('active');
                        this.innerHTML = this.classList.contains('active') ? '<i class="fas fa-heart" style="color: #E53E3E;"></i>' : '♡';
                    });
                    icon.dataset.listenerAttachedF = 'true';
                }
            });
        }
    }

    // --- Helper: Format Duration ---
    function formatDuration(isoDuration) { 
        if (!isoDuration || typeof isoDuration !== 'string') return 'N/A';
        let T_index = isoDuration.indexOf('T');
        if (T_index === -1 && isoDuration.startsWith('P')) T_index = isoDuration.indexOf('P');
        else if (T_index === -1) return isoDuration; 

        let durationPart = isoDuration.substring(T_index + 1);
        let hours = 0, minutes = 0;       
        const hourMatch = durationPart.match(/(\d+)H/);
        if (hourMatch) hours = parseInt(hourMatch[1]);       
        const minuteMatch = durationPart.match(/(\d+)M/);
        if (minuteMatch) minutes = parseInt(minuteMatch[1]);
        if (hours === 0 && minutes === 0 && durationPart.includes('S')) {
             const secondMatch = durationPart.match(/(\d+)S/);
             if (secondMatch && parseInt(secondMatch[1]) > 0) return "<1m";
        }
        return `${hours}h ${minutes}m`;
    }

    // --- Helper: Generate Journey Line ---
    function generateJourneyLine(segmentCount) { 
        if (segmentCount <= 0) return '<span class="line"></span>';
        if (segmentCount === 1) { 
            return '<span class="dot"></span><span class="line"></span><span class="plane-icon">✈</span><span class="line"></span><span class="dot"></span>';
        }
        let lineHtml = '<span class="dot"></span><span class="line"></span><span class="plane-icon">✈</span>';
        for (let i = 1; i < segmentCount; i++) { 
            lineHtml += '<span class="line"></span><span class="dot accent"></span><span class="line"></span><span class="plane-icon">✈</span>';
        }
        lineHtml += '<span class="line"></span><span class="dot"></span>';
        return lineHtml;
    }

    // --- Other Event Listeners (from your original code) ---
    if (summaryFilterBtns.length > 0) {
        summaryFilterBtns.forEach(button => {
            button.addEventListener('click', function() {
                summaryFilterBtns.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
    if (hotelTopFilterBtns.length > 0) {
        hotelTopFilterBtns.forEach(button => {
            button.addEventListener('click', function() {
                hotelTopFilterBtns.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
    if (favoriteIcons.length > 0) {
        favoriteIcons.forEach(icon => {
            if (!icon.dataset.listenerAttachedI) {
                icon.addEventListener('click', function(e) {
                    e.stopPropagation(); 
                    this.classList.toggle('active');
                    this.innerHTML = this.classList.contains('active') ? '<i class="fas fa-heart" style="color: #E53E3E;"></i>' : '♡';
                });
                icon.dataset.listenerAttachedI = 'true';
            }
        });
    }
    
    // --- Date Picker Min Date Logic (from your original code) ---
    const today = new Date().toISOString().split('T')[0];
    function setMinDates(dateEl, relatedDateEl = null, isStartDate = true) { 
        if (!dateEl) return;
        dateEl.min = today;
        
        if(!dateEl.value && isStartDate && dateEl.id !== 'return-date' && dateEl.id !== 'checkout-date' && dateEl.id !== 'dropoff-date') {
            dateEl.value = today;
        }
        if(!dateEl.value && !isStartDate && relatedDateEl && relatedDateEl.value) {
            let tempDate = new Date(relatedDateEl.value);
            tempDate.setDate(tempDate.getDate() + (dateEl.id === 'checkout-date' || dateEl.id === 'dropoff-date' ? 1 : 7));
            if (new Date(tempDate.toISOString().split('T')[0]) < new Date(relatedDateEl.value)) {
                 tempDate = new Date(relatedDateEl.value);
                 tempDate.setDate(tempDate.getDate() +1);
            }
            if (new Date(tempDate.toISOString().split('T')[0]) < new Date(today)) {
                tempDate = new Date(today); 
                if(relatedDateEl.value === today) tempDate.setDate(tempDate.getDate() + 1);
            }
            dateEl.value = tempDate.toISOString().split('T')[0];
        } else if (!dateEl.value && !isStartDate && !relatedDateEl) {
            let tempDate = new Date(today);
            tempDate.setDate(tempDate.getDate() + (dateEl.id === 'checkout-date' || dateEl.id === 'dropoff-date' ? 1 : 7));
            dateEl.value = tempDate.toISOString().split('T')[0];
        }

        dateEl.addEventListener('change', function() {
            if (relatedDateEl) {
                if (isStartDate) {
                    relatedDateEl.min = this.value || today;
                    if (relatedDateEl.value && this.value && new Date(relatedDateEl.value) < new Date(this.value)) {
                        relatedDateEl.value = this.value;
                    }
                }
            }
        });
    }

    if(departDateEl && returnDateInput) setMinDates(departDateEl, returnDateInput, true); 
    if(returnDateInput && departDateEl) setMinDates(returnDateInput, departDateEl, false); // Ensure departDateEl exists
    if(checkinDateEl && checkoutDateEl) setMinDates(checkinDateEl, checkoutDateEl, true); 
    if(checkoutDateEl && checkinDateEl) setMinDates(checkoutDateEl, checkinDateEl, false); // Ensure checkinDateEl exists
    if(pickupDateEl && dropoffDateEl) setMinDates(pickupDateEl, dropoffDateEl, true); 
    if(dropoffDateEl && pickupDateEl) setMinDates(dropoffDateEl, pickupDateEl, false); // Ensure pickupDateEl exists

    // Initial call for flight form trip type logic (if elements exist)
    if (typeof updateFormForFlightTripType === "function" && oneWayRadio && roundTripRadio && returnDateInput) {
         updateFormForFlightTripType();
    }
});