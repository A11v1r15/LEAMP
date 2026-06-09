<?php

	require_once "includes/supabase.php";
	include_once "includes/ui.php";

	$id = $_GET["id"] ?? null;

	if (!$id) {
		echo "<h2 class='error'>Livro não encontrado</h2>";
		return;
	}

	$book = supabaseGet(
		"books?".
		"id=eq.$id".
		"&select=*"
	);

	if (!$book) {
		echo "<h2 class='error'>Livro não encontrado</h2>";
		return;
	}

	$book = $book[0];

	$title = $book["title"]." - LÉAMP";

	if (isLogged()) {

		$loan = supabaseGet(
			"loans?" .
			"book_id=eq.$id" .
			"&is_active=eq.true" .
			"&select=*",

			$_SESSION["user"]["token"] ?? null
		);

		$loan = $loan[0] ?? null;

		/* leitor */

		$user = null;

		$ranking = supabaseGet(
			"ranking?".
			"select=".
				"uuid".
			"&order=total.desc".
			"&limit=1",

			$_SESSION["user"]["token"]
		);

		if ($loan) {

			$reader_id = $loan["reader"];

			$user = supabaseGet(
				"users?" .
				"uuid=eq.$reader_id" .
				"&select=uuid,name,avatar",

				$_SESSION["user"]["token"] ?? null
			);

			$user = $user[0] ?? null;
		}

		if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "approve") {
			$result = supabasePatch(
				"books?".
				"id=eq.$id",
				[
					"status" => "Disponível"
				],
				$_SESSION["user"]["token"]
			);

			if (hasErrorCode($result)) {
				flash("error", "Erro ao disponibilizar livro: " . $result["message"]);
			} else {
				flash("success", $book[0]["title"]." disponibilizado com sucesso!");
				cacheDelete("livros");
			}

			header("Location: /livro?id=$id");
			exit;
		}
	}


?>
<link rel="stylesheet" href="/css/livro.css">

<div class="book-header">
	<div class="book-meta">
		<h2><?=htmlspecialchars($book["title"])?></h2>

		<div class="book-author">
			<?=htmlspecialchars($book["author"])?>
		</div>

		<div class="status <?=colorClass($book["status"])?>">
			<?=htmlspecialchars($book["status"])?>
		</div>
	</div>
</div>

<?php 
	if (isAdmin() && $book["status"] === "Pendente"):
?>

<form method="POST" class="inline-form">
	<button
		type="submit"
		name="action"
		value="approve"
		class="button green"
	>✓ Disponibilizar
	</button>
</form>

<?php
	elseif (isAdmin() && $book["status"] === "Disponível"):
?>

<a
	href="/emprestimo?id=<?=$book["id"]?>"
	class="button blue">→ Emprestar livro
</a>

<?php endif;?>

<?php if (isLogged() && $loan && $user):?>
	<div class="loan-card <?=isOverdue($loan["deadline"])?"overdue":""?>">
		<div class="avatar-wrapper">
			<img
				src="<?= htmlspecialchars(
					$user["avatar"]
				) ?>"
				class="loan-avatar"
			>
				<?php if ($user["uuid"] === $ranking[0]["uuid"]):?>
				<img
					class="crown"
					src="/img/Crown.png"
					alt="Crown"
				>
			<?php endif; ?>
		</div>
		<div class="loan-info">
			<div class="loan-title">
				Emprestado para
				<?=htmlspecialchars(
					$user["name"]
				)?>
			</div>

			<div class="loan-deadline">
				Até <?=date("d/m/Y", strtotime($loan["deadline"]))?>
			</div>
		</div>
		<?php if (isAdmin()):?>
			<a
				href="/devolucao?id=<?=$loan["id"]?>"
				class="button blue">↩ Devolver
			</a>
		<?php endif;?>
	</div>
<?php endif;?>