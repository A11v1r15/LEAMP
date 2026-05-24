<?php

$SUPABASE_URL = "https://exriifeecupaansgczgp.supabase.co";
$SUPABASE_ANON_KEY = "sb_publishable_KpCIiH4-5AOu-rWwZU8KOw_NJ1qTfhr";

function supabaseGet($endpoint, $userToken = null) {

	global $SUPABASE_URL;
	global $SUPABASE_ANON_KEY;

	$auth = $userToken ?? $SUPABASE_ANON_KEY;

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "$SUPABASE_URL/rest/v1/$endpoint",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => [
			"apikey: $SUPABASE_ANON_KEY",
			"Authorization: Bearer $auth",
			"Content-Type: application/json"
		]
	]);

	$response = curl_exec($curl);

	return json_decode($response, true);
}

function supabasePost($table, $data, $userToken = null) {

	global $SUPABASE_URL;
	global $SUPABASE_ANON_KEY;

	$auth = $userToken ?? $SUPABASE_ANON_KEY;

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "$SUPABASE_URL/rest/v1/$table",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => json_encode($data),
		CURLOPT_HTTPHEADER => [
			"apikey: $SUPABASE_ANON_KEY",
			"Authorization: Bearer $auth",
			"Content-Type: application/json",
			"Prefer: return=representation"
		]
	]);

	$response = curl_exec($curl);

	return json_decode($response, true);
}

function supabasePatch($endpoint, $data, $userToken = null) {

	global $SUPABASE_URL;
	global $SUPABASE_ANON_KEY;

	$auth = $userToken ?? $SUPABASE_ANON_KEY;

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "$SUPABASE_URL/rest/v1/$endpoint",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => "PATCH",
		CURLOPT_POSTFIELDS => json_encode($data),
		CURLOPT_HTTPHEADER => [
			"apikey: $SUPABASE_ANON_KEY",
			"Authorization: Bearer $auth",
			"Content-Type: application/json",
			"Prefer: return=minimal"
		]
	]);

	$response = curl_exec($curl);

	return json_decode($response, true);
}