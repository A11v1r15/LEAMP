<?php

require_once "includes/cache.php";

requireAdmin();

$titulo = "Doação - LÉAMP";

$livros = getCacheOrFetch(
	"livros",
	"books?".
	"select=*"
);

$authors = array_unique(
	array_column($livros, "author")
);

sort($authors);

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$title = $_POST["title"];
	$author = $_POST["author"];

	$result = supabasePost(
		"books", [
			"title" => $title,
			"author" => $author,
			"status" => "Disponível"
		],

		$_SESSION["user"]["token"]
	);

	cacheDelete("livros");

	echo "<p>Doação registrada!</p>";
//	file_put_contents("php://stderr", print_r($result, true));
}

?>

<h2>Doação de livro</h2>

<div class="form-page">
	<form class="donation-form" method="POST">
		<datalist id="authors">
			<?php foreach ($authors as $author): ?>
				<option
					value="<?= htmlspecialchars($author) ?>"
				>
			<?php endforeach; ?>
		</datalist>

		<label for="title">
			Título:
		</label>
		<input type="text" name="title" required>

		<label for="author">
			Autor:
		</label>
		<input type="text" name="author" list="authors" required>

		<button type="submit" class="button green">
			← Registrar doação e sair
		</button>

		<button type="submit" class="button blue">
			↞ Registrar doação e doar outro livro
		</button>

		<a href="/" class="button red">
			⨯ Cancelar
		</a>

	</form>
</div>

<script src="/js/doar.js"></script>