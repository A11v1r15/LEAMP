<?php

require_once "includes/supabase.php";

$id = $_GET["id"] ?? null;

if (!$id) {
	echo "Livro não encontrado";
	return;
}

$livro = supabaseGet("Test?id=eq.$id&select=*");

if (!$livro) {
	echo "Livro não encontrado";
	return;
}

$livro = $livro[0];

$titulo = "LÉAMP - ".$livro["title"];

?>

<h2><?= htmlspecialchars($livro["title"]) ?></h2>

<p><strong>Autor:</strong> <?= htmlspecialchars($livro["author"]) ?></p>

<p><strong>Status:</strong> <?= htmlspecialchars($livro["status"]) ?></p>