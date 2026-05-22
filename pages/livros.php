<?php
	require_once "includes/supabase.php";
	require_once "includes/cache.php";

	$titulo = "LÉAMP - Livros";

	$livros = cacheGet("livros");
	if ($livros === null) {
		$livros = supabaseGet("books?select=*");
		cacheSet("livros", $livros);
	}
	if (!is_array($livros)) {
		$livros = [];
	}
//	echo "<pre>";
//	print_r($livros);
//	echo "</pre>";
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

		<?php foreach ($livros as $livro): ?>

			<tr>

				<td>
					<a href="/livro?id=<?= $livro["id"] ?>">
						<?= htmlspecialchars($livro["title"]) ?>
					</a>
				</td>

				<td>
					<?= htmlspecialchars($livro["author"]) ?>
				</td>

				<td>
					<?= htmlspecialchars($livro["status"]) ?>
				</td>

			</tr>

		<?php endforeach; ?>

	</tbody>

</table>

<script>
	new DataTable("#tabelaLivros", {language: {url: "/assets/datatables.json"}});
</script>