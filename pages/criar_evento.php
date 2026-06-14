<?php

require_once "includes/cache.php";

requireAdmin();

$id = $_GET["id"] ?? null;

$page_title = "Criar evento - LÉAMP";

$events = getCacheOrFetch(
	"eventos",
	"events?".
	"select=*"
);

$event = supabaseGet(
	"events?".
	"id=eq.$id".
	"&select=*"
);

$event = $event[0] ?? null;

if ($event !== null) {
	$page_title = "Editar: ".buidEventTitle($event)." - LÉAMP";
}

$event_titles = array_unique(
	array_column($events, "title")
);

sort($event_titles);

$event_locations = array_unique(
	array_column($events, "location")
);
sort($event_locations);

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	file_put_contents("php://stderr", print_r($_POST, true));
	if ($event !== null && $_POST["action"] === "edit") {
		$result = supabasePatch(
			"events?".
			"id=eq.$id", [
				"title" => $_POST["title"],
				"edition" => $_POST["edition"],
				"location" => $_POST["location"],
				"description" => $_POST["description"],
				"start_time" => $_POST["start_time"]."-03:00",
				"end_time" => $_POST["end_time"]."-03:00",
				"status" => isset($_POST["draft"]) ? "Rascunho" : "Publicado"
			],
			$_SESSION["user"]["token"]
		);
		if (hasErrorCode($result)) {
			flash("error", "Erro ao editar evento: " . $result["message"]);
		} else {
			flash("success", "Evento editado com sucesso!");
			cacheDelete("eventos");
			session_write_close();
			header("Location: /evento?id=".$id);
		}
	} else {
		$result = supabasePost(
			"events", [
				"title" => $_POST["title"],
				"edition" => $_POST["edition"],
				"location" => $_POST["location"],
				"description" => $_POST["description"],
				"start_time" => $_POST["start_time"]."-03:00",
				"end_time" => $_POST["end_time"]."-03:00",
				"status" => isset($_POST["draft"]) ? "Rascunho" : "Publicado"
			],
			$_SESSION["user"]["token"]
		);
		if (hasErrorCode($result)) {
			flash("error", "Erro ao registrar evento: " . $result["message"]);
		} else {
			flash("success", "Evento criado com sucesso!");
			cacheDelete("eventos");
			session_write_close();
			header("Location: /eventos");
		}
	}

//	file_put_contents("php://stderr", print_r($result, true));
	exit;
}

?>

<h2>Criar evento</h2>

<div class="form-page">
	<form method="POST">
		<datalist id="events">
			<?php foreach ($event_titles as $title): ?>
				<option
					value="<?= htmlspecialchars($title) ?>"
				>
			<?php endforeach; ?>
		</datalist>
		<datalist id="locations">
			<?php foreach ($event_locations as $location): ?>
				<option
					value="<?= htmlspecialchars($location) ?>"
				>
			<?php endforeach; ?>
		</datalist>

		<label for="title">
			<h3>Título:</h3>
		</label>
		<input
			type="text"
			name="title"
			list="events"
			value="<?=htmlspecialchars($event["title"]?? "")?>"
			required>

		<label for="edition">
			<h3>Edição:</h3>
		</label>
		<input
			type="number"
			name="edition"
			min="1"
			value="<?=htmlspecialchars($event["edition"]?? "")?>">

		<label for="description">
			<h3>Descrição:</h3>
		</label>
		<textarea
			name="description"
			spellcheck="true"
			lang="pt-BR"
			autocapitalize="sentences"
			autocomplete="on"
			autocorrect="on"
			rows="7"
		><?=htmlspecialchars($event["description"]?? "")?></textarea>

		<label for ="location">
			<h3>Local:</h3>
		</label>
		<input
			type="text"
			name="location"
			list="locations"
			required
			value="<?=htmlspecialchars($event["location"]?? "") ?>"
		>

		<label for="start_time">
			<h3>Data de início:</h3>
		</label>
		<input
			type="datetime-local"
			name="start_time"
			value="<?=
				!empty($event["start_time"])
					? date("Y-m-d\TH:i",
						strtotime($event["start_time"])
					): ""?>"
			required
		>

		<label for="end_time">
			<h3>Data de término:</h3>
		</label>
		<input 
			type="datetime-local" 
			name="end_time" 
			value="<?=
				!empty($event["end_time"])
					? date("Y-m-d\TH:i",
						strtotime($event["end_time"])
					): ""?>"
		>

		<label for="draft">
			<input
				type="checkbox"
				name="draft"
				value="1"
				<?=(isset($event["status"]) && $event["status"] === "Rascunho")? "checked" : ""?>
			>
			Marcar como rascunho
		</label>

		<?php if($event !== null):?>
			<?=buildFormButton("blue",
				"edit", "↑ Editar evento")?>
			<?=buildFormButton("green",
				"publish", "← Registrar nova edição")?>
		<?php else: ?>
			<?=buildFormButton("green",
				"publish", "← Registrar evento")?>
		<?php endif; ?>

		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
		</a>
	</form>
</div>