<?php
function loadFolders() {
    $foldersFile = "folders.json";
    if (!file_exists($foldersFile)) {
        $defaultFolders = [
            ["id" => 99, "name" => "Recently Deleted", "is_special" => true]
        ];
        file_put_contents($foldersFile, json_encode($defaultFolders, JSON_PRETTY_PRINT));
        return $defaultFolders;
    }
    return json_decode(file_get_contents($foldersFile), true);
}
function saveFolders($folders) {
    $foldersFile = "folders.json";
    file_put_contents($foldersFile, json_encode(array_values($folders), JSON_PRETTY_PRINT));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['folder_name'])) {
    $folders = loadFolders();

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

    header("Location: index.php");
    exit();
}


?>