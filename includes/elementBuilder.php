<?php

include_once "includes/util.php";

function buildStatus($type) {
	return
		"<span class='status ".
		colorClass($type).
		"'>".
		htmlspecialchars($type).
		"</span>";
}

function buidEventTitle($event) {
	return
		htmlspecialchars($event["title"]).
		(!empty($event["edition"])?
		" - ".toRoman((int)$event["edition"]):"");
}