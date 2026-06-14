<?php

	require_once "includes/supabase.php";
	include_once "includes/util.php";

	requireReviewer();

	$page_title = "Resenhas - LÉAMP";

	/* resenhas */

	$reviews = supabaseGet(
	"reviews?".
	"select=".
		"loan_id,".
		"rating,".
		"status,".
		"loan:loan_id(".
			"id,".
			"book:book_id(".
				"id,".
				"title".
			"),".
			"reader:reader(".
				"name,".
				"avatar".
			")".
		")",

	$_SESSION["user"]["token"]
);

	if (!is_array($reviews)) {
		$reviews = [];
	}
?>

<h2>Resenhas</h2>

<table id="tabelaReviews">
	<thead>
		<tr>
			<th>Livro</th>
			<th>Leitor</th>
			<th>Nota</th>
			<th>Status</th>
			<th>Ações</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($reviews as $review):?>
			<tr>
				<td>
					<a
						href="/livro?id=<?=$review["loan"]["book"]["id"]?>"
					>
						<?=htmlspecialchars(
							$review["loan"]["book"]["title"]
						)?>
					</a>
				</td>

				<td data-order="<?=htmlspecialchars(
					$review["loan"]["reader"]["name"]
					?? "Desconhecido"
				)?>">
					<div class="user-inline">
						<img
							src="<?=htmlspecialchars($review["loan"]["reader"]["avatar"])?>"
							class="mini-avatar"
							alt=""
						>
						<span>
							<?=htmlspecialchars($review["loan"]["reader"]["name"])?>
						</span>
					</div>
				</td>

				<td>
					<div class="rating">
						<?php
							echo str_repeat("★",
								(int)$review["rating"]);
							echo str_repeat("☆",
								5 - (int)$review["rating"]);
						?>
					</div>
				</td>

				<td>
					<?=buildStatus($review["status"])?>
				</td>

				<td>
					<?=buildAButton("blue",
						"/resenha?id=".$review["loan_id"], "👓 Revisar")?>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable(
		"#tabelaReviews",
		{
			order: [[3	, "desc"]],
			language: {
				url: "https://cdn.datatables.net/plug-ins/2.3.1/i18n/pt-BR.json"
			}
		}
	);
</script>