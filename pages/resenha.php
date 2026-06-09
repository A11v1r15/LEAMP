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

$title = "Resenha - LÉAMP";

if (!is_array($loan)) {
	$loan = null;
} else {
	$loan = $loan[0];
	$title = "Resenha: ".$loan["book"]["title"]." - LÉAMP";
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

	$result = supabasePost(
		"reviews", [
			"loan_id" => $loan_id,
			"comment" => $_POST["comment"],
			"favorite_excerpt" => $_POST["favorite_excerpt"],
			"review" => $_POST["review"],
			"rating" => $_POST["rating"],
			"typing_time" => $_POST["typing_time"],
			"used_paste" => $_POST["used_paste"],
			"status" => "Pendente"
		],
		$_SESSION["user"]["token"]
	);

	echo "<p>Resenha registrada!</p>";

	header("Location: /livro?id=".$loan["book"]["id"]);
	file_put_contents("php://stderr", print_r($result, true));
}

?>
<link rel="stylesheet" href="/css/resenha.css">

<h2>Resenha de: <?=$loan["book"]["title"]?></h2>

<div class="form-page">
	<form class="review-form" method="POST">
		<label>
			<h3>Classificação:</h3>
		</label>
		<div class="stars">
			<input type="radio" name="rating" value="0" id="star0" checked>
			<label for="star0">Não classificar ∣ </label>
			<label for="star1">☆</label>
			<input type="radio" name="rating" value="1" id="star1" hidden>
			<label for="star2">☆</label>
			<input type="radio" name="rating" value="2" id="star2" hidden>
			<label for="star3">☆</label>
			<input type="radio" name="rating" value="3" id="star3" hidden>
			<label for="star4">☆</label>
			<input type="radio" name="rating" value="4" id="star4" hidden>
			<label for="star5">☆</label>
			<input type="radio" name="rating" value="5" id="star5" hidden>
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
		></textarea>

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
		></textarea>

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
		></textarea>

		<div class="review-meta">
			<span id="typingTime">
				0 segundos
			</span>
			•
			<span id="charCount">
				0 caracteres
			</span>
		</div>

		<button type="submit" class="button green">
			↑ Enviar resenha
		</button>

		<a href="/" class="button red">
			⨯ Cancelar
		</a>

	</form>
</div>

<script src="/js/resenha.js"></script>