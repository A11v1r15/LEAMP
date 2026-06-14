
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

	$reviewedLoans = array_column(
		$reviews,
		"loan_id"
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
	<?=buildAvatar(
		$_SESSION["user"],
		$ranking)?>
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
<div class="small-card-container">
	<?php foreach ($loans as $loan):?>
		<?php
			$book = $loan["book"];
			$review_url = "/resenha?"."id=".$loan["id"];
		?>
		<?=buildSmallCard([
			"color" => (!$loan["is_active"])?"gray":
							(isOverdue($loan["deadline"], $loan["is_active"])?
								"red":"green"),
			"title" => $book["title"],
			"title_url" => "/livro?id=".$book["id"],
			"deadline" => ($loan["is_active"])?
				"Até ".date("d/m/Y", strtotime($loan["deadline"])):
				"Entregue ".date("d/m/Y", strtotime($loan["end_date"])),
			"subtitle" => (!$loan["is_active"])?"Finalizado":
							(isOverdue($loan["deadline"], $loan["is_active"])?
								"Atrasado":"Em andamento"),
			"extra" => buildAButton("blue",
							$review_url, "🖉 ".(in_array($loan["id"], $reviewedLoans)?
									"Editar resenha" : "Escrever resenha"))
		])?>
	<?php endforeach;?>
</div>