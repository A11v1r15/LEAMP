<?php

require_once "includes/supabase.php";

requireLogged();

$loan_id = $_GET["id"] ?? null;

if (!$loan_id) {
	echo "<h2 class='error'>Empréstimo não encontrado</h2>";
	return;
}

/* empréstimo */

$uuid = $_SESSION["user"]["uuid"];

$loan = supabaseGet(
	"loans?".
	"id=eq.$loan_id".
	"&reader=eq.$uuid".
	"&select=".
		"book:book_id(".
			"id,".
			"title".
		")",

	$_SESSION["user"]["token"]
);

$review = supabaseGet(
	"reviews?".
	"loan_id=eq.$loan_id".
	"&select=*",

	$_SESSION["user"]["token"]
);

$loan = $loan[0] ?? null;
$review = $review[0] ?? null;

$title = "Resenha - LÉAMP";

if ($loan === null) {
	echo "<h2 class='error'>Empréstimo não encontrado</h2>";
	return;
} else {
	$title = "Resenha: ".$loan["book"]["title"]." - LÉAMP";
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

	$data = [
		"comment" => $_POST["comment"],
		"favorite_excerpt" => $_POST["favorite_excerpt"],
		"review" => $_POST["review"],
		"rating" => $_POST["rating"],
		"typing_time" => $_POST["typing_time"],
		"used_paste" => $_POST["used_paste"],
	];

	$result = [
		"code" => 0,
		"message" => "unknown error",
	];

	// se já existe resenha, atualiza. senão, cria nova
	if ($review) {
		$data["updated_at"] = date("c");
		$result = supabasePatch(
			"reviews?loan_id=eq.".$review["loan_id"],
			$data,
			$_SESSION["user"]["token"]
		);
	} else {
		$data["loan_id"] = $loan_id;
		$data["used_paste"] = $review["used_paste"]==="1" ? "1" : $_POST["used_paste"];
		$result = supabasePost(
			"reviews",
			$data,
			$_SESSION["user"]["token"]
		);
	}

//	file_put_contents("php://stderr", print_r($result, true));

	if (hasErrorCode($result)) {
		flash("error", "Erro ao ".($review?"atualizar":"registrar")." resenha: ".$result["message"]);
	} else {
		flash("success", "Resenha ".($review?"atualizada":"registrada")." com sucesso!");
		session_write_close();
		header("Location: /livro?id=".$loan["book"]["id"]);
	}
}

?>
<link rel="stylesheet" href="/css/resenha.css">

<h2>Resenha de: <?=$loan["book"]["title"]?></h2>

<div class="form-page">
	<form class="review-form" method="POST">
		<label>
			<h3>Classificação:</h3>
		</label>
		<?php $rating = $review["rating"] ?? 0;?>
		<div class="stars">
			<input type="radio" name="rating" value="0" id="star0" <?= $rating == 0 ? "checked" : "" ?>>
			<label for="star0">Não classificar ∣ </label>
			<label for="star1">☆</label>
			<input type="radio" name="rating" value="1" id="star1" hidden <?= $rating == 1 ? "checked" : "" ?>>
			<label for="star2">☆</label>
			<input type="radio" name="rating" value="2" id="star2" hidden <?= $rating == 2 ? "checked" : "" ?>>
			<label for="star3">☆</label>
			<input type="radio" name="rating" value="3" id="star3" hidden <?= $rating == 3 ? "checked" : "" ?>>
			<label for="star4">☆</label>
			<input type="radio" name="rating" value="4" id="star4" hidden <?= $rating == 4 ? "checked" : "" ?>>
			<label for="star5">☆</label>
			<input type="radio" name="rating" value="5" id="star5" hidden <?= $rating == 5 ? "checked" : "" ?>>
		</div>

		<div class="review-help">
			<strong>
				Sobre a resenha:
			</strong>
			<p>
				Escreva com suas próprias
				palavras.

				Vale comentar:
				personagens,
				partes favoritas,
				o que sentiu lendo
				ou se recomendaria
				o livro.
			</p>
		</div>

		<label for="comment">
			<h3>Comentário:</h3>
		</label>
		<textarea
			name="comment"
			spellcheck="true"
			lang="pt-BR"
			autocapitalize="sentences"
			autocomplete="on"
			autocorrect="on"
			rows="7"
			placeholder="O que você achou do livro?"
			class="protegido"
		><?= $review["comment"] ?? "" ?></textarea>

		<label for="favorite_excerpt">
			<h3>Trecho favorito:</h3>
		</label>
		<textarea
			name="favorite_excerpt"
			spellcheck="true"
			lang="pt-BR"
			autocapitalize="sentences"
			autocomplete="on"
			autocorrect="on"
			rows="3"
			placeholder="Transcreva a sua parte favorita do livro '<?=$loan["book"]["title"]?>': Pode ser uma frase, um parágrafo ou uma cena inteira."
		><?= $review["favorite_excerpt"] ?? "" ?></textarea>

		<label for="review">
			<h3>Resenha:</h3>
		</label>
		<textarea
			name="review"
			spellcheck="true"
			lang="pt-BR"
			autocapitalize="sentences"
			autocomplete="on"
			autocorrect="on"
			rows="13"
			placeholder="Resenha extendida sobre o livro, usada para avaliação detalhada. Escreva sobre o enredo, personagens, temas, estilo de escrita e sua opinião geral."
			class="protegido"
			required
		><?= $review["review"] ?? "" ?></textarea>

		<button type="submit" class="button green">
			↑ <?=$review ? "Atualizar" : "Enviar"?> resenha
		</button>

		<a href="/" class="button red">
			⨯ Cancelar
		</a>

	</form>
</div>

<script src="/js/resenha.js"></script>