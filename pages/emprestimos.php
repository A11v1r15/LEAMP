<?php

require_once "includes/supabase.php";
require_once "includes/auth.php";

requireAdmin();

$titulo = "Empréstimos - LÉAMP";

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
<link rel="stylesheet" href="/css/emprestimos.css">

<h2>Empréstimos</h2>

<table id="tabelaLoans">
	<thead>
		<tr>
			<th>Livro</th>
			<th>Leitor</th>
			<th>Emprestado em</th>
			<th>Concedente</th>
			<th>Data limite</th>
			<th>Recebedor</th>
			<th>Devolvido em</th>
			<th>Status</th>
			<th>Ações</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($loans as $loan): ?>
			<tr>
				<td>
					<a
						href="/livro?id=<?= $loan["book"]["id"] ?>"
					>
						<?= htmlspecialchars(
							$loan["book"]["title"]
						) ?>
					</a>
				</td>

				<td data-order="<?= htmlspecialchars(
					$loan["reader"]["name"]
					?? "Desconhecido"
				) ?>">
					<div class="user-inline">
						<img
							src="<?= htmlspecialchars($loan["reader"]["avatar"]) ?>"
							class="mini-avatar"
							alt=""
						>
						<span>
							<?= htmlspecialchars($loan["reader"]["name"]) ?>
						</span>
					</div>
				</td>

				<td>
					<?= date(
						"d/m/Y H:i",
						strtotime(
							$loan["start_date"]
						)
					) ?>
				</td>

				<td data-order="<?= htmlspecialchars(
					$loan["grantor"]["name"]
					?? "Desconhecido"
				) ?>">
					<div class="user-inline">
						<img
							src="<?= htmlspecialchars($loan["grantor"]["avatar"]) ?>"
							class="mini-avatar"
							alt=""
						>
						<span>
							<?= htmlspecialchars($loan["grantor"]["name"]) ?>
						</span>
					</div>
				</td>

				<td>
					<?= date(
						"d/m/Y",
						strtotime(
							$loan["deadline"]
						)
					) ?>
				</td>
				

				<td data-order="<?= htmlspecialchars(
					$loan["receiver"]["name"]
					?? "Desconhecido"
				) ?>">
					<?php if (
						$loan["is_active"]
					): ?>
						—
					<?php else: ?>
						<div class="user-inline">
							<img
								src="<?= htmlspecialchars($loan["receiver"]["avatar"]) ?>"
								class="mini-avatar"
								alt=""
							>
							<span>
								<?= htmlspecialchars($loan["receiver"]["name"]) ?>
							</span>
						</div>
					<?php endif; ?>
				</td>

				<td>
					<?php if (
						$loan["is_active"]
					): ?>
						—
					<?php else: ?>
						<?= date(
							"d/m/Y H:i",
							strtotime(
								$loan["end_date"]
							)
						) ?>
					<?php endif; ?>
				</td>

				<td>
					<?php if (
						$loan["is_active"]
					): ?>
						<span class="loan-active
						">Ativo
						</span>
					<?php else: ?>
						<span class="loan-finished
						">Finalizado
						</span>
					<?php endif; ?>
				</td>

				<td>
					<?php if (
						$loan["is_active"]
					): ?>
						<a
							href="/devolucao?id=<?= $loan["id"] ?>"
							class="loan-button"
						>Gerenciar
						</a>
					<?php else: ?>
						—
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
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