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
	<?php foreach ($published as $event): ?>
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

			<a
				href="/evento?id=<?=$event["id"]?>"
				class="button blue"
			>→ Visualizar evento
			</a>

		</div>
	<?php endforeach; ?>
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
						><?= htmlspecialchars($event["title"]) ?>
						<?php if (!empty($event["edition"])
						):?> - <?= toRoman((int)$event["edition"]) ?>
						<?php endif;?>
						</a>
					</td>

					<td>
						<span class="status <?=
							colorClass($event["status"])?>">
							<?=htmlspecialchars($event["status"])?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<script> new DataTable("#eventTable"); </script>
<?php endif; ?>