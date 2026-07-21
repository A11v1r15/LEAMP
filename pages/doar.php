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

$wantedBooks = array_filter(
	$books,
	fn($book) => $book["status"] === "Indisponível"
);

sort($wantedBooks);

/* envia formulário */

$action = $_POST["action"] ?? "finish";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !str_starts_with($action, "replace_")) {
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

	if ($action === "finish") {
		header("Location: /livros");
	} else if ($action === "continue") {
		header("Location: /doar");
	}
//	file_put_contents("php://stderr", print_r($result, true));
	exit;
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && str_starts_with($action, "replace_")) {
	$book_id = substr($action, strlen("replace_"));
	$book = array_filter(
		$books,
		fn($b) => $b["id"] === $book_id
	)[0];

	$result = supabasePatch(
		"books?".
		"id=eq.$book_id", [
			"status" => "Disponível"
		],

		$_SESSION["user"]["token"]
	);

	if (hasErrorCode($result)) {
		flash("error", "Erro ao registrar doação: " . $result["message"]);
	} else {
		flash("success", "Doação de ".$book["title"]." registrada com sucesso!");
		cacheDelete("livros");
	}
	session_write_close();
	header("Location: /livro?id=".$book_id);
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
		<input
			type="text"
			name="title"
			required
			>
		<label for="author">
			<h3>Autor:</h3>
		</label>
		<input
			type="text"
			autocapitalize="words"
			name="author"
			list="authors"
			required
			>
		<label>
			<input
				type="checkbox"
				name="pending"
				value="1"
				checked
			>
			Livro à receber
		</label>

		<?=buildFormButton("green",
			"continue", "↞ Registrar doação e doar outro livro")?>
		<?=buildFormButton("blue",
			"finish", "← Registrar doação e sair")?>
		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>

		<?php if (!empty($wantedBooks)): ?>
			<h2>Livros procurados</h2>

			<?=buildSmallCard([
				"color" => "blue",
				"text" =>
					"Os títulos abaixo fazem parte do acervo do projeto,
					mas encontram-se indisponíveis por extravio ou dano
					irreparável. Caso algum deles seja doado, poderá
					substituir o exemplar anterior."
			])?>

			<table id="wantedBooksTable">
				<thead>
					<tr>
						<th>Título</th>
						<th>Autor</th>
						<th>Ação</th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($wantedBooks as $book): ?>
						<tr>
							<td><?=htmlspecialchars($book["title"])?></td>
							<td><?=htmlspecialchars($book["author"])?></td>
							<td><?=buildFormButton("green",
								"replace_".$book["id"],
								"♻ Substituir exemplar")?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<script>
				new DataTable("#wantedBooksTable", {
					paging: false,
					info: false,
					order: [[0, "asc"]],
					language: {
						url: "https://cdn.datatables.net/plug-ins/2.3.1/i18n/pt-BR.json"
					}
				});
			</script>
		<?php endif; ?>
	</form>
</div>

<script src="/js/doar.js"></script>