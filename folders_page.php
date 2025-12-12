<?php
$notesFile = "notes.json";
$foldersFile = "folders.json";

if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");
$notes = json_decode(file_get_contents($notesFile), true);

if (!file_exists($foldersFile)) {
    $folders = [["id" => 99, "name" => "Recently Deleted", "is_special" => true]];
    file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT));
} else {
    $folders = json_decode(file_get_contents($foldersFile), true);
}

$selectedFolderId = $_GET["folder_id"] ?? null;
$userFolders = array_filter($folders, fn($f) => $f['is_special'] === false);

if ($selectedFolderId === null) {
    $defaultFolder = reset($userFolders);
    $selectedFolderId = $defaultFolder ? $defaultFolder['id'] : 99;
} else {
    $selectedFolderId = intval($selectedFolderId);
}

$folderNotes = array_filter($notes, function($note) use ($selectedFolderId) {
    $noteFolderId = $note['folder_id'] ?? null;
    $isDeleted = isset($note['deleted_at']);

    if ($selectedFolderId == 99) {
        return $isDeleted;
    }

    return $noteFolderId === $selectedFolderId && !$isDeleted;
});

$currentFolder = array_filter($folders, fn($f) => $f['id'] === $selectedFolderId);
$currentFolderName = current($currentFolder)['name'] ?? 'Notes in Folder';

function saveFolders($folders) {
    global $foldersFile;
    file_put_contents($foldersFile, json_encode(array_values($folders), JSON_PRETTY_PRINT));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['folder_name'])) {
    $folders = json_decode(file_get_contents($foldersFile), true); 
    
    $maxId = 0;
    foreach ($folders as $f) {
        if ($f['id'] > $maxId) {
            $maxId = $f['id'];
        }
    }
    $newId = $maxId + 1;

    $newFolder = [
        "id" => $newId,
        "name" => htmlspecialchars(trim($_POST["folder_name"])),
        "is_special" => false
    ];

    $folders[] = $newFolder;
    saveFolders($folders);

    header("Location: folders_page.php?folder_id=" . $newId);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Folders & Trash</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .layout-folders-page {
            display: flex;
            height: 100vh;
        }
        .folder-management-panel {
            width: 360px;
            background: #0f0f0f; 
            border-right: 1px solid #1f1f1f;
            padding: 20px;
            overflow-y: auto;
        }
        .notes-in-folder-panel {
            flex: 1; 
            background: #0d0d0d;
            padding: 20px 40px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="layout-folders-page"> 

    <aside class="sidebar">
        <h2 class="sidebar-title">Menu</h2>
        <ul class="nav">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="notes.php" class="nav-link">Notes</a></li>
            <li><a href="folders_page.php" class="nav-link active-folder">Folders</a></li>
            <li>Calendar</li>
        </ul>
        <a href="new.php" class="add-note-btn" style="margin-top: 40px;">＋ Add Note</a>
    </aside>


    <div class="folder-management-panel">
        <h2 class="panel-title">Folders & Trash</h2>

        <form action="folders_page.php" method="POST" class="folder-add-form">
            <input type="text" name="folder_name" placeholder="New Folder Name" required />
            <button type="submit">
                <i class="fas fa-plus"></i> Add Folder
            </button>
        </form>
        
        <h3 class="sidebar-title">Your Folders</h3>
        <?php foreach ($folders as $folder): ?>
            <?php if ($folder['is_special'] === false): ?>
                <a href="?folder_id=<?= $folder['id'] ?>" 
                   class="folder-item <?= ($selectedFolderId === $folder['id']) ? 'active' : '' ?>">
                    <h4><i class="fas fa-folder"></i> <?= htmlspecialchars($folder["name"]) ?></h4>
                    <p>Notes: <?= count(array_filter($notes, fn($n) => ($n['folder_id'] ?? null) === $folder['id'] && !isset($n['deleted_at']))) ?></p>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <h3 class="sidebar-title">Trash</h3>
        <?php foreach ($folders as $folder): ?>
            <?php if ($folder['is_special'] === true): ?>
                <a href="?folder_id=<?= $folder['id'] ?>" 
                   class="folder-item <?= ($selectedFolderId === $folder['id']) ? 'active' : '' ?>">
                    <h4><i class="fas fa-trash-alt"></i> <?= htmlspecialchars($folder["name"]) ?></h4>
                    <p>Contains: <?= count(array_filter($notes, fn($n) => isset($n['deleted_at']))) ?> deleted notes</p>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>


    <div class="notes-in-folder-panel">
        <h2 class="page-title">Notes in <?= htmlspecialchars($currentFolderName) ?></h2>

        <div class="notes-list">
            <?php if (empty($folderNotes)): ?>
                <p style="opacity: 0.5;">
                    <?php if ($selectedFolderId === 99): ?>
                        The trash folder is empty.
                    <?php else: ?>
                        This folder is empty. Create a new note or move an existing one here.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            
            <?php foreach ($folderNotes as $index => $n): ?>
                <a href="index.php?id=<?= $index ?>&folder_id=<?= $selectedFolderId ?>" class="folder-note-item">
                    <div class="content">
                        <h4><?= htmlspecialchars($n["title"] ?? "Untitled") ?></h4>
                        <p>
                            <?= substr(strip_tags($n["text"] ?? ''), 0, 80) ?>...
                            <span class="date"><?= $n["timestamp"] ?? 'N/A' ?></span>
                        </p>
                    </div>
                    <div class="actions">
                        <?php if ($selectedFolderId === 99): ?>
                            <i class="fas fa-undo" title="Restore"></i>
                            <i class="fas fa-times" title="Permanently Delete"></i>
                        <?php else: ?>
                            <a href="delete.php?id=<?= $index ?>&redirect_folder_id=<?= $selectedFolderId ?>">
                                <i class="fas fa-trash-alt" title="Move to Trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>