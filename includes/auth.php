<?php
	class HttpError
		extends Exception {

		public $status;
		public $page;

		public function __construct(
			$status, $page
		) {
			$this->status = $status;
			$this->page = $page;
		}
	}

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
			throw new HttpError(
				403, "pages/403.php"
			);
		}
	}

	function requireAdmin() {
		if (!isAdmin()) {
			http_response_code(403);
			throw new HttpError(
				403, "pages/403.php"
			);
		}
	}