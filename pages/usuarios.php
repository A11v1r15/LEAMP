<?php

	require_once "includes/supabase.php";
	include_once "includes/util.php";

	requireAdmin();

	$page_title = "Usuários - LÉAMP";

	$users = supabaseGet(
		"users?".
		"select=uuid,".
			"name,".
			"email,".
			"avatar,".
			"role",

		$_SESSION["user"]["token"]
	);

	if (!is_array($users)) {
		$users = [];
	}

	if ($_SERVER["REQUEST_METHOD"] === "POST" &&
			isset($_POST["user_uuid"]) &&
			isset($_POST["new_role"])) {
		$user_uuid = $_POST["user_uuid"];
		$new_role = $_POST["new_role"];

		$user = array_values(
			array_filter(
				$users,
				fn($u) => $u["uuid"] === $user_uuid
			)
		)[0] ?? null;

		if (!$user) {
			header("Content-Type: application/json; charset=utf-8");
			http_response_code(400);
			echo json_encode([
				"error" => "Usuário não encontrado"
			]);
			exit;
		}

		$user_name = $user["name"];

		if ($new_role === "Leitor") {
			$new_role = str_ends_with($user["email"], "ifce.edu.br")?
				"Leitor" : "Leitor Externo";
		}

		$result = supabasePatch(
			"users?".
				"uuid=eq.$user_uuid", [
					"role" => $new_role
				],
			$_SESSION["user"]["token"]
		);

		if (hasErrorCode($result)) {
			header("Content-Type: application/json; charset=utf-8");
			http_response_code(400);
			echo json_encode([
				"error" => "Erro ao atualizar função de {$user_name}: " . $result["message"]
			]);
			exit;
		}

		header("Content-Type: application/json; charset=utf-8");
		http_response_code(200);
		echo json_encode([
			"success" => true,
			"message" => "{$user_name} agora é {$new_role}"
		]);
		exit;
	}
?>

<h2>Usuários</h2>

<table id="tabelaUsers">
	<thead>
		<tr>
			<th>Usuário</th>
			<th>Email</th>
			<th>Função</th>
			<th>Ações</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($users as $user):?>
			<tr>
				<td>
					<?=buildMiniAvatar($user)?>
				</td>

				<td>
					<?=htmlspecialchars($user["email"])?>
				</td>

				<td>
					<select
						class="role-select"
						data-uuid="<?=htmlspecialchars($user["uuid"])?>"
						data-current="<?=htmlspecialchars($user["role"])?>"
					>
						<option value="Pendente" <?=$user["role"] === "Pendente" ? "selected" : ""?>>Pendente</option>
						<option value="Leitor" <?=str_starts_with($user["role"], "Leitor")? "selected" : ""?>>Leitor</option>
						<option value="Revisor" <?=$user["role"] === "Revisor" ? "selected" : ""?>>Revisor</option>
						<option value="Concedente" <?=$user["role"] === "Concedente" ? "selected" : ""?>>Concedente</option>
					</select>
				</td>

				<td>
					<button
						class="button yellow role-update-btn"
						data-uuid="<?=htmlspecialchars($user["uuid"])?>"
					>↑ Atualizar</button>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
	new DataTable(
		"#tabelaUsers",
		{
			order: [[1, "asc"]],
			language: {
				url: "https://cdn.datatables.net/plug-ins/2.3.1/i18n/pt-BR.json"
			}
		}
	);

	document.querySelectorAll(".role-update-btn").forEach((btn) => {
		btn.addEventListener("click", async () => {
			const uuid = btn.dataset.uuid;
			const select = document.querySelector(
				`.role-select[data-uuid="${uuid}"]`
			);
			const newRole = select.value;
			const currentRole = select.dataset.current;

			if (newRole === currentRole) {
				showFlash("Nenhuma alteração foi feita", "warning");
				return;
			}

			try {
				const response = await fetch(window.location.href, {
					method: "POST",
					headers: {
						"Content-Type": "application/x-www-form-urlencoded",
					},
					body: `user_uuid=${encodeURIComponent(uuid)}&new_role=${encodeURIComponent(newRole)}`,
				});

				if (!response.ok) {
					const error = await response.json();
					showFlash(
						error.error || "Erro ao atualizar função",
						"error"
					);
					select.value = currentRole;
					return;
				}

				select.dataset.current = newRole;
				showFlash(`Função atualizada para ${newRole}`, "success");
			} catch (err) {
				showFlash("Erro na requisição: " + err.message, "error");
				select.value = currentRole;
			}
		});
	});
</script>