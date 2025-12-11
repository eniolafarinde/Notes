<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) {
    file_put_contents($notesFile, "[]");
}

$notes = json_decode(file_get_contents($notesFile), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <h2 class="sidebar-title">Menu</h2>

        <ul class="nav">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="notes.php" class="nav-link">Notes</a></li>
            <li>Folders</li>
            <li>Calendar</li>
        </ul>

        <a href="new_note.php" class="add-note-btn">＋ Add Note</a>
    </aside>

    <div class="notes-full-page">
        <h1 class="page-title">All Notes</h1>

        <div class="notes-grid">
            <?php if (count($notes) > 0): ?>
                <?php foreach ($notes as $id => $note): ?>
                    <a href="index.php?id=<?= $id ?>" class="note-card">
                        <h3><?= htmlspecialchars($note["title"]) ?></h3>
                        <p><?= substr($note["text"], 0, 120) ?>...</p>
                        <span class="date"><?= $note["timestamp"] ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty">No notes yet. Create one!</p>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
