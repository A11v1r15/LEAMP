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
//	file_put_contents('php://stderr', print_r($ranking, TRUE));

?>

<h2>Ranking de leitores</h2>

<div class="small-card-container">
	<?php foreach ($ranking as $i => $user): ?>
		<?=buildSmallCard([
			"color" => "yellow",
			"ranking-position" => $user["position"],
			"user" => $user,
			"ranking" => $ranking,
			"title" => $user["name"],
			"deadline" => $user["loans"]." empréstimos, ".$user["reviews"]." resenhas"
		])?>
	<?php endforeach; ?>
</div>