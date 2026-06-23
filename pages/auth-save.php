<?php

require_once "includes/supabase.php";

$data = json_decode(
	file_get_contents("php://input"),
	true
);

$email = $data["email"];
$name = $data["name"];
$avatar = $data["avatar"];
$token = $data["token"];
$uuid = $data["uuid"];

$user = supabaseGet(
	"users?".
	"uuid=eq.$uuid".
	"&select=*",
	$token
);

$result = null;

if (empty($user)) {
	$role = str_ends_with($email, "ifce.edu.br")?
		"Leitor" : "Pendente";

	$result = supabasePost(
		"users", [
			"uuid" => $uuid,
			"role" => $role,
			"name" => $name,
			"avatar" => $avatar,
			"email" => $email
		],
		$token
	);

	if (is_array($result)
		&& !isset($result["code"])) {
		$_SESSION["flash"] = [
			"type" => "success",
			"message" => "Novo usuário registrado!"
		];
	}
} else {
	if (
		$user[0]["name"] !== $name ||
		$user[0]["avatar"] !== $avatar
		) {
			$result = supabasePatch(
				"users?".
				"uuid=eq.$uuid",[
					"name" => $name,
					"avatar" => $avatar
				],
				$token
			);

			if (is_array($result)
				&& !isset($result["code"])) {
				$_SESSION["flash"] = [
					"type" => "success",
					"message" => "Usuário atualizado!"
				];
			}
	}
	$role = $user[0]["role"];
}
//	file_put_contents('php://stderr', print_r($user, TRUE));

$_SESSION["user"] = [
	"uuid" => $uuid,
	"email" => $email,
	"name" => $name,
	"avatar" => $avatar,
	"token" => $token,
	"role" => $role
];