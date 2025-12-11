<?php
$db = new PDO('sqlite:notes.db');
$db->exec("CREATE TABLE IF NOT EXISTS notes (id INTEGER PRIMARY KEY, title TEXT, content TEXT)");

$notes = $db->query("SELECT * FROM notes")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<body>
<h1>My Notes</h1>
<a href="new.php">Add Note</a>
<ul>
<?php foreach($notes as $n): ?>
<li>
<b><?= htmlspecialchars($n['title']) ?></b>
<br>
<?= nl2br(htmlspecialchars($n['content'])) ?>
<br>
<a href="delete.php?id=<?= $n['id'] ?>">Delete</a>
</li>
<?php endforeach; ?>
</ul>
</body>
</html>
