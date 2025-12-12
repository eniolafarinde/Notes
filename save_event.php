<?php
$eventsFile = "events.json";

// Basic logic to handle JSON data saving
function saveEvents($events) {
    global $eventsFile;
    file_put_contents($eventsFile, json_encode(array_values($events), JSON_PRETTY_PRINT));
}

function loadEvents() {
    global $eventsFile;
    if (!file_exists($eventsFile)) return [];
    return json_decode(file_get_contents($eventsFile), true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $events = loadEvents();
    
    // Logic for saving a new event (simplified)
    if (isset($_POST['title'])) {
        $newEvent = [
            'id' => uniqid(),
            'title' => $_POST['title'],
            'start' => $_POST['start'],
            'end' => $_POST['end'],
            'allDay' => filter_var($_POST['allDay'], FILTER_VALIDATE_BOOLEAN),
            'tag' => $_POST['tag'],
            'color' => getColorByTag($_POST['tag']) // Function to assign color
        ];
        $events[] = $newEvent;
        saveEvents($events);
        
        // Return success response (FullCalendar expects specific data)
        header('Content-Type: application/json');
        echo json_encode($newEvent);
        exit;
    }
    
    // Add update/delete logic here as needed
}

function getColorByTag($tag) {
    // Simple logic to map tags to colors used in the CSS
    return match ($tag) {
        'work' => '#ff4d00',
        'school' => '#4CAF50',
        'event' => '#2196F3',
        default => '#666666',
    };
}
?>