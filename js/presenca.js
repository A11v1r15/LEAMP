const select =
	document.getElementById(
		"attendee-select"
	);

const preview =
	document.getElementById(
		"attendee-preview"
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
			option.dataset.name;
		preview.classList.remove(
			"hidden"
		);
	}
);