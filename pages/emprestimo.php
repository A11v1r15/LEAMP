<?php

require_once "includes/auth.php";
require_once "includes/supabase.php";

requireAdmin();

$titulo = "Novo empréstimo";

$users = supabaseGet("users?select=uuid,name,avatar", $_SESSION["user"]["token"]);
$book_id = $_GET["id"] ?? null;
$book = supabaseGet("books?id=eq.$book_id&select=*");

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

	echo "<p>Empréstimo registrado!</p>";

	file_put_contents(
		"php://stderr",
		print_r($result, true)
	);
}

?>

<h2>
	Empréstimo de <?=$book[0]["title"]?>
</h2>

<form method="POST">

	<label>
		Leitor:
	</label>

	<select name="reader" required>
		<option value="">
			Selecione o leitor
		</option>

		<?php foreach ($users as $u): ?>
			<option value="<?= $u["uuid"] ?>">
				<?= $u["name"] ?>
			</option>
		<?php endforeach; ?>
	</select>

	<p>
		Deadline:
		<?= date("d/m/Y", strtotime("+10 days")) ?>
	</p>

	<input type="hidden" name="book_id" value="<?= $book_id ?>">

	<button type="submit">
		Registrar empréstimo
	</button>

</form>