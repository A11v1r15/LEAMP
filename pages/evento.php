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

	$page_title = $event["title"].(!empty($event["edition"])?" - ".toRoman((int)$event["edition"]):"")." - LÉAMP";

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
			flash("success", $event["title"].(!empty($event["edition"])?" - ".toRoman((int)$event["edition"]):"")." ".substr($_POST["action"], 0, -1)."do com sucesso!");
			cacheDelete("eventos");
			session_write_close();
			header("Location: /eventos");
		}
		exit;
	}

?>

<h2><?=htmlspecialchars($event["title"].(!empty($event["edition"])?" - ".toRoman((int)$event["edition"]):""))?></h2>

<div class="event-card">
	<div class="event-header">
		<h3>
			<?=htmlspecialchars(
				$event["title"]
			)?>
			<?php if (!empty($event["edition"])
			): ?> - <?= toRoman((int)$event["edition"])?>
			<?php endif; ?>
		</h3>
		<span class="status <?=
			colorClass($event["status"])?>">
			<?=htmlspecialchars($event["status"])?>
		</span>
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
		<a
			href="/criar_evento?id=<?=$event["id"]?>"
			class="button blue"
		>→ Editar evento ou lançar nova edição
		</a>
		<form method="POST" class="inline-form">
			<button
				type="submit"
				name="action"
				value="finalizar"
				class="button green"
			>✓ Finalizar evento
			</button>
			<button
				type="submit"
				name="action"
				value="cancelar"
				class="button red"
			>⨯ Cancelar evento
			</button>
		</form>
	<?php endif;?>
</div>