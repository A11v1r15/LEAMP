<?php

session_start();

$data = json_decode(
	file_get_contents("php://input"),
	true
);

$_SESSION["user"] = [
	"email" => $data["email"],
	"name" => $data["name"],
	"avatar" => $data["avatar"],
	"token" => $data["token"]
];

echo "ok";