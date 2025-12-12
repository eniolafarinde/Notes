<?php
$eventsFile = "events.json";

if (!file_exists($eventsFile)) {
    echo "[]";
    exit;
}

// FullCalendar requires specific JSON fields: id, title, start, end, allDay, and color.
// Custom data (like 'tag') must be placed in 'extendedProps' for retrieval.

$events = json_decode(file_get_contents($eventsFile), true);

$outputEvents = array_map(function($event) {
    return [
        'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['start'],
        'end' => $event['end'],
        'allDay' => $event['allDay'],
        'color' => $event['color'],
        // Custom properties go here:
        'extendedProps' => [
            'tag' => $event['tag'] 
        ]
    ];
}, $events);

header('Content-Type: application/json');
echo json_encode($outputEvents);
?>