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
	"&select=".
		"book:book_id(".
			"id,".
			"title".
		"),".
		"reader:reader(".
			"uuid,".
			"name".
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

$page_title = "Resenha - LÉAMP";

if ($loan === null) {
	echo "<h2 class='error'>Empréstimo não encontrado</h2>";
	return;
} else {
	$page_title = "Resenha: ".$loan["book"]["title"]." - LÉAMP";
}

function isNotTheReader() {
	global $loan;
	return $loan["reader"]["uuid"] !== $_SESSION["user"]["uuid"];
}

if (isNotTheReader() && !isReviewer()) {
	throw new HttpError(
		403, "pages/403.php"
	);
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$action = $_POST["action"];

	if ($action === "accept" && isReviewer()) {
		if (!$review) {
			flash("error", "Resenha não encontrada para este empréstimo.");
			session_write_close();
			header("Location: /livro?id=".$loan["book"]["id"]);
			exit;
		}

		$result = supabasePatch(
			"reviews?".
			"loan_id=eq.".$review["loan_id"],
			[
				"status" => "Aprovado",
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao aceitar resenha: ".$result["message"]);
		} else {
			flash("success", "Resenha aceita com sucesso!");
			session_write_close();
			header("Location: /livro?id=".$loan["book"]["id"]);
		}
		exit;

	} else if ($action === "submit") {
		$data = [
			"comment" => $_POST["comment"],
			"favorite_excerpt" => $_POST["favorite_excerpt"],
			"review" => $_POST["review"],
			"rating" => $_POST["rating"],
			"typing_time" => $_POST["typing_time"],
			"used_paste" => $_POST["used_paste"],
			"status" => "Pendente",
		];

		$result = [
			"code" => 0,
			"message" => "unknown error",
		];

		// se já existe resenha, atualiza. senão, cria nova
		if ($review) {
			$data["updated_at"] = date("c");
			if (isNotTheReader()) {
				$data["status"] = $review["status"];
			}
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
			exit;
	}
}

?>
<link rel="stylesheet" href="/css/resenha.css">

<h2>Resenha de: <?=$loan["book"]["title"]?></h2>
<?php if (isNotTheReader() && $review === null): ?>
	<h3><?=$loan["reader"]["name"]?> ainda não escreveu uma resenha para este livro.</h3>
<?php return;
	endif; ?>
<?= ($review === null)?
	"<span class='status gray'>Nova</span>"
	:(($review["status"] === "Aprovado")?
	"<span class='status green'>Aprovado</span>"
	:"<span class='status yellow'>Pendente</span>")
?>

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

		<?php if (isNotTheReader()): ?>
			<?=buildSmallCard($card=[
				"color" => "blue",
				"strong" => "Sobre aceitar a resenha:",
				"text" =>
					"Aceitar a resenha significa
					que o comentário dela passará a ser exibida
					na página do livro, contribuindo
					para a avaliação geral da obra.

					Você pode aceitar resenhas que
					estejam bem escritas, sejam
					construtivas e reflitam uma
					opinião honesta sobre o livro.

					Resenhas ofensivas, irrelevantes
					ou que não agreguem valor à comunidade
					podem ser rejeitadas."
			])?>
		<?php else: ?>
			<?=buildSmallCard($card=[
				"color" => "blue",
				"strong" => "Sobre o comentário:",
				"text" =>
					"Escreva com suas próprias
					palavras.

					Seja cordial e respeitoso,
					mesmo que tenha opiniões negativas
					sobre a obra. Lembre-se de que
					o comentário é para ajudar outros
					leitores e não para atacar o autor
					ou a obra.

					Apenas o comentário será exibido
					na página do livro, então evite
					spoilers ou detalhes que possam
					estragar a experiência de outros leitores."
			])?>
		<?php endif; ?>
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

		<?php if (!isNotTheReader()): ?>
			<?=buildSmallCard($card=[
				"color" => "blue",
				"strong" => "Sobre a resenha:",
				"text" =>
					"Escreva com suas próprias
					palavras.

					Vale comentar:
					personagens,
					partes favoritas,
					o que sentiu lendo
					ou se recomendaria
					o livro.

					A resenha serve para você
					ser avaliado quanto ao seu
					entendimento sobre a obra."
			])?>
		<?php endif; ?>
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

		<?php if (!isNotTheReader() && $review["status"] === "Aprovado"): ?>
			<?=buildSmallCard($card=[
				"color" => "yellow",
				"strong" => "Atenção!",
				"text" =>
					"Atualizar a resenha de um livro que já foi aprovada
					fará com que ela volte para o status \"Pendente\",
					ou seja, precisará ser aprovada novamente por ".(isReviewer() ? "outro" : "um")."
					revisor para voltar a ser exibida na página do livro."
			])?>
		<?php endif; ?>
		<?=buildFormButton(isNotTheReader()?"blue":"green",
			"submit", "↑ ".($review?
				(isNotTheReader()?
					"Modificar":"Atualizar")
				:"Enviar").
				" resenha")?>
		<?php if (isNotTheReader()): ?>
			<?=buildFormButton("green",
				"accept", "✓ Aceitar resenha")?>
		<?php endif; ?>

		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>

<script src="/js/resenha.js"></script>