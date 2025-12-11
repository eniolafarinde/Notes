<?php
$db = new PDO('sqlite:notes.db');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $stmt = $db->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content']]);
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<body>
<h1>New Note</h1>
<form method="POST">
<input name="title" placeholder="Title"><br><br>
<textarea name="content" placeholder="Content"></textarea><br><br>
<button type="submit">Save</button>
</form>
</body>
</html>
