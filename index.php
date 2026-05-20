<?php
	$pagina = $_GET["p"] ?? "inicio";
	$arquivo = "pages/" . $pagina . ".php";
	if (!file_exists($arquivo)) {
		$arquivo = "pages/404.php";
	}
	$titulo = "Ler é a Minha Praia";
	ob_start();
	include $arquivo;
	$conteudo = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $titulo ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
	<link rel="icon" type="image/png" href="img/Logo Ler é a minha praia.png">
</head>
<body>

	<header>
		<img src="img/Logo Ler é a minha praia.png" alt="Logo Ler é a minha praia">

		<div>
			<h1>LER É A MINHA PRAIA</h1>
			<p>Projeto de incentivo à leitura</p>
		</div>
	</header>

	<nav>
		<ul>
			<li><a href="?">Início</a></li>
			<li><a href="?p=livros">Livros</a></li>
			<li><a href="?p=ranking">Ranking</a></li>
			<li><a href="?p=eventos">Eventos</a></li>
			<li><a href="?p=contato">Contato</a></li>
		</ul>
	</nav>

	<div class="container">

		<aside>
			<h2>Menu</h2>

			<ul>
				<li><a href="#">Painel</a></li>
				<li><a href="#">Meu Perfil</a></li>
				<li><a href="#">Leituras</a></li>
				<li><a href="#">Certificados</a></li>
				<li><a href="#">Configurações</a></li>
			</ul>
		</aside>

		<main>
			<?= $conteudo ?>
		</main>

	</div>

	<footer>

		<div class="footer-content">

			<div class="footer-logos">
				<img src="img/Logo IFCE Campus Camocim.png" alt="IFCE Campus Camocim">
			</div>

			<div class="footer-text">
				<p>
					Instituto Federal de Educação, Ciência e Tecnologia do Ceará
				</p>

				<p>
					Campus Camocim
				</p>

				<p>
					Projeto Ler é a Minha Praia © <?= date("Y") ?>
				</p>
			</div>

			<div class="footer-logos">
				<img src="img/Logo Eu Faço Parte.png" alt="Eu Faço Parte">
			</div>
		</div>

	</footer>

</body>
</html>