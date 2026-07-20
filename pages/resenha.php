<?php

require_once "includes/supabase.php";
require_once "includes/cache.php";

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
	"&select=*,".
		"moderator:moderated_by(".
			"uuid,".
			"name,".
			"avatar,".
			"role".
		")",

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

function isTheReviewer() {
	global $loan;
	return $loan["reader"]["uuid"] !== $_SESSION["user"]["uuid"];
}

function isTheReader() {
	return !isTheReviewer();
}

if (isTheReviewer() && !isReviewer()) {
	throw new HttpError(
		403, "pages/403.php"
	);
}

$defaultFeedbackText =
	"Escreva com suas próprias palavras.

	Este comentário será utilizado para avaliar
	sua compreensão da obra e, após aprovação,
	também será exibido na página do livro para
	ajudar outros leitores.

	Você pode comentar sobre a história,
	personagens, temas, momentos marcantes,
	o que sentiu durante a leitura e se
	recomendaria o livro.

	Seja cordial e respeitoso, mesmo que tenha
	opiniões negativas, e evite spoilers ou
	detalhes que possam prejudicar a experiência
	de quem ainda não leu a obra.";

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
			"loan_id=eq.".$review["loan_id"], [
				"status" => "Aprovado",
				"moderated_by" => $_SESSION["user"]["uuid"],
				"moderated_at" => date("c")
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao aceitar resenha: ".$result["message"]);
		} else {
			flash("success", "Resenha aceita com sucesso!");
			cacheDelete("livros");
			session_write_close();
			header("Location: /livro?id=".$loan["book"]["id"]);
		}
		exit;

	} else if ($action === "submit") {
		$data = [
			"comment" => $_POST["comment"],
			"favorite_excerpt" => $_POST["favorite_excerpt"],
			"feedback" => $_POST["feedback"],
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
			if (isTheReviewer()) {
				$data["status"] = "Devolvido";
				$data["comment"] = $review["comment"];
				$data["rating"] = $review["rating"];
				$data["favorite_excerpt"] = $review["favorite_excerpt"];
				$data["moderated_by"] = $_SESSION["user"]["uuid"];
				$data["moderated_at"] = date("c");
			}
			$result = supabasePatch(
				"reviews?".
				"loan_id=eq.".$review["loan_id"],
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
			if ($review) cacheDelete("livros");
			session_write_close();
			header("Location: /livro?id=".$loan["book"]["id"]);
		}
		exit;
	}
}

?>
<link rel="stylesheet" href="/css/resenha.css">

<h2>Resenha de: <?=$loan["book"]["title"]?></h2>
<?php if (isTheReviewer() && $review === null): ?>
	<h3><?=$loan["reader"]["name"]?> ainda não escreveu uma resenha para este livro.</h3>
<?php return;
	endif; ?>
<?= ($review === null)?	buildStatus("Novo")	: buildStatus($review["status"]);
?>

<div class="main-page-container">
	<form class="main-page" method="POST">
		<?php if (isTheReviewer()): ?>
			<h3>Classificação:</h3>
			<?= buildRating($review["rating"]) ?>
		<?php else: ?>
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
		<?php endif; ?>

		<?php if (isTheReviewer()): ?>
			<?=buildSmallCard([
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
					podem ser devolvidas com feedback."
			])?>
		<?php else: ?>
			<?php if ($review && !empty($review["feedback"])): ?>
				<?=buildSmallCard([
					"color" => "blue",
					"title" => "Feedback do revisor",
					"user" => $review["moderator"],
					"strong" => $review["moderator"]["name"] ?? "Revisor",
					"text" => $review["feedback"]
				])?>
				<?php else: ?>
					<?=buildSmallCard([
						"color" => "blue",
						"strong" => "Sobre o comentário:",
						"text" => $defaultFeedbackText
					])?>
				<?php endif; ?>
		<?php endif; ?>

		<?php if (isTheReviewer()): ?>
			<h3>Comentário:</h3>
			<p><?= $review["comment"] ?? "" ?></p>
			<h3>Trecho favorito:</h3>
			<p><?= $review["favorite_excerpt"] ?? "" ?></p>
		<?php else: ?>
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
				placeholder="Escreva seu comentário sobre o livro. Ele será avaliado e, se aprovado, poderá ser exibido na página da obra."
				class="protegido"
				required
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
		<?php endif;?>

		<?php if (isTheReviewer()): ?>
			<?=buildSmallCard([
				"color" => "blue",
				"strong" => "Sobre o feedback:",
				"text" =>
					"Escreva o motivo da aceitação ou rejeição da resenha do leitor.
					Seja cordial e respeitoso, lembre-se de que o objetivo é ajudar
					o leitor a melhorar sua escrita e compreensão da obra."
			])?>
			<label for="feedback">
				<h3>Feedback:</h3>
			</label>
			<textarea
				name="feedback"
				spellcheck="true"
				lang="pt-BR"
				autocapitalize="sentences"
				autocomplete="on"
				autocorrect="on"
				rows="13"
				placeholder="Por favor, expanda seu comentário"
				required
			><?= $review["feedback"] ?? $defaultFeedbackText ?></textarea>
		<?php endif; ?>
		<?php if (isTheReader() && !empty($review) && $review["status"] === "Aprovado"): ?>
			<?=buildSmallCard([
				"color" => "yellow",
				"strong" => "Atenção!",
				"text" =>
					"Atualizar a resenha de um livro que já foi aprovada
					fará com que ela volte para o status \"Pendente\",
					ou seja, precisará ser aprovada novamente por ".(isReviewer() ? "outro" : "um")."
					revisor para voltar a ser exibida na página do livro."
			])?>
		<?php endif; ?>
		<?php if (!$review && isTheReader()): ?>
			<?=buildFormButton("green",
				"submit", "↑ Enviar resenha")?>
		<?php else: ?>
			<?=buildFormButton("yellow",
				"submit", "🖉 ".(isTheReviewer()?
						"Modificar":"Atualizar").
					" resenha")?>
		<?php endif; ?>
		<?php if (isTheReviewer()): ?>
			<?=buildFormButton("green",
				"accept", "✓ Aceitar resenha")?>
		<?php endif; ?>

		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>

<script src="/js/resenha.js"></script>