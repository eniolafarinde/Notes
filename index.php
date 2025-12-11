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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
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

            <a href="new.php" class="add-note-btn">＋ Add Note</a>
        </aside>

        <div class="notes-panel">
            <h2 class="panel-title">Notes</h2>

            <div class="notes-list">
                <?php foreach ($notes as $index => $n): ?>
                    <div class="note-item-container">
                        <a class="note-item <?= ($selectedNote && array_key_exists($index, $notes) && $notes[$index] === $selectedNote) ? "active" : "" ?>"
                        href="?id=<?= $index ?>">
                            <h4><?= htmlspecialchars($n["title"] ?? "Untitled") ?></h4>
                            <p><?= substr(strip_tags($n["text"]), 0, 60) ?>...</p>
                        </a>
                        <a href="delete.php?id=<?= $index ?>" 
                        class="delete-btn" 
                        onclick="return confirm('Are you sure you want to delete this note?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="editor">
        <?php if ($selectedNote): ?>
            <form action="save_note.php" method="POST" class="editor-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
                <input class="title-input" name="title" value="<?= htmlspecialchars($selectedNote["title"]) ?>" />
                
                <input type="hidden" name="text" id="hidden_text_input" value="<?= htmlspecialchars($selectedNote["text"]) ?>" />

                <div id="editor-container" class="editor-area">
                    <?= $selectedNote["text"] ?>
                </div>

                <button class="save-btn">Save</button>
            </form>
        <?php else: ?>
            <div class="empty-editor">
                <p>Select a note to view or edit</p>
            </div>
        <?php endif; ?>
    </div>
    </div>
    <script>
        document.getElementById('image_upload').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('file-name-display').textContent = fileName;
        });
    </script>
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
