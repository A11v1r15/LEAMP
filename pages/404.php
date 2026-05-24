<?php
	$titulo = "Página não encontrada";
	$quotes = array(
		"Nem todos que vagueiam estão perdidos→O Senhor dos Anéis: A sociedade do anel",
		"Quem sou eu no mundo? Ah, essa é a grande charada→Alice no País das Maravilhas",
		"O vazio tem o valor de espaço e de limites→A Hora da Estrela",
		"Sempre a mesma coisa. Ora uma faísca de esperança, ora o mar de desespero que ruge→A Morte de Ivan Ilitch",
		"O essencial é invisível aos olhos→O pequeno príncipe",
		"A minha alma é um labirinto escuro→Dom Casmurro",
		"⁠Como seria agora, se todo o sossego, todo o bem-estar, toda a satisfação chegasse assustadoramente ao fim?→A metamorfose"
		);
	$quote = $quotes[array_rand($quotes)];
	http_response_code(404);
?>

<h2 class="error">Erro 404: Página não encontrada</h2>

<p>
	<?php
		echo "<blockquote>".explode("→", $quote)[0]."</blockquote><p style='text-indent: 20%;'>".explode("→", $quote)[1]."</p>";
	?>
</p>