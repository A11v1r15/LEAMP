<?php

require_once "includes/supabase.php";

$id = $_GET["id"] ?? null;

if (!$id) {
	echo "Livro não encontrado";
	return;
}

$livro = supabaseGet("books?id=eq.$id&select=*");

if (!$livro) {
	echo "Livro não encontrado";
	return;
}

$livro = $livro[0];

$titulo = $livro["title"]." - LÉAMP";

$loan = supabaseGet(
	"loans?book_id=eq.$id&is_active=eq.true&select=*",
	$_SESSION["user"]["token"]
);

?>

<h2><?= htmlspecialchars($livro["title"]) ?></h2>

<p><strong>Autor:</strong> <?= htmlspecialchars($livro["author"]) ?></p>

<p><strong>Status:</strong> <?= htmlspecialchars($livro["status"]) ?></p>

<?php if (isAdmin() && $livro["status"] == "Disponível"):?>
<a href="/emprestimo?id=<?= $livro["id"] ?>">Emprestar livro</a>
<?php endif;?>

<?php 
if (!empty($loan)):

	$reader_id = $loan[0]["reader"];

	$user = supabaseGet(
		"users?uuid=eq.$reader_id&select=name,avatar",
		$_SESSION["user"]["token"]
	);
	file_put_contents('php://stderr', print_r($user, TRUE));

	$name = $user[0]["name"] ?? "desconhecido";
 ?>
	<div class="loan-card">
		<img
			src="<?= htmlspecialchars($user[0]["avatar"]) ?>"
			class="loan-avatar"
			alt="Avatar"
		>
		<div class="loan-info">
			<div class="loan-title">
				Emprestado para
				<?= htmlspecialchars($name) ?>
			</div>
			<div class="loan-deadline">
				Até
				<?= date("d/m/Y", strtotime($loan[0]["deadline"])) ?>
			</div>
		</div>
	</div>
<?php endif;?>