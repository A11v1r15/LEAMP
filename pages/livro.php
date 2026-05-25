<?php

require_once "includes/supabase.php";
require_once "includes/auth.php";

$id = $_GET["id"] ?? null;

if (!$id) {
	echo "<h2 class='error'>Livro não encontrado</h2>";
	return;
}

$livro = supabaseGet(
	"books?".
	"id=eq.$id".
	"&select=*",
	
	$_SESSION["user"]["token"]
);

if (!$livro) {
	echo "<h2 class='error'>Livro não encontrado</h2>";
	return;
}

$livro = $livro[0];

$titulo = $livro["title"]." - LÉAMP";

$loan = supabaseGet(
	"loans?" .
	"book_id=eq.$id" .
	"&is_active=eq.true" .
	"&select=*",

	$_SESSION["user"]["token"]
);

$loan = $loan[0] ?? null;

/* leitor */

$user = null;

if ($loan) {

	$reader_id = $loan["reader"];

	$user = supabaseGet(
		"users?" .
		"uuid=eq.$reader_id" .
		"&select=name,avatar",

		$_SESSION["user"]["token"]
	);

	$user = $user[0] ?? null;
}

?>
<link rel="stylesheet" href="/css/livro.css">

<div class="book-header">
	<div class="book-meta">
		<h2><?= htmlspecialchars($livro["title"]) ?></h2>

		<div class="book-author">
			<?= htmlspecialchars($livro["author"]) ?>
		</div>

		<div class="status <?= strtolower($livro["status"]) ?>">
			<?= htmlspecialchars($livro["status"]) ?>
		</div>
	</div>
</div>

<?php
	if (isAdmin() && $livro["status"] == "Disponível"):
?>

<a
	href="/emprestimo?id=<?= $livro["id"] ?>"
	class="button blue">Emprestar livro
</a>

<?php endif; ?>

<?php if ($loan && $user): ?>
	<div class="loan-card">
		<img
			src="<?= htmlspecialchars(
				$user["avatar"]
			) ?>"
			class="loan-avatar"
			alt="Avatar"
		>
		<div class="loan-info">
			<div class="loan-title">
				Emprestado para
				<?= htmlspecialchars(
					$user["name"]
				) ?>
			</div>

			<div class="loan-deadline">
				Até <?= date("d/m/Y", strtotime($loan["deadline"])) ?>
			</div>
		</div>
		<?php if (isAdmin()): ?>
			<a
				href="/devolucao?id=<?= $loan["id"] ?>"
				class="button red">↩ Devolver
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>