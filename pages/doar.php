<?php

require_once "includes/cache.php";

requireAdmin();

$page_title = "Doação - LÉAMP";

$books = getCacheOrFetch(
	"livros",
	"books?".
	"select=*"
);

$authors = array_unique(
	array_column($books, "author")
);

sort($authors);

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$title = $_POST["title"];
	$author = $_POST["author"];
	$status = isset($_POST["pending"])? "Pendente" 	: "Disponível";

	$result = supabasePost(
		"books", [
			"title" => $title,
			"author" => $author,
			"status" => $status
		],

		$_SESSION["user"]["token"]
	);

	if (hasErrorCode($result)) {
		flash("error", "Erro ao registrar doação: " . $result["message"]);
	} else {
		flash("success", "Doação de ".$title." registrada com sucesso!");
		cacheDelete("livros");
	}
	session_write_close();

	$action = $_POST["action"] ?? "finish";

	if ($action === "finish") {
		header("Location: /livros");
	} else if ($action === "continue") {
		header("Location: /doar");
	}
//	file_put_contents("php://stderr", print_r($result, true));
	exit;
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
			<h3>Título:</h3>
		</label>
		<input type="text" name="title" required>

		<label for="author">
			<h3>Autor:</h3>
		</label>
		<input type="text" name="author" list="authors" required>

		<label>
			<input
				type="checkbox"
				name="pending"
				value="1"
				checked
			>
			Livro à receber
		</label>

		<button
			type="submit"
			name="action"
			value="finish"
			class="button blue"
			>← Registrar doação e sair
		</button>

		<button
			type="submit"
			name="action"
			value="continue"
			class="button green"
			>↞ Registrar doação e doar outro livro
		</button>

		<a href="<?=htmlspecialchars(previousPage())?>" class="button red">
			⨯ Cancelar
		</a>

	</form>
</div>

<script src="/js/doar.js"></script>