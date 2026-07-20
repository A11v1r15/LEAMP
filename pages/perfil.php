
<?php
	include_once "includes/cache.php";
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
		"loan_id,".
		"status",

	$_SESSION["user"]["token"]
	);

	$reviewedLoans = array_column(
		$reviews,
		"loan_id"
	);

	$reviewMap = [];

	foreach ($reviews as $review) {
		$reviewMap[$review["loan_id"]] = $review;
	}
	
	$openLoans = array_filter($loans, function($loan) use ($reviewMap) {
		return $loan["is_active"] ||
			!isset($reviewMap[$loan["id"]]) ||
			$reviewMap[$loan["id"]]["status"] !== "Aprovado";
	});

	$closedLoans = array_filter($loans, function($loan) use ($reviewMap) {
		return
			!$loan["is_active"] &&
			isset($reviewMap[$loan["id"]]) &&
			$reviewMap[$loan["id"]]["status"] === "Aprovado";
	});

	$ranking = getCacheOrFetch(
		"primeiro_lugar",
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
	</div>
</div>

<?php if (!empty($openLoans)): ?>
	<h2>Empréstimos:</h2>
	<div class="small-card-container">
		<?php foreach ($openLoans as $loan):?>
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
				"extra" => buildAButton((in_array($loan["id"], $reviewedLoans) && $reviewMap[$loan["id"]]["status"] === "Devolvido")? "orange" : "blue",
								$review_url, "🖉 ".(in_array($loan["id"], $reviewedLoans)?
										"Editar resenha" : "Escrever resenha"))
			])?>
		<?php endforeach;?>
	</div>
<?php endif; ?>

<?php if (!empty($closedLoans)): ?>
	<h2>Empréstimos concluídos:</h2>
	<table id="oldLoanTable">
			<thead>
				<tr>
					<th>Livro</th>
					<th>Entregue</th>
				<th>Ação</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($closedLoans as $loan): ?>
				<?php
					$book = $loan["book"];
					$review_url = "/resenha?"."id=".$loan["id"];
				?>
				<tr>
					<td>
						<a href="/livro?id=<?=$book["id"]?>"
						><?=htmlspecialchars($book["title"])?>
						</a>
					</td>

					<td>
						<?=date("d/m/Y", strtotime($loan["end_date"]))?>
					</td>

					<td>
						<?=buildAButton("blue",
								$review_url, "🖉 Editar resenha")?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<script> new DataTable("#oldLoanTable"); </script>
<?php endif; ?>