<?php

require_once "includes/cache.php";

requireAdmin();

$titulo = "Doação - LÉAMP";

$livros = getCacheOrFetch(
	"livros",
	"books?".
	"select=*"
);

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

		<label>
			Título:
		</label>
		<input type="text" name="title" required>

		<label>
			Autor:
		</label>
		<input type="text" name="author" required>

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