<?php
	date_default_timezone_set("America/Fortaleza");
	session_start();
	$path = trim(
		parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
		"/"
	);
	$status = 200;

	$pagina = $path ?: "inicio";
	$arquivo = "pages/" . $pagina . ".php";
	if (!file_exists($arquivo)) {
		$status = 404;
		$arquivo = "pages/404.php";
	}
	$page_title = "Ler é a Minha Praia";
	include_once "includes/auth.php";
	include_once "includes/supabase.php";
	include_once "includes/util.php";
	include_once "includes/elementBuilder.php";

	try {
		ob_start();
		require $arquivo;
		$conteudo = ob_get_clean();
	} catch (HttpError $e) {
		$status = $e->status;
		ob_clean();
		ob_start();
		require $e->page;
		$conteudo = ob_get_clean();
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$page_title?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.css"/>
	<link rel="stylesheet" href="/css/main.css">
	<link rel="stylesheet" href="/css/main-dark.css">
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

	function localStorageGetItem(key, value) {
		const storage = localStorage.getItem(key);
		if (storage == null ||
			storage == 'NaN' ||
			storage == '' ||
			storage == 'undefined' ||
			storage == undefined) {
			//console.log("Getting default value for " + key);
			return value;
		}
		return storage;
	}

	function showFlash(message, type = "warning") {
		let flash = document.querySelector(".flash.js-flash");
		if (!flash) {
			flash = document.createElement("div");
			flash.className = `flash js-flash ${type}`;
			document.body.appendChild(flash);
		}
		flash.className = `flash js-flash ${type}`;
		flash.textContent = message;
		flash.classList.remove("hide");
		clearTimeout(flash.hideTimer);
		clearTimeout(flash.removeTimer);
		flash.hideTimer =
			setTimeout(
				() => flash.classList.add("hide"),
				4500
			);
	}
</script>
<body>

	<?php
	if (!empty($_SESSION["flash"])):
		$flash = $_SESSION["flash"];
		unset($_SESSION["flash"]);
	?>
		<div class="flash <?= htmlspecialchars($flash["type"]) ?>">
			<?= htmlspecialchars($flash["message"]) ?>
		</div>
	<?php endif; ?>

	<header>
		<img src="/img/Logo Ler é a minha praia.png" alt="Logo Ler é a minha praia">
		<div>
			<div>
				<h1>LER É A MINHA PRAIA</h1>
				<p>Projeto de incentivo à leitura</p>
			</div>
			<nav>
				<button class="menu-toggle" onclick="toggleMenu('nav-menu')">
					☰
				</button>
				<ul id="nav-menu">
					<li><a href="/">Início</a></li>
					<li><a href="/instrucoes">Instruções</a></li>
					<li><a href="/livros">Livros</a></li>
					<?php if (isLogged()):?>
						<li><a href="/ranking">Ranking</a></li>
					<?php endif;?>
					<li><a href="/eventos">Eventos</a></li>
					<li><a href="/equipe">Equipe</a></li>
				</ul>
			</nav>
		</div>
	</header>

	<div class="container">
		<aside>
			<button class="side-toggle" onclick="toggleMenu('side-menu')">
				⋮
			</button>
			<div id="side-menu">
				<h2>Menu</h2>

				<ul>
					<?php if (isLogged()):?>
						<li><a href="/perfil">Perfil</a></li>
						<li><a href="/logout">Sair</a></li>
					<?php else:?>
						<li><a onclick="loginGoogle()">Login</a></li>
					<?php endif;?>
					<?php if (isAdmin()):?>
						<li><a href="/emprestimos">Lista de empréstimos</a></li>
						<li><a href="/doar">Adicionar livros</a></li>
						<li><a href="/criar_evento">Criar evento</a></li>
					<?php endif;?>
				</ul>
			</div>
		</aside>

		<main>
			<?=$conteudo?>
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
					Projeto Ler é a Minha Praia © <?=date("Y")?>
				</p>
			</div>

			<div class="footer-logos">
				<img src="/img/Logo Eu Faço Parte.png" alt="Eu Faço Parte">
			</div>
		</div>

	</footer>

</body>
<script>
	const termos_version = 1;
	const privacidade_version = 1;
// Mudar ↑ quando atualizar os Termos de Uso e Política de privacidade

	let seen_termos_version = Number(localStorageGetItem("seen_termos_version", termos_version));
	let seen_privacidade_version = Number(localStorageGetItem("seen_privacidade_version", privacidade_version));

	if((termos_version != seen_termos_version) &&
		(privacidade_version != seen_privacidade_version)
	) {
		showFlash("Os Termos de Uso e Políticas de privacidade foram atualizados");
	} else if(termos_version != seen_termos_version) {
		showFlash("Os Termos de Uso foram atualizados");
	} else if(privacidade_version != seen_privacidade_version) {
		showFlash("As Políticas de privacidade foram atualizados");
	}

	localStorage.setItem("seen_termos_version", termos_version);
	localStorage.setItem("seen_privacidade_version", privacidade_version);
</script>
</html>