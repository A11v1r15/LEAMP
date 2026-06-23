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

	$page_title = buildEventTitle($event)." - LÉAMP";

	if (isAuthorised()){
		$presences = supabaseGet(
			"presences?".
			"event_id=eq.".$event["id"].
			"&select=".
				"created_at,".
				"attendee:attendee(".
					"uuid,".
					"name,".
					"avatar".
				")".
				"&order=created_at.asc",

			$_SESSION["user"]["token"]
		);
	}

	if ($_SERVER["REQUEST_METHOD"] === "POST" && isAdmin()) {
		$result = supabasePatch(
			"events?".
			"id=eq.$id", [
				"status" => ucfirst(substr($_POST["action"], 0, -1)."do")
			],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			flash("error", "Erro ao ".$_POST["action"]." evento: " . $result["message"]);
		} else {
			flash("success", buildEventTitle($event)." ".substr($_POST["action"], 0, -1)."do com sucesso!");
			cacheDelete("eventos");
			session_write_close();
			header("Location: /eventos");
		}
		exit;
	}

	function canRegisterPresence($event) {
		if ($event["status"] !== "Publicado") {
			return false;
		}
		$start = strtotime($event["start_time"]);
		return time() >= ($start - 1800);
	}
?>
<div class="main-page-container">
	<div class="main-page">
		<div class="event-header">
			<h2>
				<?=buildEventTitle($event)?>
			</h2>
			<?=buildStatus(getEventStatus($event))?>
		</div>

		<p>
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
			<p>
				<b>Local:</b>
				<?=htmlspecialchars($event["location"])?>
			</p>
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
			<?php if(($event["status"] ?? "") === "Publicado"):?>
				<form method="POST" class="inline-form">
					<?php if(time() >= strtotime($event["start_time"])):?>
						<?=buildFormButton("green",
							"finalizar", "✓ Finalizar evento")?>
					<?php endif;?>
					<?=buildFormButton("red",
						"cancelar", "⨯ Cancelar evento")?>
				</form>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
<?php if (isAuthorised()):?>
	<h2>Participantes:</h2>
	<?php if (isAdmin()):?>
		<?php if (canRegisterPresence($event)):?>
			<?=buildAButton("blue",
				"/presenca?id=".$event["id"], "↓ Adicionar presença")?>
		<?php elseif(($event["status"] ?? "") === "Publicado"):?>
			<?=buildSmallCard([
				"color" => "yellow",
				"text" => "O registro de presença será liberado 30 minutos antes do início do evento."
			]);
			$adminYellowCard = true;?>
		<?php endif;?>
	<?php endif;?>
	<?php if (empty($presences)): ?>
		<?php if (!isset($adminYellowCard)): ?>
			<?=buildSmallCard([
				"color" => "gray",
				"text" => (time() >= strtotime($event["start_time"]))?
					"Nenhuma presença registrada.":"O evento ainda não está aceitando presenças."
			])?>
		<?php endif;?>
	<?php else: ?>
		<div class="small-card-container">
			<?php foreach ($presences as $presence): ?>
				<?=buildSmallCard([
					"user" => $presence["attendee"],
					"title" => $presence["attendee"]["name"],
					"deadline" => "Registrado em ".
						date("d/m/Y H:i", strtotime($presence["created_at"]))])?>
			<?php endforeach;?>
		</div>
	<?php endif; ?>
<?php endif;?>