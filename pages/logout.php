<?php

session_destroy();

header("Location: <?=htmlspecialchars(previousPage())?>");

exit;