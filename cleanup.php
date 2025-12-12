<?php
$notesFile = "notes.json";
$daysToKeep = 30;
$cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);

if (!file_exists($notesFile)) {
    exit("Notes file not found.");
}

$notes = json_decode(file_get_contents($notesFile), true);
$notesToKeep = [];
$deletedCount = 0;

foreach ($notes as $note) {
    if (isset($note['deleted_at'])) {
        $deletedTimestamp = strtotime($note['deleted_at']);
        if ($deletedTimestamp < $cutoffTime) {
            if (isset($note['image']) && file_exists($note['image'])) {
                unlink($note['image']);
            }
            $deletedCount++;
            continue;
        }
    }
    $notesToKeep[] = $note;
}

if ($deletedCount > 0) {
    file_put_contents($notesFile, json_encode(array_values($notesToKeep), JSON_PRETTY_PRINT));
    echo "Cleanup complete. Permanently deleted {$deletedCount} notes.\n";
} else {
    echo "No notes needed permanent deletion.\n";
}
?>