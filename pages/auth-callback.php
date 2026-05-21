<h2>Entrando...</h2>

<script>
	async function finalizarLogin() {

		const {
			data: { session }
		} = await supabaseClient.auth.getSession();

		if (!session) {

			document.body.innerHTML =
				"Login inválido";

			return;
		}

		const email = session.user.email;

		/* envia pro PHP */

		const response = await fetch(
			"/auth-save",
			{
				method: "POST",
				headers: {
					"Content-Type":
						"application/json"
				},
				body: JSON.stringify({
					email: session.user.email,
					name: session.user.user_metadata?.full_name,
					avatar: session.user.user_metadata?.avatar_url,
					token: session.access_token
		})});

		window.location = "/user";
	}

	finalizarLogin();

</script>