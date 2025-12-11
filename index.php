<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");
$notes = json_decode(file_get_contents($notesFile), true);
$selectedNote = null;

if (isset($_GET["id"])) {
    $selectedNote = $notes[$_GET["id"]] ?? null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Menu</h2>
        <ul class="nav">
            <li>Home</li>
            <li>Notes</li>
            <li>Folders</li>
            <li>Calendar</li>
        </ul>

        <a href="#" class="add-note-btn">＋ Add Note</a>
    </aside>

    <div class="notes-panel">
        <h2 class="panel-title">Notes</h2>

        <div class="notes-list">
            <?php foreach ($notes as $index => $n): ?>
                <a class="note-item <?= ($selectedNote && $notes[$index] === $selectedNote) ? "active" : "" ?>"
                   href="?id=<?= $index ?>">
                    <h4><?= htmlspecialchars($n["title"] ?? "Untitled") ?></h4>
                    <p><?= htmlspecialchars(substr($n["text"], 0, 60)) ?>...</p>
                    <span class="date"><?= $n["timestamp"] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Editor -->
    <div class="editor">

        <?php if ($selectedNote): ?>
            <form action="save_note.php" method="POST" class="editor-form">
                <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />

                <input class="title-input" name="title" value="<?= htmlspecialchars($selectedNote["title"]) ?>" />

                <textarea class="editor-area" name="text"><?= htmlspecialchars($selectedNote["text"]) ?></textarea>

                <button class="save-btn">Save</button>
            </form>
        <?php else: ?>
            <div class="empty-editor">
                <p>Select a note to view or edit</p>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
