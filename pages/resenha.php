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

$titulo = "Resenha - LÉAMP";

if (!is_array($loan)) {
	$loan = null;
} else {
	$loan = $loan[0];
	$titulo = "Resenha: ".$loan["book"]["title"]." - LÉAMP";
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


	echo "<p>Resenha registrada!</p>";
//	file_put_contents("php://stderr", print_r($result, true));
}

?>
<link rel="stylesheet" href="/css/resenha.css">

<h2>Resenha de: <?php echo $loan["book"]["title"];?></h2>

<div class="form-page">
	<form class="review-form" method="POST">
		<label for="rating">
			Classificação:
		</label>
		<input type="number" name="rating" min="0" max="5" required>

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
			Comentário:
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
		></textarea>

		<label for="review">
			Resenha:
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