<?php

require_once "includes/supabase.php";

$protocol =
	(
		!empty($_SERVER["HTTPS"]) &&
		$_SERVER["HTTPS"] !== "off"
	)
		? "https"
		: "http";

$host = $_SERVER["HTTP_HOST"];

$redirect = urlencode(
	"$protocol://$host/auth-callback"
);

$url =
	$SUPABASE_URL .
	"/auth/v1/authorize" .
	"?provider=google" .
	"&redirect_to=$redirect";

header("Location: $url");

exit;