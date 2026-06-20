<?php

	require_once "includes/supabase.php";
	include_once "includes/util.php";

	requireAdmin();

	$page_title = "Empréstimos - LÉAMP";

	/* empréstimos */

	$loans = supabaseGet(
		"loans?".
		"select=".
			"id,".
			"start_date,".
			"deadline,".
			"end_date,".
			"is_active,".
			"book:book_id(".
				"id,".
				"title".
			"),".
			"reader:reader(".
				"name,".
				"avatar".
			"),".
			"grantor:grantor(".
				"name,".
				"avatar".
			"),".
			"receiver:receiver(".
				"name,".
				"avatar".
			")",

		$_SESSION["user"]["token"]
	);

	if (!is_array($loans)) {
		$loans = [];
	}
?>

<h2>Empréstimos</h2>

<table id="tabelaLoans">
	<thead>
		<tr>
			<th>Livro</th>
			<th>Leitor</th>
			<th>Emprestado em</th>
			<th>Concedente</th>
			<th>Data limite</th>
			<th>Recebimento</th>
			<th>Status</th>
			<th>Ações</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($loans as $loan):?>
			<tr>
				<td>
					<a
						href="/livro?id=<?=$loan["book"]["id"]?>"
					>
						<?=htmlspecialchars(
							$loan["book"]["title"]
						)?>
					</a>
				</td>

				<td data-order="<?=htmlspecialchars(
					$loan["reader"]["name"]
					?? "Desconhecido"
				)?>">
					<?=buildMiniAvatar($loan["reader"])?>
				</td>

				<td>
					<?=date("d/m/Y H:i",strtotime($loan["start_date"]))?>
				</td>

				<td data-order="<?=htmlspecialchars(
					$loan["grantor"]["name"]
					?? "Desconhecido"
				)?>">
					<?=buildMiniAvatar($loan["grantor"])?>
				</td>

				<td>
					<?=date("d/m/Y",strtotime($loan["deadline"]))?>
				</td>


				<td data-order="<?=htmlspecialchars(
					$loan["receiver"]["name"]
					?? "Desconhecido"
				)?>">
					<?php if ($loan["is_active"]):?>
						—
					<?php else:?>
						<?=buildMiniAvatar($loan["receiver"])?>
						<small>
							<?=date("d/m/Y H:i", strtotime($loan["end_date"]))?>
						</small>
					<?php endif;?>
				</td>

				<td>
					<?php if ($loan["is_active"]):?>
						<?=buildStatus("Ativo")?>
					<?php else:?>
						<?=buildStatus("Finalizado")?>
					<?php endif;?>
				</td>

				<td>
					<?php if ($loan["is_active"]):?>
						<?=buildAButton("blue",
							"/devolucao?id=".$loan["id"], "↩ Devolver")?>
					<?php else:?>
						—
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable(
		"#tabelaLoans",
		{
			order: [[2, "desc"]],
			language: {
				url: "https://cdn.datatables.net/plug-ins/2.3.1/i18n/pt-BR.json"
			}
		}
	);
</script>