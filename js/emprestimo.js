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

const role =
	document.getElementById(
		"preview-role"
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
		role.className =
			"badge " + option.dataset.role;
		role.textContent =
			option.dataset.role;
		name.textContent =
			"Emprestar para " +
			option.dataset.name;
		preview.classList.remove(
			"hidden"
		);
	}
);