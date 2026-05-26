<?php

	require_once "includes/supabase.php";

	requireLogged();

	$titulo = "Ranking - LÉAMP";

	$ranking = supabaseGet(
		"ranking?".
		"select=*".
		"&limit=10",

		$_SESSION["user"]["token"]
	);

?>
<link rel="stylesheet" href="/css/ranking.css">

<h2>Ranking de leitores</h2>

<div class="ranking-list">

<?php foreach ($ranking as $i => $user): ?>

	<div class="loan-card">

		<div class="ranking-position">
			#<?= $i + 1 ?>
		</div>

		<div class="avatar-wrapper">
			<img
				src="<?= htmlspecialchars(
					$user["avatar"]
				) ?>"
				class="loan-avatar"
			>
			<?php if ($i === 0): ?>
				<img
					class="crown"
					src="/img/Crown.png"
					alt="Crown"
				>
			<?php endif; ?>
		</div>

		<div class="loan-info">

			<div class="loan-title">

				<?= htmlspecialchars(
					$user["name"]
				) ?>

			</div>

			<div class="loan-deadline">

				<?= $user["total"] ?>
				empréstimos

			</div>

		</div>

	</div>

<?php endforeach; ?>

</div>