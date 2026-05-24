const select =
	document.getElementById(
		"reader-select"
	);

const preview =
	document.getElementById(
		"reader-preview"
	);

const avatar =
	document.getElementById(
		"preview-avatar"
	);

const name =
	document.getElementById(
		"preview-name"
	);

select.addEventListener(
	"change",

	() => {

		const option =
			select.selectedOptions[0];

		if (!option.value) {

			preview.classList.add(
				"hidden"
			);

			return;
		}

		avatar.src =
			option.dataset.avatar;

		name.textContent =
			"Emprestar para " +
			option.dataset.name;

		preview.classList.remove(
			"hidden"
		);
	}
);