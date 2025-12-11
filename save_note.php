<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");

$notes = json_decode(file_get_contents($notesFile), true);

$id = isset($_POST["id"]) && $_POST["id"] !== "" ? intval($_POST["id"]) : null;

$new = [
    "title" => trim($_POST["title"]) ?: "Untitled",
    "text"  => $_POST["text"],
    "timestamp" => date("Y-m-d H:i:s")
];

if ($id !== null && isset($notes[$id])) {
    // Update existing
    $notes[$id] = $new;
} else {
    // Add new
    $notes[] = $new;
    $id = array_key_last($notes);
}

file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));

header("Location: index.php?id=" . $id);
exit();
