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
			<th>Classificação</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($books as $book):?>
			<tr>
				<td><a href="/livro?id=<?=$book["id"]?>">
					<?=htmlspecialchars($book["title"])?>
				</a></td>
				<td>
					<?=htmlspecialchars($book["author"])?>
				</td>
				<td>
					<?=htmlspecialchars($book["loans_count"])?>
				</td>
				<td>
					<?=htmlspecialchars($book["reviews_count"])?>
				</td>
				<td data-order="<?=$book["rating_avg"]??""?>">
					<?=$book["rating_avg"]?buildRating($book["rating_avg"]):"—"?>
				</td>
				<td>
					<?=buildStatus($book["status"])?>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable("#tabelaLivros", {language: {url: "/assets/datatables.json"}});
</script>