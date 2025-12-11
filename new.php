<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");

$notes = json_decode(file_get_contents($notesFile), true);

$new = [
    "title" => "Untitled Note",
    "text"  => "",
    "timestamp" => date("Y-m-d H:i:s")
];

$notes[] = $new;

file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));

$newId = array_key_last($notes);

header("Location: index.php?id=" . $newId);
exit();
