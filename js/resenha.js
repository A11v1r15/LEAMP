document.addEventListener(
	"DOMContentLoaded",
	function () {

		const textareas =
			document.querySelectorAll(".protegido");

		textareas.forEach(function (textarea) {
			let pasted = false;
			const startedAt = Date.now();

			/* cria hidden typing_time */
			const typingInput =
				document.createElement("input");
			typingInput.type = "hidden";
			typingInput.name = "typing_time";

			/* cria hidden used_paste */
			const pasteInput =
				document.createElement("input");
			pasteInput.type = "hidden";
			pasteInput.name = "used_paste";

			/* adiciona no formulário */
			const form =
				textarea.closest("form");
			if (!form) {return;}
			form.appendChild(typingInput);
			form.appendChild(pasteInput);

			/* detecta paste */
			textarea.addEventListener("paste",
				function () {
					pasted = true;
					showFlash("Tente escrever com suas próprias palavras");
				}
			);

			/* submit */
			form.addEventListener("submit",
				function () {
					typingInput.value =
						Math.floor((Date.now() - startedAt) / 1000);
					pasteInput.value = pasted? "1" : "0";
				}
			);
		});

		document.querySelectorAll(".stars input").forEach(function (input) {
			input.addEventListener("change",updateStars);
		});

		updateStars();
	}
);

function updateStars() {
	const checked = document.querySelector('.stars input[name="rating"]:checked');
	const rating = Number(checked?.value ?? 0);

	document.querySelectorAll(
		'.stars label'
	).forEach(function (label) {
		const star = Number(label.htmlFor.replace("star",""));
		if (star > 0 && star <= rating) {
			label.textContent = "★";
		} else if (star > 0) {
			label.textContent = "☆";
		}
	});
}