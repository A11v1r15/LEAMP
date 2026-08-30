<?php

	require_once "includes/supabase.php";

	requireAuthorised();

	$page_title = "Ranking - LÉAMP";

	$ranking = supabaseGet(
		"ranking?".
		"position=lte.3".
		"&select=*",

		$_SESSION["user"]["token"]
	);
//	file_put_contents('php://stderr', print_r($ranking, TRUE));

?>

<h2>Ranking de leitores</h2>

<?=buildSmallCard([
	"color" => "blue",
	"title" => "Como funciona o ranking?",
	"text" =>
		"Cada empréstimo realizado vale 2 pontos,
		cada resenha aprovada valem 3 pontos.

		Quanto mais pontos você acumular,
		melhor será sua posição no ranking.

		Em caso de empate, os leitores
		compartilham a mesma colocação.

		Caso já tenha feita uma resenha e
		ainda não esteja contando aqui,
		espere a equipe de revisores aprova-la."
])?>

<div class="small-card-container">
	<?php foreach ($ranking as $i => $user): ?>
		<?=buildSmallCard([
			"color" => "yellow",
			"ranking-position" => $user["position"],
			"user" => $user,
			"ranking" => $ranking,
			"title" => $user["name"],
			"deadline" =>
				$user["loans"]." empréstimos, ".
				$user["reviews"]." resenhas aprovadas"
		])?>
	<?php endforeach; ?>
</div>