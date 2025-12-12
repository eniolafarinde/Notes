<?php
$notesFile = "notes.json";
if (!file_exists($notesFile)) file_put_contents($notesFile, "[]");
$notes = json_decode(file_get_contents($notesFile), true);

$foldersFile = "folders.json";
if (!file_exists($foldersFile)) {
    $folders = [["id" => 99, "name" => "Recently Deleted", "is_special" => true]];
    file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT));
} else {
    $folders = json_decode(file_get_contents($foldersFile), true);
}

$selectedNote = null;
$selectedFolderId = $_GET["folder_id"] ?? null;
$filteredNotes = array_filter($notes, function($note) use ($selectedFolderId) {
    $noteFolderId = $note['folder_id'] ?? null;
    if ($selectedFolderId === null) {
        return !isset($note['deleted_at']);
    }
    if ($selectedFolderId == 99) {
        return isset($note['deleted_at']);
    }
    return $noteFolderId == $selectedFolderId; 
});

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
        .active-folder {
            color: #ff4d00 !important;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Menu</h2>
        <ul class="nav">
            <li><a href="index.php" class="nav-link <?= ($selectedFolderId === null) ? 'active-folder' : '' ?>">Home</a></li>
            <li><a href="notes.php" class="nav-link">Notes</a></li>
            <li><a href="folders_page.php" class="nav-link">Folders</a></li>
            <li>Calendar</li>
        </ul>

        <a href="new.php" class="add-note-btn" style="margin-top: 40px;">＋ Add Note</a>
    </aside>

    <div class="notes-panel">
        <h2 class="panel-title">
            <?php 
                if ($selectedFolderId === null) {
                    echo "Home";
                } elseif ($selectedFolderId == 99) {
                    echo "Recently Deleted";
                } else {
                    $currentFolder = array_filter($folders, fn($f) => $f['id'] == $selectedFolderId);
                    echo htmlspecialchars(current($currentFolder)['name'] ?? 'Notes');
                }
            ?>
        </h2>

        <div class="notes-list">
            <?php foreach ($filteredNotes as $index => $n): ?>
                <div class="note-item-container">
                    <a class="note-item <?= ($selectedNote && array_key_exists($index, $notes) && $notes[$index] === $selectedNote) ? "active" : "" ?>"
                    href="?id=<?= $index ?><?= $selectedFolderId ? '&folder_id=' . $selectedFolderId : '' ?>">
                        <h4><?= htmlspecialchars($n["title"] ?? "Untitled") ?></h4>
                        <p><?= substr(strip_tags($n["text"] ?? ''), 0, 60) ?>...</p>
                        <span class="date"><?= $n["timestamp"] ?? 'N/A' ?></span>
                    </a>
                    
                    <?php if ($selectedFolderId != 99): ?>
                        <a href="delete.php?id=<?= $index ?><?= $selectedFolderId ? '&redirect_folder_id=' . $selectedFolderId : '' ?>" 
                        class="delete-btn" 
                        onclick="return confirm('Are you sure you want to move this note to trash?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="editor">
    <?php if ($selectedNote): ?>
        <form action="save_note.php" method="POST" class="editor-form" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
            <input type="hidden" name="current_folder_id" value="<?= htmlspecialchars($_GET['folder_id'] ?? '') ?>" /> 
            
            <div style="margin-bottom: 15px; display: flex; align-items: center;">
                <label for="move_to_folder" style="margin-right: 10px; opacity: 0.7;">Move to:</label>
                <select name="new_folder_id" id="move_to_folder" 
                        style="padding: 5px; background: #1f1f1f; color: white; border: none; border-radius: 5px;">
                    <option value="" <?= !isset($selectedNote['folder_id']) ? 'selected' : '' ?>>Home</option>
                    <?php foreach ($folders as $folder): ?>
                        <?php if ($folder['is_special'] === true) continue; // Skip trash folder here ?>
                        <option value="<?= $folder['id'] ?>" 
                                <?= (isset($selectedNote['folder_id']) && $selectedNote['folder_id'] == $folder['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($folder['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input class="title-input" name="title" value="<?= htmlspecialchars($selectedNote["title"]) ?>" />
            
            <input type="hidden" name="text" id="hidden_text_input" value="<?= htmlspecialchars($selectedNote["text"]) ?>" />

            <div id="editor-container" class="editor-area">
                <?= $selectedNote["text"] ?>
            </div>

            <button class="save-btn">Save</button>
        </form>
    <?php else: ?>
        <div class="empty-editor">
            <p>
                <?php if ($selectedFolderId == 99): ?>
                    Your trash is empty.
                <?php elseif ($selectedFolderId !== null): ?>
                    This folder is empty. Select a note or click "Add Note" to create one here.
                <?php else: ?>
                    Select a note to view or edit.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
<?php if ($selectedNote): ?>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'font': [] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean']
    ];
    var quill = new Quill('#editor-container', {
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    'image': selectLocalImage 
                }
            }
        },
        theme: 'snow' 
    });
    function selectLocalImage() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', 'uploading...');
                uploadFile(file, range.index);
            }
        }
    }
    function uploadFile(file, index) {
        const formData = new FormData();
        formData.append('image_upload_quill', file);
        formData.append('is_quill_upload', '1');

        fetch('save_note.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            quill.deleteText(index, 1); 
            quill.insertEmbed(index, 'image', data.url); 
        })
        .catch(error => {
            console.error('Upload failed:', error);
            quill.deleteText(index, 1);
        });
    }
    var form = document.querySelector('.editor-form');
    var hiddenInput = document.getElementById('hidden_text_input');
    
    form.onsubmit = function() {
        hiddenInput.value = quill.root.innerHTML;
        return true; 
    };
<?php endif; ?>
</script>
</body>
</html>
