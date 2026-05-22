<?php
	session_start();
	$path = trim(
		parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
		"/"
	);

	$pagina = $path ?: "inicio";
	$arquivo = "pages/" . $pagina . ".php";
	if (!file_exists($arquivo)) {
		http_response_code(404);
		$arquivo = "pages/404.php";
	}
	$titulo = "Ler é a Minha Praia";
	include_once "includes/auth.php";
	include_once "includes/supabase.php";
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
	<link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.css"/>
	<link rel="stylesheet" href="/css/main.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.datatables.net/2.3.1/js/dataTables.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
	<link rel="icon" type="image/png" href="/img/Logo Ler é a minha praia.png">
</head>
<script>

	function toggleMenu(menuId) {

		document
			.getElementById(menuId)
			.classList
			.toggle("active");
	}

	const supabaseClient = supabase.createClient(
		<?php echo "\"".$SUPABASE_URL."\","."\"".$SUPABASE_ANON_KEY."\""?>
	);

	async function loginGoogle() {

		await supabaseClient.auth.signInWithOAuth({

			provider: "google",

			options: {
				redirectTo:
					window.location.origin +
					"/auth-callback"
			}

		});
	}

</script>
<body>

	<header>
		<img src="/img/Logo Ler é a minha praia.png" alt="Logo Ler é a minha praia">

		<div>
			<h1>LER É A MINHA PRAIA</h1>
			<p>Projeto de incentivo à leitura</p>
		</div>
	</header>

	<nav>
		<button class="menu-toggle" onclick="toggleMenu('nav-menu')">
			☰
		</button>
		<ul id="nav-menu">
			<li><a href="/">Início</a></li>
			<li><a href="/livros">Livros</a></li>
			<li><a href="/ranking">Ranking</a></li>
			<li><a href="/eventos">Eventos</a></li>
			<li><a href="/contatos">Contato</a></li>
		</ul>
	</nav>

	<div class="container">

		<aside>
			<button class="side-toggle" onclick="toggleMenu('side-menu')">
				⋮
			</button>
			<div id="side-menu">
				<h2>Menu</h2>

				<ul>
					<?php if (isLogged()): ?>
						<li><a href="/perfil"><?= $_SESSION["user"]["name"] ?></a></li>
						<li><a href="/logout">Sair</a></li>
					<?php else: ?>
						<li><a onclick="loginGoogle()">Login</a></li>
					<?php endif; ?>
					<?php if (isAdmin()): ?>
						<li><a href="/doar">Adicionar livros</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</aside>

		<main>
			<?php include $arquivo; ?>
		</main>

	</div>

	<footer>

		<div class="footer-content">

			<div class="footer-logos">
				<img src="/img/Logo IFCE Campus Camocim.png" alt="IFCE Campus Camocim">
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
				<img src="/img/Logo Eu Faço Parte.png" alt="Eu Faço Parte">
			</div>
		</div>

	</footer>

</body>
</html>