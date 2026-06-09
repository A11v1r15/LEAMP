<?php
	$title = "Eu sou um bule de chá - LÉAMP";
	$quotes = array(
		"Quando certa manhã Gregor Samsa acordou de sonhos intranquilos, encontrou-se em sua cama metamorfoseado num inseto monstruoso→A metamorfose"
		);
	$quote = $quotes[array_rand($quotes)];
	http_response_code(418);
?>

<h2 class="error">Erro 418: Eu sou um bule de chá</h2>

<p>
	<?php
		echo "<blockquote>".explode("→", $quote)[0]."</blockquote><p style='text-indent: 20%;'>".explode("→", $quote)[1]."</p>";
	?>
</p>