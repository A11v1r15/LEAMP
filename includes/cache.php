<?php

function cacheGet($nome, $tempo = 86400) {

	$arquivo = "cache/" . $nome . ".json";

	if (!file_exists($arquivo)) {
		return null;
	}

	/* expirado */

	if (
		time() - filemtime($arquivo)
		> $tempo
	) {
		return null;
	}

	$json = file_get_contents($arquivo);

	return json_decode($json, true);
}

function cacheSet($nome, $dados) {

	$arquivo = "cache/" . $nome . ".json";

	file_put_contents(
		$arquivo,
		json_encode($dados)
	);
}