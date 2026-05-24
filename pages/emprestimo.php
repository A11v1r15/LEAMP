<?php

require_once "includes/auth.php";
require_once "includes/supabase.php";

requireAdmin();

$titulo = "Empréstimo - LÉAMP";

$users = supabaseGet(
	"users?".
	"select=uuid,name,avatar",

	$_SESSION["user"]["token"]
);
$book_id = $_GET["id"] ?? null;
$book = supabaseGet(
	"books?".
	"id=eq.$book_id".
	"&select=*",
	
	$_SESSION["user"]["token"]
);
if ($book[0]["title"]) {
	$titulo = "Empréstimo: ".$book[0]["title"]." - LÉAMP";
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
			"deadline" => $deadline,
			"book_id" => $book_id
		],
		$_SESSION["user"]["token"]
	);

	supabasePatch(
		"books?".
		"id=eq.$book_id",
		[
			"status" => "Emprestado"
		],
		$_SESSION["user"]["token"]
	);

	echo "<p>Empréstimo registrado!</p>";
//	file_put_contents("php://stderr", print_r($result, true));
}

?>
<link rel="stylesheet" href="/css/emprestimo.css">

<h2>
	Empréstimo de <?=$book[0]["title"]?>
</h2>

<div class="loan-page">
	<form class="loan-form" method="POST">

		<label>
			Leitor:
		</label>

		<select name="reader" id="reader-select" required>
			<option value="">
				Selecione o leitor
			</option>

			<?php foreach ($users as $u): ?>
				<option
					value="<?= $u["uuid"] ?>"
					data-name="<?= htmlspecialchars($u["name"]) ?>"
					data-avatar="<?= htmlspecialchars($u["avatar"]) ?>"
				>
					<?= $u["name"] ?>
				</option>
			<?php endforeach; ?>
		</select>

		<div id="reader-preview" class="loan-card hidden">
			<img
				id="preview-avatar"
				class="loan-avatar"
				src=""
				alt="Avatar"
			>
			<div class="loan-info">
				<div
					id="preview-name"
					class="loan-title"
				>
				</div>
				<div class="loan-deadline">
					Até<?= date("d/m/Y", strtotime("+10 days"))?>
				</div>
			</div>
		</div>

		<input type="hidden" name="book_id" value="<?= $book_id ?>">

		<button type="submit">
			Registrar empréstimo
		</button>

	</form>
</div>

<script src="/js/emprestimo.js"></script>