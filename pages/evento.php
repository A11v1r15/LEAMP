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
	<?php endif;?>
</div>