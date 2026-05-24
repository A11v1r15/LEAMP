<?php

	function isLogged() {
		return isset($_SESSION["user"]);
	}

	function isAdmin() {
	return
		isLogged()
		&& $_SESSION["user"]["role"]
			=== "Concedente";
	}

	function requireLogged() {
		if (!isLogged()) {
			http_response_code(403);
			include "pages/403.php";
			exit;
		}
	}

	function requireAdmin() {

		if (!isAdmin()) {
			http_response_code(403);
			include "pages/403.php";
			exit;
		}
	}