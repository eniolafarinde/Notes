<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");

$notes = json_decode(file_get_contents($notesFile), true);

$id = $_POST["id"] ?? null;

$new = [
    "title" => $_POST["title"] ?: "Untitled",
    "text"  => $_POST["text"],
    "timestamp" => date("Y-m-d H:i:s")
];

if ($id !== null) {
    $notes[$id] = $new;
} else {
    $notes[] = $new;
}

file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));

header("Location: index.php?id=" . array_key_last($notes));
exit();
