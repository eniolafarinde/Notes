<?php
$db = new PDO('sqlite:notes.db');
$id = $_GET['id'];
$stmt = $db->prepare("DELETE FROM notes WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
