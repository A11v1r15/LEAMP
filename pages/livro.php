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

$titulo = "LÉAMP - ".$livro["title"];

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
if (!empty($loan)) {

	$reader_id = $loan[0]["reader"];

	$user = supabaseGet(
		"users?uuid=eq.$reader_id&select=name,avatar",
		$_SESSION["user"]["token"]
	);
	file_put_contents('php://stderr', print_r($user, TRUE));

	$name = $user[0]["name"] ?? "desconhecido";

	echo "<p style='color:red'>";
	echo "Emprestado para: $name";
	echo "<img src='".$user[0]["avatar"]."' alt='Foto de perfil' class='profile-picture'/>";
	echo "</p>";
} ?>