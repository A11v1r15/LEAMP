
<?php if (!isset($_SESSION["user"])):
		header("Location: /403");
		exit;
	else: ?>
		<h2>Olá, <?= $_SESSION["user"]["name"] ?>!</h2>
		<img src="<?= $_SESSION["user"]["avatar"] ?>" alt="Foto de perfil" class="profile-picture"/>
		<p>Email: <?= $_SESSION["user"]["email"] ?></p>
<?php endif; ?>