<?php

function colorClass($string): string {
	return match (
		strtolower($string)
	) {

		"disponível",
		"disponivel",
		"ativo",
		"active",
		"concedente",
		"green"
			=> "green",

		"emprestado",
		"revisor",
		"yellow"
			=> "yellow",

		"erro",
		"pendente",
		"bloqueado",
		"red"
			=> "red",

		"finalizado",
		"finished",
		"cinza",
		"grey",
		"gray"
			=> "gray",

		"informação",
		"informacao",
		"leitor",
		"blue"
			=> "blue",

		default
			=> "gray"
	};
}