<?php
$notesFile = "notes.json";
$uploadsDir = "uploads/";

if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

if (!file_exists($notesFile)) {
    file_put_contents($notesFile, "[]");
}

$notes = json_decode(file_get_contents($notesFile), true);

function handleFileUpload($fileInputName, $uploadsDir) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileName = basename($_FILES[$fileInputName]['name']);
        
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeFileName = uniqid() . '-' . time() . '.' . $extension;
        $destPath = $uploadsDir . $safeFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            return $destPath;
        }
    }
    return null;
}

if (isset($_POST['is_quill_upload']) && $_POST['is_quill_upload'] == '1') {
    $imagePath = handleFileUpload('image_upload_quill', $uploadsDir);

    if ($imagePath) {
        header('Content-Type: application/json');
        echo json_encode(['url' => $imagePath]);
        exit();
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Upload failed']);
        exit();
    }
}

$id = isset($_POST["id"]) && $_POST["id"] !== "" ? intval($_POST["id"]) : null;
$newFolderId = $_POST['new_folder_id'] ?? null; 
if ($newFolderId === '') {
    $newFolderId = null;
} else {
    $newFolderId = intval($newFolderId);
}

$currentFolderId = $_POST['current_folder_id'] ?? null;
if ($currentFolderId !== null) {
    $currentFolderId = intval($currentFolderId);
}


$newNoteData = [
    "title" => trim($_POST["title"]) ?: "Untitled",
    "text"  => $_POST["text"] ?? '', 
    "timestamp" => date("Y-m-d H:i:s"),
    "folder_id" => $newFolderId,
];

if ($newFolderId !== 99) {
    unset($newNoteData['deleted_at']);
}

if ($id !== null && isset($notes[$id])) {
    $notes[$id] = array_merge($notes[$id], $newNoteData);
    $redirectId = $id;
} else {
    $notes[] = $newNoteData;
    $redirectId = array_key_last($notes);
    $id = $redirectId;
}

file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));

$redirectParams = "id=" . $id;
$targetFolderId = $newFolderId ?? $currentFolderId;

if ($targetFolderId !== null) {
    $redirectParams .= "&folder_id=" . $targetFolderId;
}
header("Location: index.php?" . $redirectParams);
exit();
?>