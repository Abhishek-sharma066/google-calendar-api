<?php
session_start();
include('credentials.php');

// Check for valid access token in session
if (!isset($_SESSION['access_token']) || !isset($_SESSION['access_token_expiry']) || time() > $_SESSION['access_token_expiry']) {
    $accessTokenData = refreshAccessToken(REFRESH_TOKEN);
    if ($accessTokenData) {
        $_SESSION['access_token'] = $accessTokenData['access_token'];
        $_SESSION['access_token_expiry'] = time() + $accessTokenData['expires_in'];
    } else {
        echo json_encode(['error' => 'Unable to refresh access token']);
        exit;
    }
}

// Handle POST requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'getEvents':
            getEvents($_SESSION['access_token']);
            break;
        case 'addEvent':
            addEvent($_POST['data'], $_SESSION['access_token']);
            break;
        case 'deleteEvent':
            deleteEvent($_POST['eventId'], $_SESSION['access_token']);
            break;
        case 'getEventDetails':
            getEventDetails($_POST['eventId'], $_SESSION['access_token']);
            break;
        case 'editEvent':
            editEvent($_POST['data'], $_SESSION['access_token']);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}

// Function to refresh the access token using the refresh token
function refreshAccessToken($refreshToken) {
    $postData = [
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => REFRESH_TOKEN_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($postData),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    $data = json_decode($response, true);

    return isset($data['access_token']) ? $data : false;
}

// Fetch events from Google Calendar API
function getEvents($accessToken) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => CALENDAR_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $accessToken"],
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
        curl_close($ch);
        return;
    }

    curl_close($ch);
    $events = json_decode($response, true)['items'];
    $eventList = [];

    foreach ($events as $event) {
        $eventList[] = [
            'id' => $event['id'],
            'summary' => $event['summary'],
            'location' => $event['location'] ?? '',
            'start' => $event['start']['dateTime'],
            'end' => $event['end']['dateTime']
        ];
    }

    echo json_encode($eventList);
}

// get specific event to edit 
function getEventDetails($data, $accessToken){

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.googleapis.com/calendar/v3/calendars/vishal26677@gmail.com/events/'.$data,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $accessToken",
        "Content-Type: application/x-www-form-urlencoded"
      ),
    ));
    
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo json_encode(['error' => curl_error($curl)]);
        curl_close($curl);
        return;
    }
    $events = json_decode($response, true);
    


    echo json_encode($events);


curl_close($curl);


}

// Add an event to Google Calendar
function addEvent($data, $accessToken) {
 // Decode the JSON data sent from the front-end (from AJAX)
 // Assuming $data is coming from the frontend (json_decode the JSON data sent by AJAX or similar)
$data = json_decode($data, true);

// Check if JSON decoding was successful
if (!$data) {
    echo json_encode(['error' => 'Invalid JSON data']);
    return;
}

// Format the start time
$startTime = new DateTime($data['startTime']);
$startTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
$formattedStartTime = $startTime->format('Y-m-d\TH:i:sP'); // Example: 2025-01-18T09:00:00+05:30

// Format the end time
$endTime = new DateTime($data['endTime']);
$endTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
$formattedEndTime = $endTime->format('Y-m-d\TH:i:sP');
// Initialize cURL session

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.googleapis.com/calendar/v3/calendars/primary/events',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
  "summary": "' . $data['summary'] . '",
  "location": "' . $data['location'] . '",
  "description":"' . $data['description'] . '",
  "start": {
    "dateTime": "'.$formattedStartTime.'",
    "timeZone": "Asia/Kolkata"
  },
  "end": {
    "dateTime":  "'.$formattedEndTime.'",
    "timeZone": "Asia/Kolkata"
  },
  "attendees": [
    {"email": "testuser@gmail.com"},
    {"email": "testuser1@gmail.com"}
  ],
  "reminders": {
    "useDefault": false,
    "overrides": [
      {"method": "email", "minutes": 1440},
      {"method": "popup", "minutes": 10}
    ]
  }
}
',
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $accessToken", 
        'Content-Type: application/json',
    ),
));

echo $response = curl_exec($curl);

// Check for cURL errors
if (curl_errno($curl)) {
    echo json_encode(['error' => curl_error($curl)]);
} else {
    // Check the response for any errors from the API
    $responseDecoded = json_decode($response, true);
    
    if (isset($responseDecoded['error'])) {
        echo json_encode(['error' => $responseDecoded['error']['message']]);
    } else {
        // If no errors, return success message with the response
        echo json_encode(['message' => 'Event created successfully', 'response' => $response]);
    }
}

// Close the cURL session
curl_close($curl);
}


// Delete an event from Google Calendar
function deleteEvent($eventId, $accessToken) {
    $url = CALENDAR_API_URL . '/' . $eventId;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $accessToken"],
        CURLOPT_CUSTOMREQUEST => 'DELETE',
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        echo json_encode(['message' => 'Event deleted successfully']);
    }
    curl_close($ch);
}
?>
