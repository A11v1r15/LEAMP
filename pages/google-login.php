<?php

require_once "includes/supabase.php";

$redirect =
	urlencode("http://localhost:8000/auth-callback");

$url =
	$SUPABASE_URL .
	"/auth/v1/authorize" .
	"?provider=google" .
	"&redirect_to=$redirect";

header("Location: $url");

exit;