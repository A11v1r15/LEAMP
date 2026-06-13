<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";
	include_once "includes/util.php";

	$id = $_GET["id"] ?? null;

	if (!$id) {
		echo "<h2 class='error'>Evento não encontrado</h2>";
		return;
	}

	$event = supabaseGet(
		"events?".
		"id=eq.$id".
		"&select=*"
	);

	if (!$event) {
		echo "<h2 class='error'>Evento não encontrado</h2>";
		return;
	}

	$event = $event[0];

	$page_title = buidEventTitle($event)." - LÉAMP";

	if ($_SERVER["REQUEST_METHOD"] === "POST" && isAdmin()) {
		$result = supabasePatch(
			"events?".
			"id=eq.$id",
			[
				"status" => ucfirst(substr($_POST["action"], 0, -1)."do")
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao ".$_POST["action"]." evento: " . $result["message"]);
		} else {
			flash("success", buidEventTitle($event)." ".substr($_POST["action"], 0, -1)."do com sucesso!");
			cacheDelete("eventos");
			session_write_close();
			header("Location: /eventos");
		}
		exit;
	}

?>

<h2><?=buidEventTitle($event)?></h2>

<div class="event-card">
	<div class="event-header">
		<h3>
			<?=buidEventTitle($event)?>
		</h3>
		<?=buildStatus($event["status"])?>
	</div>

	<p class="event-time">
		<b>Data:</b>
		<?= date("d/m/Y H:i", strtotime($event["start_time"]))?>
		<?php if (
			!empty($event["end_time"])
		): ?> até
			<?= date("d/m/Y H:i", strtotime($event["end_time"]))?>
		<?php endif; ?>
	</p>

	<?php if (
		!empty($event["location"])
	): ?>
		<b>Local:</b>
		<?=htmlspecialchars($event["location"])?>
	<?php endif; ?>
	<br>
	<?php if (
		!empty($event["description"])
	): ?>
		<b>Descrição:</b>
		<p class="event-description">
			<?= nl2br(
				htmlspecialchars(
					$event["description"]
				)
			) ?>
		</p>
	<?php endif; ?>
	<?php if (isAdmin()):?>
		<?=buildAButton("blue",
			"/criar_evento?id=".$event["id"], "→ Editar evento ou lançar nova edição")?>
		<form method="POST" class="inline-form">
			<?=buildFormButton("green",
				"finalizar", "✓ Finalizar evento")?>
			<?=buildFormButton("red",
				"cancelar", "⨯ Cancelar evento")?>
		</form>
	<?php endif;?>
</div>