<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";
	include_once "includes/util.php";

	$page_title = "Livros - LÉAMP";

	$books = getCacheOrFetch(
		"livros",
		"books?".
		"select=*"
	);
?>

<h2>Livros</h2>

<table id="tabelaLivros">

	<thead>
		<tr>
			<th>Título</th>
			<th>Autores</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($books as $book):?>
			<tr>
				<td><a href="/livro?id=<?=$book["id"]?>">
					<?=htmlspecialchars($book["title"])?>
				</a></td>
				<td><?=htmlspecialchars($book["author"])?></td>
				<td>
					<span class="status <?=
						colorClass($book["status"])?>">
						<?=htmlspecialchars($book["status"])?>
					</span>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable("#tabelaLivros", {language: {url: "/assets/datatables.json"}});
</script>