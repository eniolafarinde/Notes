<?php
$notesFile = "notes.json";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$idToDelete = (int)$_GET['id'];

if (!file_exists($notesFile)) {
    header("Location: index.php");
    exit;
}

$notes = json_decode(file_get_contents($notesFile), true);

if (isset($notes[$idToDelete])) {
    unset($notes[$idToDelete]);
    file_put_contents($notesFile, json_encode(array_values($notes), JSON_PRETTY_PRINT));
}

header("Location: index.php");
exit;
?>