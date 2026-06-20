<?php

require_once "includes/supabase.php";
require_once "includes/cache.php";

requireAdmin();

$page_title = "Presença - LÉAMP";
$event_id = $_GET["id"] ?? null;

$users = supabaseGet(
	"users?".
	"select=uuid,name,avatar",

	$_SESSION["user"]["token"]
);
$presentUsers = supabaseGet(
	"presences?".
	"event_id=eq.$event_id".
	"&select=attendee",

	$_SESSION["user"]["token"]
);
$presentIds = array_column(
	$presentUsers,
	"attendee"
);
$users = array_filter(
	$users,
	fn($u) => !in_array(
		$u["uuid"],
		$presentIds
	)
);
$event = supabaseGet(
	"events?".
	"id=eq.$event_id".
	"&select=*"
);
$event = $event[0]?? null;
if ($event["title"]) {
	$page_title = "Presença: ".buildEventTitle($event)." - LÉAMP";
}

/* envia formulário */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$result = supabasePost(
		"presences",
		[
			"event_id" => $event_id,
			"attendee" => $_POST["attendee"]
		],
		$_SESSION["user"]["token"]
	);

	$attendee = array_values(
		array_filter(
			$users,
			fn($u) => $u["uuid"] === $_POST["attendee"]
		)
	)[0] ?? null;

	if (hasErrorCode($result)) {
		flash("error", "Erro ao registrar presença: " . $result["message"]);
	} else {
		flash("success", "Presença de ".$attendee["name"]." em ".buildEventTitle($event)." registrada com sucesso!");
		session_write_close();
		header("Location: /evento?id=".$event_id);
		exit;
	}

//	file_put_contents("php://stderr", print_r($result, true));
}

?>

<h2>
	Presença em <?=buildEventTitle($event)?>
</h2>

<div class="main-page-container">
	<form class="main-page" method="POST">

		<label for="attendee">
			<h3>Participante:</h3>
		</label>
		<?php if (empty($users)): ?>
			<?=buildSmallCard([
				"color" => "red",
				"deadline" => "Todos os usuários já possuem presença registrada neste evento."
			])?>
		<?php else: ?>
			<select name="attendee" id="attendee-select" required>
				<option value="">Selecione o participante</option>
				<?php foreach ($users as $u):?>
					<option
						value="<?=$u["uuid"]?>"
						data-name="<?=htmlspecialchars($u["name"])?>"
						data-avatar="<?=htmlspecialchars($u["avatar"])?>"
					>
						<?=$u["name"]?>
					</option>
				<?php endforeach;?>
			</select>
			<?=buildSmallCard([
				"id" => "attendee-preview",
				"color" => "yellow",
				"dynamic" => true,
				"deadline" => date("d/m/Y H:i")
			])?>

			<input type="hidden" name="event_id" value="<?=$event_id?>">

			<?=buildFormButton("green",
				"", "→ Registrar presença")?>
		<?php endif; ?>
		<?=buildAButton("red",
			previousPage(), "⨯ Cancelar")?>
	</form>
</div>

<script src="/js/presenca.js"></script>