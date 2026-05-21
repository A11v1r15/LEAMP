<?php

$SUPABASE_URL = "https://exriifeecupaansgczgp.supabase.co";
$SUPABASE_KEY = "sb_publishable_KpCIiH4-5AOu-rWwZU8KOw_NJ1qTfhr";

function supabaseGet($endpoint)
{
	global $SUPABASE_URL, $SUPABASE_KEY;

	$url = $SUPABASE_URL . "/rest/v1/" . $endpoint;

	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"apikey: $SUPABASE_KEY",
		"Authorization: Bearer $SUPABASE_KEY",
		"Content-Type: application/json",
	]);

	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($response === false) {
		$err = curl_error($ch);

		return [
			"error" => true,
			"message" => $err,
		];
	}

	$data = json_decode($response, true);

	if ($httpCode < 200 || $httpCode >= 300) {
		return [
			"error" => true,

			"httpCode" => $httpCode,

			"response" => $data ?? $response,
		];
	}

	return is_array($data) ? $data : [];
}
