<?php
	date_default_timezone_set("America/Fortaleza");
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
		<span class="status <?=
			colorClass($book["status"])?>">
			<?=htmlspecialchars($book["status"])?>
		</span>
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
	<div class="loan-card <?=isOverdue($loan["deadline"], $loan["is_active"])?"overdue":""?>">
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


<?php if (!empty($reviews)): ?>
	<h3>Resenhas dos leitores:</h3>
	<?php foreach ($reviews as $review):
		if ($review["status"] === "Aprovado"): ?>
		<div class="review-card">
			<div class="review-header">
				<div class="avatar-wrapper">
					<img
						src="<?= htmlspecialchars(
							$review["loan"]["reader"]["avatar"]
						) ?>"
						class="loan-avatar"
					>
					<?php if (
						$review["loan"]["reader"]["uuid"] === $ranking[0]["uuid"]): ?>
						<img
							class="crown"
							src="/img/Crown.png"
							alt="Crown"
						>
					<?php endif; ?>
				</div>
				<div>
					<div class="review-reader">
						<?= htmlspecialchars(
							$review["loan"]["reader"]["name"]
						) ?>
					</div>
					<?php if (!empty($review["rating"]) && $review["rating"] !== 0): ?>
						<div class="review-rating">
							<?php
							echo str_repeat(
								"★",
								(int)$review["rating"]
							);
							echo str_repeat(
								"☆",
								5 - (int)$review["rating"]
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if (
				!empty(
					$review["comment"]
				) && $review["comment"] !== ""
			): ?>
				<p class="review-comment">
					<?= nl2br(
						htmlspecialchars($review["comment"])) 
					?>
				</p>

			<?php endif; ?>
			<?php if (
				!empty($review["favorite_excerpt"]) &&
				$review["favorite_excerpt"] !== ""
			): ?>
				<blockquote>
					<?= nl2br(
						htmlspecialchars($review["favorite_excerpt"]))
					?>
				</blockquote>
			<?php endif; ?>
		</div>
	<?php endif; 
		endforeach; ?>
<?php endif; ?>