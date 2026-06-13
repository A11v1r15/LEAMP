<?php

if (!isset($_SESSION["history"])) {
	$_SESSION["history"] = [];
}

$current = $_SERVER["REQUEST_URI"];

if (
	empty($_SESSION["history"]) ||
	end($_SESSION["history"]) !== $current
) {
	$_SESSION["history"][] = $current;

	if (count($_SESSION["history"]) > 10) {
		array_shift($_SESSION["history"]);
	}
}

function previousPage(): string {
	$history = $_SESSION["history"] ?? [];

	if (count($history) < 2) {
		return "/";
	}

	return $history[count($history) - 2];
}

function flash($type, $message) {
	$_SESSION["flash"] = [
		"type" => $type,
		"message" => $message
	];
}

function hasErrorCode($result) {
	return (is_array($result) &&
		isset($result["code"]));
}

function isOverdue($deadline, $isActive) {
	return $isActive && strtotime($deadline) < time();
}

function toRoman($number) {
	$map = [
		1000 => "M",
		900 => "CM",
		500 => "D",
		400 => "CD",
		100 => "C",
		90 => "XC",
		50 => "L",
		40 => "XL",
		10 => "X",
		9 => "IX",
		5 => "V",
		4 => "IV",
		1 => "I"
	];
	$result = "";
	foreach ($map as $value => $roman) {
		while ($number >= $value) {
			$result .= $roman;
			$number -= $value;
		}
	}
	return $result;
}

function colorClass($string): string {
	return match (
		strtolower($string)
	) {

		"disponível",
		"disponivel",
		"ativo",
		"active",
		"concedente",
		"publicado",
		"em andamento",
		"green"
			=> "green",

		"emprestado",
		"revisor",
		"rascunho",
		"yellow"
			=> "yellow",

		"erro",
		"pendente",
		"bloqueado",
		"cancelado",
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