<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";
	include_once "includes/util.php";

	$page_title = "Livros - LÉAMP";

	$books = getCacheOrFetch(
		"livros",
		"books_ranked?".
		"select=*"
	);
?>

<h2>Livros</h2>

<table id="tabelaLivros">

	<thead>
		<tr>
			<th>Título</th>
			<th>Autores</th>
			<th>Empréstimos</th>
			<th>Resenhas</th>
			<th>Nota</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($books as $book):?>
			<?php if ($book["status"] !== "Indisponível"):?>
				<tr>
					<td><a href="/livro?id=<?=$book["id"]?>">
						<?=htmlspecialchars($book["title"])?>
					</a></td>
					<td>
						<?=htmlspecialchars($book["author"])?>
					</td>
					<td>
						<?=$book["loans_count"]?? 0?>
					</td>
					<td>
						<?=$book["reviews_count"]?? 0?>
					</td>
					<td data-order="<?=$book["rating_avg"]??""?>">
						<?=isset($book["rating_avg"])?buildRating($book["rating_avg"]):"—"?>
					</td>
					<td>
						<?=buildStatus($book["status"])?>
					</td>
				</tr>
			<?php endif;?>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable("#tabelaLivros", {language: {url: "/assets/datatables.json"}});
</script>