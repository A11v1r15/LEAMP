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

function isAuthorised() {
	return isLogged() &&
	$_SESSION["user"]["role"] !== "Pendente";
}

function isReviewer() {
return
	isLogged() &&
	($_SESSION["user"]["role"] === "Concedente" ||
	$_SESSION["user"]["role"] === "Revisor");
}

function isAdmin() {
return
	isLogged() &&
	$_SESSION["user"]["role"] === "Concedente";
}

function requireAuthorised() {
	if (!isAuthorised()) {
		throw new HttpError(
			403, "pages/403.php"
		);
	}
}

function requireLogged() {
	if (!isLogged()) {
		throw new HttpError(
			403, "pages/403.php"
		);
	}
}

function requireReviewer() {
	if (!isReviewer()) {
		throw new HttpError(
			403, "pages/403.php"
		);
	}
}

function requireAdmin() {
	if (!isAdmin()) {
		throw new HttpError(
			403, "pages/403.php"
		);
	}
}