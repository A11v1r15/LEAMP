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
					alert("Tente escrever com suas próprias palavras");
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
			input.addEventListener("change", function () {
				const rating = this.value;
				document.querySelectorAll(".stars label").forEach(function (label) {
					if (label.htmlFor.startsWith("star") &&
							label.htmlFor !== "star0" &&
							!isNaN(label.htmlFor.slice(4)) &&
							Number(label.htmlFor.slice(4)) <= rating) {
						label.textContent = "★";
					} else if (label.htmlFor.startsWith("star") && label.htmlFor !== "star0") {
						label.textContent = "☆";
					}
				});
			});
		});
	}
);