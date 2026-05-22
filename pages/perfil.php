
<?php 
	include_once "includes/auth.php";
	requireLogged();
?>

<h2>Olá, <?= $_SESSION["user"]["name"] ?>!</h2>
<img src="<?= $_SESSION["user"]["avatar"] ?>" alt="Foto de perfil" class="profile-picture"/>
<p>Email: <?= $_SESSION["user"]["email"] ?></p>
<p>Nível: <?= $_SESSION["user"]["role"] ?></p>