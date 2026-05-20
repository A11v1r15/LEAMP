<?php
	require_once "includes/supabase.php";

	$titulo = "LÉAMP - Livros";

	$livros = supabaseGet("Test?select=*");
	if (!is_array($livros)) {
		$livros = [];
	}
//	echo "<pre>";
//	print_r($livros);
//	echo "</pre>";
?>

<h2>Livros</h2>

<ul>
	<?php foreach ($livros as $livro): ?>

		<li>
			<a href="?p=livro&id=<?= $livro["id"] ?>">
				<?= htmlspecialchars($livro["title"]) ?>
			</a>
			→ <?= htmlspecialchars($livro["author"]) ?>
		</li>

	<?php endforeach; ?>
</ul>