
<?php 
	include_once "includes/auth.php";
	include_once "includes/util.php";
	requireLogged();

	$page_title = "Perfil - LÉAMP";

	$loans = supabaseGet(
	"loans?".
	"reader=eq.".$_SESSION["user"]["uuid"].
	"&select=".
		"id,".
		"deadline,".
		"end_date,".
		"is_active,".
		"book:book_id(".
			"id," .
			"title" .
		")",

	$_SESSION["user"]["token"]
	);

	$reviews = supabaseGet(
	"reviews?".
	"&select=".
		"loan_id",

	$_SESSION["user"]["token"]
	);

	$ranking = supabaseGet(
		"ranking?".
		"select=".
			"uuid".
		"&order=total.desc".
		"&limit=1",

		$_SESSION["user"]["token"]
	);
?>
<link rel="stylesheet" href="/css/perfil.css">

<div class="profile-header">
	<div class="avatar-wrapper">
		<img
			src="<?= htmlspecialchars(
				$_SESSION["user"]["avatar"]
			) ?>"
			class="avatar"
		>
			<?php if ($_SESSION["user"]["uuid"] === $ranking[0]["uuid"]):?>
			<img
				class="crown"
				src="/img/Crown.png"
				alt="Crown"
			>
		<?php endif; ?>
	</div>

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
			<span class="profile-role-badge <?=$_SESSION["user"]["role"]?>">
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

		<div class="small-card <?=isOverdue($loan["deadline"], $loan["is_active"])?"red":"green"?>">
			<div class="info">
				<a
					href="/livro?id=<?=$book["id"]?>"
					class="title"
				><?=htmlspecialchars($book["title"])?>
				</a>
				<div>
					<?php if ($loan["is_active"]):?>
						<span class="deadline">
							Até <?=date("d/m/Y", strtotime($loan["deadline"]))?>
						</span>
					<?php else:?>
						<span class="deadline">
							Entregue <?=date("d/m/Y", strtotime($loan["end_date"]))?>
						</span>
					<?php endif;?>
				</div>
			</div>
			<div class="extra">
				<?php if (isOverdue($loan["deadline"], $loan["is_active"])):?>
					<?=buildStatus("Atrasado")?>
				<?php elseif ($loan["is_active"]):?>
					<?=buildStatus("Em andamento")?>
				<?php else:?>
					<?=buildStatus("Finalizado")?>
				<?php endif;?>
				<a
					href="<?=$review_url?>"
					class="button blue"
				>🖉 <?=in_array($loan["id"], array_column($reviews, "loan_id"))
						? "Editar resenha"
						: "Escrever resenha"
					?>
				</a>
			</div>
		</div>
	<?php endforeach;?>
</div>