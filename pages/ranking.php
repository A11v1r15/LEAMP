<?php

	require_once "includes/supabase.php";

	requireAuthorised();

	$page_title = "Ranking - LÉAMP";

	$ranking = supabaseGet(
		"ranking?".
		"select=*".
		"&limit=10",

		$_SESSION["user"]["token"]
	);

?>

<h2>Ranking de leitores</h2>

<div class="small-card-container">
	<?php foreach ($ranking as $i => $user): ?>
		<?=buildSmallCard([
			"color" => "yellow",
			"ranking-position" => $i+1,
			"user" => $user,
			"ranking" => $ranking,
			"title" => $user["name"],
			"deadline" => $user["total"]." empréstimos"
		])?>
	<?php endforeach; ?>
</div>