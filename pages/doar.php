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

<div class="main-page-container">
	<form class="main-page" method="POST">
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

		<?=buildFormButton("blue",
			"finish", "← Registrar doação e sair")?>
		<?=buildFormButton("green",
			"continue", "↞ Registrar doação e doar outro livro")?>
		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>

<script src="/js/doar.js"></script>