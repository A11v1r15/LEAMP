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
	"users?uuid=eq.$uuid&select=*",
	$token
);

if (empty($user)) {
	supabasePost(
		"users",
		[
			"uuid" => $uuid,
			"role" => "Leitor",
			"name" => $name,
			"avatar" => $avatar
		],
		$token
	);
	$role = "Leitor";
} else {
//	file_put_contents('php://stderr', print_r($user, TRUE));
	$role = $user[0]["role"];
}

$_SESSION["user"] = [
	"uuid" => $uuid,
	"email" => $email,
	"name" => $name,
	"avatar" => $avatar,
	"token" => $token,
	"role" => $role
];

echo "ok";