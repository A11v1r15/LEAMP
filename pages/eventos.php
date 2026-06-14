<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";
	include_once "includes/util.php";

	$page_title = "Eventos - LÉAMP";

	$events = getCacheOrFetch(
		"eventos",
		"events?".
		"select=*".
		"&order=start_time.asc"
	);

	if (!is_array($events)) {
		$events = [];
	}

	$published = [];
	$archived = [];

	foreach ($events as $event) {
		if (($event["status"] ?? "") === "Publicado") {
			$published[] = $event;
		} elseif (
				(($event["status"] ?? "") !== "Rascunho") ||
				((($event["status"] ?? "") === "Rascunho") && isAdmin())
			) {
			$archived[] = $event;
		}
	}
?>

<h2>Eventos</h2>

<?php if (!empty($published)): ?>
	<div class="big-card-container">
		<?php foreach ($published as $event): ?>
			<?=buildBigCard([
				"title" => buidEventTitle($event),
				"status" => getEventStatus($event),
				"labelsText" => [
					["Data: ", date("d/m/Y H:i", strtotime($event["start_time"])).
						(empty($event["end_time"])?"":
							" até ".date("d/m/Y H:i", strtotime($event["end_time"])))],
					["Local: ", $event["location"]],
					["Descrição:", "\n".$event["description"]]
				],
				"extra" => buildAButton("blue",
					"/evento?id=".$event["id"], "→ Visualizar evento")
			])?>
		<?php endforeach; ?>
	</div>
<?php else:?>
	<p>Nenhum evento publicado.</p>
<?php endif;?>


<?php if (!empty($archived)): ?>
	<h2>Outros eventos</h2>

	<table id="eventTable">
		<thead>
			<tr>
				<th>Evento</th>
				<th>Status</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($archived as $event): ?>
				<tr>
					<td>
						<a href="/evento?id=<?=$event["id"]?>"
						><?=buidEventTitle($event)?>
						</a>
					</td>

					<td>
						<?=buildStatus(getEventStatus($event))?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<script> new DataTable("#eventTable"); </script>
<?php endif; ?>