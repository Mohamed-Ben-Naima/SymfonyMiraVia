<?php

namespace App\Service;

use App\Service\AmadeusApiService; // Corrected namespace
use App\Repository\EvenementsRepository;
use App\Repository\PartenairesRepository;
use App\Repository\DemandesAideRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class GeminiService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $geminiApiUrl;
    private AmadeusApiService $amadeusApiService;
    private LoggerInterface $logger;
    private EvenementsRepository $evenementsRepository;
    private ?PartenairesRepository $partenairesRepository;
    private ?DemandesAideRepository $demandesAideRepository;

    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $params,
        AmadeusApiService $amadeusApiService,
        LoggerInterface $logger,
        EvenementsRepository $evenementsRepository,
        ?PartenairesRepository $partenairesRepository = null,
        ?DemandesAideRepository $demandesAideRepository = null
    ) {
        $this->client = $client;
        $this->apiKey = $params->get('gemini_api_key');
        $this->geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
        $this->amadeusApiService = $amadeusApiService;
        $this->logger = $logger;
        $this->evenementsRepository = $evenementsRepository;
        $this->partenairesRepository = $partenairesRepository;
        $this->demandesAideRepository = $demandesAideRepository;
    }

    private function callGeminiAPI(array $contents, ?array $generationConfig = null): ?array
    {
        $payload = ['contents' => $contents];
        if ($generationConfig) {
            $payload['generationConfig'] = $generationConfig;
        }

        try {
            $response = $this->client->request('POST', $this->geminiApiUrl . '?key=' . $this->apiKey, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                $errorContent = $response->getContent(false);
                $this->logger->error('Gemini API Error: Status {status}, Response: {response}', [
                    'status' => $response->getStatusCode(),
                    'response' => $errorContent
                ]);
                return null;
            }
            return $response->toArray();

        } catch (\Throwable $e) {
            $this->logger->error('Gemini API Request Exception: {message}, Trace: {trace}', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function getChatbotResponse(string $userQuery): string
    {
        $currentDate = date('Y-m-d');

        $intentPrompt = <<<PROMPT
        You are MiraBot, an advanced AI assistant for the Miravia travel agency. Your task is to understand the user's query and extract structured information to help with travel bookings and provide information about local events and partners. Today's date is {$currentDate}.

        Supported intents are: "search_flights", "search_events", "search_partners", "get_help_request_info", "general_travel_question".

        **Critical Instructions for Location Codes:**
        - For "search_flights", `originLocationCode` and `destinationLocationCode` MUST be valid 3-letter IATA airport codes (e.g., "TUN", "CDG", "MRS").
        - If the user provides a city name (e.g., "Tunis", "Paris", "Marseille"), try to map it to its main IATA airport code.
        - If the user provides a country name for a flight destination (e.g., "France", "Italy") but NOT a specific city, you MUST identify that a specific city is needed. Set the "destinationLocationCode" to the country name (e.g., "France") and add "destination_city_needed" to a "missing_info" object with the country name as value (e.g., "missing_info": {"destination_city_needed": "France"}).
        - For dates, if the year is not specified, infer it based on the current date ({$currentDate}) and the provided month/day, usually assuming the upcoming occurrence.

        **Intent: "search_flights"**
        Parameters:
        - originLocationCode (IATA airport code, e.g., "TUN")
        - destinationLocationCode (IATA airport code OR country name if city is missing, e.g., "MRS" or "France")
        - departureDate (YYYY-MM-DD)
        - returnDate (YYYY-MM-DD, optional)
        - adults (number, default 1)
        Example 1 (Specific cities): "je veux vol from tunise to marseille le 25/05/2025"
        Expected JSON: {"intent": "search_flights", "parameters": {"originLocationCode": "TUN", "destinationLocationCode": "MRS", "departureDate": "2025-05-25", "adults": 1}}
        Example 2 (Country as destination): "Flights from London to Germany next Friday for 2"
        Expected JSON: {"intent": "search_flights", "parameters": {"originLocationCode": "LHR", "destinationLocationCode": "Germany", "departureDate": "YYYY-MM-DD", "adults": 2}, "missing_info": {"destination_city_needed": "Germany"}}
        Example 3 (User query: "je veux vol from tunise to frensh le 25/05/2025")
        Expected JSON: {"intent": "search_flights", "parameters": {"originLocationCode": "TUN", "destinationLocationCode": "France", "departureDate": "2025-05-25", "adults": 1}, "missing_info": {"destination_city_needed": "France"}}

        **Intent: "search_events"**
        Parameters to search events from the Miravia database:
        - keywords (string, optional, e.g., "concert", "festival", name of the event)
        - location (string, optional, e.g., "Tunis", "Paris", a specific venue)
        - date (string, optional, YYYY-MM-DD, or relative like "today", "next week", "July")
        - status (string, optional, default "actif")
        If the user's query for events is very general (e.g., "show me events", "any events?") and does not provide any specific keywords, location, or date, include `needs_event_clarification: true` in your JSON output alongside the intent.
        Example: "any active festivals in Sousse next month?"
        Expected JSON: {"intent": "search_events", "parameters": {"keywords": "festivals", "location": "Sousse", "date": "YYYY-MM", "status": "actif"}}
        Example (Vague query): "Tell me about some events."
        Expected JSON: {"intent": "search_events", "parameters": {"status": "actif"}, "needs_event_clarification": true}

        **Intent: "search_partners"**
        Parameters to search partners from the Miravia database:
        - name (string, optional, name of the company)
        - activity_type (string, optional, e.g., "hotel", "restaurant")
        - location (string, optional)
        Example: "list hotel partners in Djerba"
        Expected JSON: {"intent": "search_partners", "parameters": {"activity_type": "hotel", "location": "Djerba"}}

        **Intent: "get_help_request_info"**
        Parameters to get info about a help request from the Miravia database:
        - request_id (integer or string, required)
        - client_id (integer, optional)
        Example: "status for my help request 789"
        Expected JSON: {"intent": "get_help_request_info", "parameters": {"request_id": "789"}}

        For general travel questions not fitting other intents, use "general_travel_question".
        User Query: "{$userQuery}"

        Respond ONLY with a valid JSON object containing "intent" and "parameters". If clarification is needed (like a specific city for a country, or more event details), include "missing_info" or "needs_event_clarification" flags.
        Example for general question: {"intent": "general_travel_question", "parameters": {"query": "What's the best time to visit Japan?"}}
        PROMPT;

        $geminiResponse = $this->callGeminiAPI([['parts' => [['text' => $intentPrompt]]]]);
        $extractedText = $geminiResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$extractedText) {
            $this->logger->warning("Gemini returned no extractable text for query: {$userQuery}");
            return "I'm having a bit of trouble understanding that. Could you try rephrasing?";
        }

        $jsonResponse = trim($extractedText);
        if (strpos($jsonResponse, '```json') === 0) {
            $jsonResponse = preg_replace('/^```json\s*|\s*```$/', '', $jsonResponse);
            $jsonResponse = trim($jsonResponse);
        }
        
        $this->logger->info("Gemini Extracted Intent JSON: " . $jsonResponse);
        $interpretation = json_decode($jsonResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($interpretation['intent'])) {
            $this->logger->error("Failed to decode Gemini's intent JSON or intent not set. Raw: {$extractedText}, Error: " . json_last_error_msg());
            $interpretation = ['intent' => 'general_travel_question', 'parameters' => ['query' => $userQuery]];
        }

        $intent = $interpretation['intent'];
        $params = $interpretation['parameters'] ?? [];
        $missingInfo = $interpretation['missing_info'] ?? [];
        $needsEventClarification = $interpretation['needs_event_clarification'] ?? false;

        if ($intent === 'search_flights' && isset($missingInfo['destination_city_needed'])) {
            $country = htmlspecialchars($missingInfo['destination_city_needed']);
            return "To search for flights to {$country}, I need a specific city. For example, if going to France, you could say Paris, Marseille, or Nice. Which city in {$country} are you interested in?";
        }
        
        if ($intent === 'search_events' && $needsEventClarification) {
            $hasSpecificEventCriteria = !empty($params['keywords']) || !empty($params['location']) || !empty($params['date']);
            if (!$hasSpecificEventCriteria) {
                return "I can certainly look for events for you! To help me find the best matches, could you tell me a bit more? For example:
- What **type of event** are you interested in (e.g., concert, festival, exhibition)?
- Is there a **specific location** you have in mind?
- Are you looking for events around a **particular date or timeframe**?";
            }
        }
        
        if (isset($interpretation['missing_info']) && is_array($interpretation['missing_info']) && !empty($interpretation['missing_info']) && 
            !isset($missingInfo['destination_city_needed']) ) {
            $missingFieldsList = [];
            foreach ($interpretation['missing_info'] as $key => $value) {
                $missingFieldsList[] = is_string($key) ? htmlspecialchars($key) : htmlspecialchars($value);
            }
            if (!empty($missingFieldsList)) {
                $fieldsString = implode(', ', $missingFieldsList);
                // Avoid returning this if it was just a flag like 'destination_city_needed' which is handled above.
                // The prompt for Gemini should be precise about what goes into missing_info.
                // For now, if it's not 'destination_city_needed', we'll show it.
                if ($fieldsString !== $missingInfo['destination_city_needed']){ // Basic check
                     return "I need a bit more information to proceed, such as: {$fieldsString}. Could you provide that?";
                }
            }
        }

        $apiData = null;
        $actionResult = "";
        $dataType = null; // To help format the final prompt ('flights', 'events', etc.)

        switch ($intent) {
            case 'search_flights':
                $dataType = 'flights';
                if (isset($params['originLocationCode'], $params['destinationLocationCode'], $params['departureDate']) &&
                    strlen($params['originLocationCode']) === 3 && strlen($params['destinationLocationCode']) === 3) {
                    
                    $this->logger->info('Attempting Amadeus flight search with params: ', $params);
                    $apiData = $this->amadeusApiService->searchFlights(
                        $params['originLocationCode'],
                        $params['destinationLocationCode'],
                        $params['departureDate'],
                        $params['returnDate'] ?? null,
                        (int)($params['adults'] ?? 1)
                    );
                    if (isset($apiData['error'])) {
                        $actionResult = "There was an issue searching for flights: " . $apiData['error'] . (isset($apiData['details']) ? " Details: ".json_encode($apiData['details']) : "");
                    } elseif (empty($apiData)) {
                        $actionResult = "I couldn't find any flights matching your criteria for {$params['originLocationCode']} to {$params['destinationLocationCode']}.";
                    } else {
                        $actionResult = "I found some flights for you from {$params['originLocationCode']} to {$params['destinationLocationCode']}.";
                    }
                } else {
                    $actionResult = "To search for flights, I need valid 3-letter IATA airport codes for both origin and destination, along with a departure date.";
                    $this->logger->warning('Flight search initiated without complete/valid IATA codes or date. Params:', $params);
                }
                break;

            case 'search_events':
                $dataType = 'events';
                $this->logger->info('Searching for events with params: ', $params);
                
                $eventCriteria = [
                    'keywords' => $params['keywords'] ?? null,
                    'location' => $params['location'] ?? null,
                    'date'     => $params['date'] ?? null,
                    'status'   => $params['status'] ?? 'actif',
                ];
                
                // TODO: Replace with a call to a proper search method in EvenementsRepository
                // $events = $this->evenementsRepository->searchByCriteria($eventCriteria);
                
                // Simplified findBy (placeholder - needs improvement in repository for keywords/location/date)
                $simpleCriteriaForFindBy = [];
                if (isset($eventCriteria['status'])) {
                    $simpleCriteriaForFindBy['status'] = trim($eventCriteria['status'], "'"); // Handle potential quotes if stored in DB
                }
                // This simplified findBy will not use keywords, partial location, or flexible date searches.
                // For a good user experience, a custom repository method is essential.
                $events = $this->evenementsRepository->findBy($simpleCriteriaForFindBy, ['dateEvenement' => 'ASC'], 5);

                if (empty($events)) {
                    $actionResult = "I couldn't find any events";
                    if ($eventCriteria['keywords']) $actionResult .= " related to '" . htmlspecialchars($eventCriteria['keywords']) . "'";
                    if ($eventCriteria['location']) $actionResult .= " in '" . htmlspecialchars($eventCriteria['location']) . "'";
                    $actionResult .= ".";
                } else {
                    $eventSummaries = [];
                    foreach ($events as $event) {
                        $summary = $event->getTitre() ?? 'Untitled Event';
                        if ($event->getDateEvenement()) {
                            $summary .= " on " . $event->getDateEvenement()->format('F j, Y'); // Example format
                        }
                        if ($event->getLieu()) {
                            $summary .= " at " . htmlspecialchars($event->getLieu());
                        }
                        $eventSummaries[] = $summary;
                    }
                    $apiData = $eventSummaries; // Store array of summary strings
                    $count = count($eventSummaries);
                    $actionResult = "I found {$count} event(s) that might interest you.";
                }
                break;

            case 'search_partners':
                $dataType = 'partners';
                if ($this->partenairesRepository) {
                    // Placeholder: Implement search logic using $this->partenairesRepository
                    // $partnerCriteria = ['activity_type' => $params['activity_type'] ?? null, 'location' => $params['location'] ?? null, 'name' => $params['name'] ?? null];
                    // $partners = $this->partenairesRepository->searchByCriteria($partnerCriteria); // Method to be created
                    // if (empty($partners)) { $actionResult = "No partners found matching your criteria."; }
                    // else { /* format $partners into $apiData and $actionResult */ }
                    $actionResult = "I'm still learning to search for partners. This feature will be fully available soon!";
                } else {
                    $actionResult = "Partner search functionality is not fully configured in my system yet.";
                     $this->logger->warning("PartenairesRepository not available for search_partners intent.");
                }
                break;

            case 'get_help_request_info':
                $dataType = 'help_request';
                if ($this->demandesAideRepository && isset($params['request_id'])) {
                    // Placeholder: Implement logic using $this->demandesAideRepository
                    // $request = $this->demandesAideRepository->find($params['request_id']);
                    // if (!$request) { $actionResult = "Could not find help request with ID " . htmlspecialchars($params['request_id']); }
                    // else { /* format $request details into $apiData (e.g., status, description) and $actionResult */ }
                    $actionResult = "I'll soon be able to fetch details about help requests! For now, please check with our support team directly for request " . htmlspecialchars($params['request_id']) . ".";
                } else {
                    $actionResult = $this->demandesAideRepository ? "I need a request ID to find information." : "Help request functionality is not fully configured in my system yet.";
                    if (!$this->demandesAideRepository) $this->logger->warning("DemandesAideRepository not available for get_help_request_info intent.");
                }
                break;
            
            case 'general_travel_question':
            default:
                $dataType = 'general';
                $actionResult = $params['query'] ?? $userQuery; // Query to be answered directly by Gemini
                break;
        }

        // 3. Generate final natural language response using Gemini
        $finalResponsePrompt = "";

        if ($dataType === 'flights') {
            if (isset($apiData['error'])) { // Error from Amadeus
                 $finalResponsePrompt = "You are MiraBot, a friendly travel agent. The user asked for flights, but there was an issue: '{$actionResult}'. Apologize, explain the issue clearly if possible, or ask them to try again or provide more specific details.";
            } elseif (!empty($apiData)) { // Flights found
                $flightSummary = "Here are up to 3 flight options I found:\n";
                $count = 0;
                foreach ($apiData as $offer) { // $apiData is an array of flight offers
                    $flightSummary .= "- Price " . ($offer['price'] ?? 'N/A') . " " . ($offer['currency'] ?? '') . ". ";
                    if (isset($offer['outbound']['segments'][0]['carrier'])) {
                         $flightSummary .= "Mainly with " . htmlspecialchars($offer['outbound']['segments'][0]['carrier']) . ". ";
                    }
                    $flightSummary .= "Outbound: " . htmlspecialchars($offer['outbound']['duration'] ?? 'N/A') . ", stops: " .htmlspecialchars($offer['outbound']['stopsDisplay'] ?? 'N/A'). ". ";
                    if(isset($offer['inbound']['duration'])) {
                        $flightSummary .= "Inbound: " . htmlspecialchars($offer['inbound']['duration'] ?? 'N/A') . ", stops: " .htmlspecialchars($offer['inbound']['stopsDisplay'] ?? 'N/A'). ". ";
                    }
                    $flightSummary .= "\n";
                    $count++;
                    if ($count >= 3) break;
                }
                if ($count > 0 && count($apiData) > $count) { // Check original count if apiData was array of offers
                    $flightSummary .= "There are more options available on our website.";
                } elseif ($count === 0 && empty($apiData['error'])) { // No offers in the apiData but no error reported
                     $flightSummary = $actionResult; // Use the "I couldn't find any flights..." message
                }
                $finalResponsePrompt = "You are MiraBot, a friendly travel agent. The user asked for flights. Based on the search, provide a helpful and concise response. Here's a summary of what was found: " . $flightSummary . " Encourage them to visit the website for more details or to book.";
            } else { // No error from Amadeus, but $apiData is empty (no flights found)
                $finalResponsePrompt = "You are MiraBot, a friendly travel agent. The user asked for flights. You searched but found nothing matching. Inform them: '{$actionResult}'. You can suggest they try different dates or locations.";
            }
        } elseif ($dataType === 'events') {
            if (!empty($apiData) && is_array($apiData)) { // $apiData for events is an array of summary strings
                $eventList = "";
                foreach($apiData as $eventSummaryLine) {
                    $eventList .= "\n- " . htmlspecialchars($eventSummaryLine);
                }
                $finalResponsePrompt = "You are MiraBot, a helpful assistant for Miravia. The user asked about events. You found the following: {$eventList}\nProvide this information in a friendly way. You can suggest asking for more details about a specific event or checking the website.";
            } else { // No events found or an issue occurred (covered by $actionResult if $apiData is empty)
                $finalResponsePrompt = "You are MiraBot, a helpful assistant for Miravia. The user asked about events. Respond based on this outcome: '{$actionResult}'. If nothing was found, suggest they try different keywords, locations, or check back later.";
            }
        } elseif ($dataType === 'partners' || $dataType === 'help_request') {
             // Generic handling for intents that currently return only a textual $actionResult (placeholders)
            $finalResponsePrompt = "You are MiraBot, a helpful AI assistant for Miravia. The user made a request and this is the current status or information: '{$actionResult}'. Respond appropriately.";
        } else { // General question or fallback
            $finalResponsePrompt = "You are MiraBot, a helpful and friendly travel agent AI for Miravia. Answer the user's question or statement: '" . htmlspecialchars($actionResult) . "'";
        }
        
        $this->logger->info("Final prompt to Gemini: " . $finalResponsePrompt);
        
        $generationConfig = [
            "temperature" => 0.7,
            "topK" => 40,
            "maxOutputTokens" => 800, // Adjust as needed
        ];
        $finalGeminiResponse = $this->callGeminiAPI([['parts' => [['text' => $finalResponsePrompt]]]], $generationConfig);
        
        if (!$finalGeminiResponse || !isset($finalGeminiResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $this->logger->error("Gemini returned no valid final response or an error structure for prompt: " . $finalResponsePrompt);
            return "I'm sorry, I encountered an issue while formulating my response. Please try again.";
        }
        $finalText = $finalGeminiResponse['candidates'][0]['content']['parts'][0]['text'];

        $this->logger->info("MiraBot Final Response: " . $finalText);
        return $finalText;
    }
}