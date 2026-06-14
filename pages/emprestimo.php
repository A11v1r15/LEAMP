<?php

require_once "includes/supabase.php";
require_once "includes/cache.php";

requireAdmin();

$page_title = "Empréstimo - LÉAMP";

$users = supabaseGet(
	"users?".
	"select=uuid,name,avatar",

	$_SESSION["user"]["token"]
);
$book_id = $_GET["id"] ?? null;
$book = supabaseGet(
	"books?".
	"id=eq.$book_id".
	"&select=*"
);
if ($book[0]["title"]) {
	$page_title = "Empréstimo: ".$book[0]["title"]." - LÉAMP";
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$reader = $_POST["reader"];
	$grantor = $_SESSION["user"]["uuid"];
	$deadline = date("c", strtotime("+10 days"));

	$result = supabasePost(
		"loans", [
			"reader" => $reader,
			"grantor" => $grantor,
			"deadline" => $deadline."-03:00",
			"book_id" => $book_id
		],
		$_SESSION["user"]["token"]
	);

	supabasePatch(
		"books?".
		"id=eq.$book_id", [
			"status" => "Emprestado"
		],
		$_SESSION["user"]["token"]
	);

	if (hasErrorCode($result)) {
		flash("error", "Erro ao registrar empréstimo: " . $result["message"]);
	} else {
		flash("success", "Empréstimo de ".$book[0]["title"]." registrado com sucesso!");
		cacheDelete("livros");
		session_write_close();
		header("Location: /livro?id=".$book[0]["id"]);
	}

//	file_put_contents("php://stderr", print_r($result, true));
}

?>
<link rel="stylesheet" href="/css/emprestimo.css">

<h2>
	Empréstimo de <?=$book[0]["title"]?>
</h2>

<div class="main-page-container">
	<form class="main-page" method="POST">

		<label for="reader">
			<h3>Leitor:</h3>
		</label>

		<select name="reader" id="reader-select" required>
			<option value="">Selecione o leitor</option>
			<?php foreach ($users as $u):?>
				<option
					value="<?=$u["uuid"]?>"
					data-name="<?=htmlspecialchars($u["name"])?>"
					data-avatar="<?=htmlspecialchars($u["avatar"])?>"
				>
					<?=$u["name"]?>
				</option>
			<?php endforeach;?>
		</select>
		<?=buildSmallCard([
			"id" => "reader-preview",
			"color" => "yellow",
			"dynamic" => true,
			"deadline" => "Até ".date("d/m/Y", strtotime("+10 days"))
		])?>

		<input type="hidden" name="book_id" value="<?=$book_id?>">

		<?=buildFormButton("green",
			"", "→ Registrar empréstimo")?>
		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>

<script src="/js/emprestimo.js"></script>