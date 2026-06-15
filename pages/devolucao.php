<?php

require_once "includes/supabase.php";
require_once "includes/cache.php";

requireAdmin();

$loan_id = $_GET["id"] ?? null;

if (!$loan_id) {

	echo "<h2 class='error'>Empréstimo não encontrado</h2>";
	return;
}

/* empréstimo */

$loan = supabaseGet(
	"loans?" .
	"id=eq.$loan_id" .
	"&select=*",

	$_SESSION["user"]["token"]
);

$loan = $loan[0] ?? null;

if (!$loan) {
	echo "<h2 class='error'>Empréstimo não encontrado</h2>";
	return;
}

/* livro */

$book = supabaseGet(
	"books?".
	"id=eq.".$loan["book_id"].
	"&select=".
		"id,".
		"title,".
		"author",

	$_SESSION["user"]["token"]
);

$book = $book[0] ?? null;

/* leitor */

$user = supabaseGet(
	"users?".
	"uuid=eq.".$loan["reader"].
	"&select=".
		"uuid,".
		"name,".
		"avatar",

	$_SESSION["user"]["token"]
);

$ranking = getCacheOrFetch(
	"primeiro_lugar",
	"ranking?".
	"select=".
		"uuid".
	"&order=total.desc".
	"&limit=1",

	$_SESSION["user"]["token"]
);

$user = $user[0] ?? null;

$page_title = "Devolução: ".(($book["title"] ?? "Livro"))." - LÉAMP";

/* ações */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$action = $_POST["action"];
	$result = null;

	/* renovar */

	if ($action === "renew") {
		$new_deadline = date("c", strtotime("+10 days"));

		$result = supabasePatch(
			"loans?".
			"id=eq.$loan_id",
			[
				"deadline" => $new_deadline."-03:00"
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao renovar empréstimo: " . $result["message"]);
		} else {
			flash("success", "Empréstimo de ".$book["title"]." renovado com sucesso para ".date("d/m/Y", strtotime($new_deadline))."!");
			session_write_close();
			header("Location: ".previousPage());
		}


		exit;
	}

	/* devolver */

	if ($action === "return") {
		$result = supabasePatch(
			"loans?".
			"id=eq.$loan_id",
			[
				"is_active" => false,
				"receiver" => $_SESSION["user"]["uuid"],
				"end_date" => date("c")."-03:00"
			],
			$_SESSION["user"]["token"]
		);

		$result = supabasePatch(
			"books?".
			"id=eq.".$loan["book_id"],
			[
				"status" => "Disponível"
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao registrar devolução: " . $result["message"]);
		} else {
			flash("success", "Devolução de ".$book["title"]." registrada com sucesso!");
			cacheDelete("livros");
			session_write_close();
			header("Location: ".previousPage());
		}

		exit;
	}
}

?>

<h2>Devolução de livro</h2>

<div class="main-page-container">
	<form class="main-page" method="POST">
		<?=buildSmallCard([
			"color" => isOverdue($loan["deadline"], $loan["is_active"])?"red":"green",
			"user" => $user,
			"ranking"=> $ranking,
			"title" => $book["title"],
			"subtitle" => "Com ".$user["name"],
			"deadline" => "Até ".date("d/m/Y", strtotime($loan["deadline"]))
		])?>
		<?=buildFormButton("green",
			"return", "↩ Confirmar devolução")?>
		<?=buildFormButton("yellow",
			"renew", "↺ Renovar +10 dias")?>
		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>