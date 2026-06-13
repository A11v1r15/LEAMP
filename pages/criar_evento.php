<?php

require_once "includes/cache.php";

requireAdmin();

$page_title = "Criar evento - LÉAMP";

$events = getCacheOrFetch(
	"eventos",
	"events?".
	"select=*"
);

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
	$result = supabasePost(
		"events", [
			"title" => $_POST["title"],
			"edition" => $_POST["edition"],
			"location" => $_POST["location"],
			"description" => $_POST["description"],
			"start_time" => $_POST["start_time"],
			"end_time" => $_POST["end_time"],
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
		<input type="text" name="title" required>

		<label for="edition">
			<h3>Edição:</h3>
		</label>
		<input type="number" name="edition" min="1">

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
		></textarea>

		<label for ="location">
			<h3>Local:</h3>
		</label>
		<input type="text" name="location" list="locations" required>

		<label for="start_time">
			<h3>Data de início:</h3>
		</label>
		<input type="datetime-local" name="start_time" required>

		<label for="end_time">
			<h3>Data de término:</h3>
		</label>
		<input type="datetime-local" name="end_time">

		<label for="draft">
			<input
				type="checkbox"
				name="draft"
				value="1"
			>
			Marcar como rascunho
		</label>

		<button
			type="submit"
			name="action"
			value="finish"
			class="button green"
			>← Registrar evento
		</button>

		<a href="/" class="button red">
			⨯ Cancelar
		</a>
	</form>
</div>