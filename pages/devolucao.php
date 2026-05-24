<?php

require_once "includes/auth.php";
require_once "includes/supabase.php";

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
	"id=eq.".$loan["book_id"] .
	"&select=title,author",

	$_SESSION["user"]["token"]
);

$book = $book[0] ?? null;

/* leitor */

$user = supabaseGet(
	"users?".
	"uuid=eq.".$loan["reader"].
	"&select=name,avatar",

	$_SESSION["user"]["token"]
);

$user = $user[0] ?? null;

$titulo = "Devolução: ".(($book["title"] ?? "Livro"))." - LÉAMP";

/* ações */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$action = $_POST["action"];

	/* renovar */

	if ($action === "renew") {
		$new_deadline =
			date(
				"c",
				strtotime("+10 days")
			);

		supabasePatch(
			"loans?".
			"id=eq.$loan_id",
			[
				"deadline" => $new_deadline
			],
			$_SESSION["user"]["token"]
		);

		header("Location: /livro?id=" .$loan["book_id"]);

		exit;
	}

	/* devolver */

	if ($action === "return") {
		supabasePatch(
			"loans?".
			"id=eq.$loan_id",
			[
				"is_active" => false,
				"receiver" => $_SESSION["user"]["uuid"],
				"end_date" => date("c")
			],
			$_SESSION["user"]["token"]
		);

		supabasePatch(
			"books?".
			"id=eq.".$loan["book_id"],
			[
				"status" => "Disponível"
			],
			$_SESSION["user"]["token"]
		);

		header("Location: /livro?id=" .$loan["book_id"]);

		exit;
	}
}

?>
<link rel="stylesheet" href="/css/devolucao.css">

<div class="return-page">

	<h2>Devolução de livro</h2>

	<div class="loan-card">
		<img
			src="<?= htmlspecialchars(
				$user["avatar"]
			) ?>"
			class="loan-avatar"
			alt="Avatar"
		>
		<div class="loan-info">
			<div class="loan-book">
				<?= htmlspecialchars(
					$book["title"]
				) ?>
			</div>

			<div class="loan-user">
				Com
				<?= htmlspecialchars(
					$user["name"]
				) ?>
			</div>

			<div class="loan-deadline">
				Até
				<?= date("d/m/Y", strtotime($loan["deadline"])) ?>
			</div>
		</div>
	</div>

	<form
		method="POST"
		class="return-actions"
	>

		<button
			type="submit"
			name="action"
			value="renew"
			class="renew-button"
		>↺ Renovar +10 dias
		</button>

		<button
			type="submit"
			name="action"
			value="return"
			class="return-button"
		>↩ Confirmar devolução
		</button>
	</form>
</div>