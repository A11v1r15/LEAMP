document.addEventListener(
	"DOMContentLoaded",
	function () {

		const textareas =
			document.querySelectorAll(
				"textarea"
			);

		textareas.forEach(function (
			textarea
		) {

			let pasted = false;

			const startedAt =
				Date.now();

			/* cria hidden typing_time */

			const typingInput =
				document.createElement(
					"input"
				);

			typingInput.type =
				"hidden";

			typingInput.name =
				"typing_time";

			/* cria hidden used_paste */

			const pasteInput =
				document.createElement(
					"input"
				);

			pasteInput.type =
				"hidden";

			pasteInput.name =
				"used_paste";

			/* adiciona no formulário */

			const form =
				textarea.closest(
					"form"
				);

			if (!form) {
				return;
			}

			form.appendChild(
				typingInput
			);

			form.appendChild(
				pasteInput
			);

			/* detecta paste */

			textarea.addEventListener(
				"paste",

				function () {

					pasted = true;

					alert(
						"Tente escrever com suas próprias palavras"
					);

				}
			);

			/* submit */

			form.addEventListener(
				"submit",

				function () {

					typingInput.value =
						Math.floor(
							(Date.now() - startedAt)
							/ 1000
						);

					pasteInput.value =
						pasted
							? "1"
							: "0";

				}
			);

		});

	}
);