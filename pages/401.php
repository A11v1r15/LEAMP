<?php
	$page_title = "Não autorizado - LÉAMP";
	$quotes = array(
		"Todos os animais são iguais, mas alguns são mais iguais que outros→A revolução dos bichos",
		"Não há barreira, fechadura ou ferrolho que possas impor à liberdade da minha mente→As pupilas do senhor reitor",
		"Você não pode passar!→O Senhor dos Anéis: A sociedade do anel"
		);
	$quote = $quotes[array_rand($quotes)];
	http_response_code(401);
	unset($_SESSION["user"]);
?>

<h2 class="error">Erro 401: Não autorizado</h2>
<p>
	Por favor, faça login novamente.
</p>

<p>
	<?php
		echo "<blockquote>".explode("→", $quote)[0]."</blockquote><p style='text-indent: 20%;'>".explode("→", $quote)[1]."</p>";
	?>
</p>