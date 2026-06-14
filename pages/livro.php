<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";
	include_once "includes/util.php";

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

	$page_title = $book["title"]." - LÉAMP";

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

		/* comentarios */

		$reviews = supabaseGet(
		"reviews?".
			"loan_id=not.is.null".
			"&select=".
				"loan_id,".
				"rating,".
				"comment,".
				"status,".
				"favorite_excerpt,".
				"loan:loan_id(".
					"reader:reader(".
						"uuid,".
						"name,".
						"avatar".
					")".
				")".
			"&loan.book_id=eq.$id",

			$_SESSION["user"]["token"]
		);
//		file_put_contents("php://stderr", print_r($reviews, true));
		$reviews = array_values(
			array_filter(
				$reviews,
				fn($review) =>
					!empty($review["loan"]) &&
					!empty($review["loan"]["reader"])
			)
		);

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
			session_write_close();
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
		<?=buildStatus($book["status"])?>
	</div>
</div>

<?php 
	if (isAdmin() && $book["status"] === "Pendente"):
?>

<form method="POST" class="inline-form">
	<?=buildFormButton("green",
		"approve", "✓ Disponibilizar")?>
</form>

<?php
	elseif (isAdmin() && $book["status"] === "Disponível"):
?>
	<?=buildAButton("blue",
		"/emprestimo?id=".$book["id"], "→ Emprestar livro")?>
<?php endif;?>

<?php if (isLogged() && $loan && $user):?>
	<?=buildSmallCard([
		"color" => isOverdue($loan["deadline"], $loan["is_active"])?"red":"green",
		"user" => $user,
		"ranking"=> $ranking,
		"title" => "Emprestado para ".$user["name"],
		"deadline" => "Até ".date("d/m/Y", strtotime($loan["deadline"])),
		"extra" => isAdmin()?
					buildAButton("blue",
						"/devolucao?id=".$loan["id"], "↩ Devolver")
					:null
	])?>
<?php endif;?>

<?php if (!empty($reviews)): ?>
	<h3>Comentários dos leitores:</h3>
	<div class="big-card-container">
		<?php foreach ($reviews as $review):
			if ($review["status"] === "Aprovado"): ?>
				<?=buildBigCard([
					"user" => $review["loan"]["reader"],
					"ranking" => $ranking,
					"title" => $review["loan"]["reader"]["name"],
					"rating" => $review["rating"],
					"big-text" => $review["comment"],
					"quote" => $review["favorite_excerpt"],
					"extra" => isReviewer()?
						buildAButton("blue",
							"/resenha?id=".$review["loan_id"], "🖉 Editar")
						:null
				])?>
			<?php endif;
		endforeach; ?>
	</div>
<?php endif; ?>