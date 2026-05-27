
<?php 
	include_once "includes/auth.php";
	include_once "includes/ui.php";
	requireLogged();

	$titulo = "Perfil - LÉAMP";

	$loans = supabaseGet(
	"loans?".
	"reader=eq.".$_SESSION["user"]["uuid"].
	"&select=".
		"id,".
		"deadline,".
		"is_active,".
		"book:book_id(".
			"id," .
			"title" .
		")",

	$_SESSION["user"]["token"]
);
?>
<link rel="stylesheet" href="/css/perfil.css">

<div class="profile-header">
	<img
		src="<?=htmlspecialchars(
			$_SESSION["user"]["avatar"]
		)?>"
		class="profile-avatar"
		alt="Foto de perfil"
	>

	<div class="profile-info">
		<h2 class="profile-name">
			Olá,
			<?=htmlspecialchars(
				$_SESSION["user"]["name"]
			)?>!
		</h2>

		<div class="profile-email">
			<?=htmlspecialchars(
				$_SESSION["user"]["email"]
			)?>
		</div>

		<div class="profile-role">
			<span class="profile-role-badge <?=colorClass($_SESSION["user"]["role"])?>">
				<?=htmlspecialchars(
					$_SESSION["user"]["role"]
				)?>
			</span>
		</div>
	</div>
</div>

<h2>Empréstimos:</h2>
<div class="profile-loans">
	<?php foreach ($loans as $loan):?>
		<?php
			$book = $loan["book"];
			$review_url = "/resenha?"."id=".$loan["id"];
		?>

		<div class="profile-loan-card">
			<div class="profile-loan-main">
				<a
					href="/livro?id=<?=$book["id"]?>"
					class="profile-loan-title"
				><?=htmlspecialchars($book["title"])?>
				</a>

				<div class="profile-loan-meta">
					<span>
						Até <?=date("d/m/Y", strtotime($loan["deadline"]))?>
					</span>
					<?php if ($loan["is_active"]):?>
						<span class="status green
						">Em andamento</span>
					<?php else:?>
						<span class="status gray
						">Finalizado</span>
					<?php endif;?>
				</div>
			</div>

			<div class="profile-loan-actions">
				<a
					href="<?=$review_url?>"
					class="button blue"
				>🖉<?=$loan["is_active"]
						? "Escrever resenha"
						: "Editar resenha"
					?>
				</a>
			</div>
		</div>
	<?php endforeach;?>
</div>