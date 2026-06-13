<?php

include_once "includes/util.php";

function buildStatus($type) {
	return
		"<span class='status ".
		colorClass($type).
		"'>".
		htmlspecialchars($type).
		"</span>";
}

function buidEventTitle($event) {
	return
		htmlspecialchars($event["title"]).
		(!empty($event["edition"])?
		" - ".toRoman((int)$event["edition"]):"");
}

function buildAButton($color, $href, $label) {
	return
		"<a class='button ".$color.
			"' href='".htmlspecialchars($href)."'>".
				htmlspecialchars($label).
		"</a>";
}

function buildFormButton($color, $action, $label) {
	return
		"<button".
			" class='button ".$color."'".
			" type='submit'".
			" name='action'".
			" value='".$action."'>".
			htmlspecialchars($label).
		"</button>";
}

function buildSmallCard($card) {
	ob_start();
	?>
	<div
		id="<?=htmlspecialchars($card["id"] ?? "")?>"
		class="small-card
			<?=htmlspecialchars($card["color"] ?? "")?>
			<?=isset($card["dynamic"]) ? "hidden" : ""?>">
		<?php if (isset($card["ranking-position"])): ?>
			<div class="ranking-position">
				#<?=$card["ranking-position"]?>
			</div>
		<?php endif; ?>
		<?php if (isset($card["user"]) || isset($card["dynamic"])): ?>
			<div class="avatar-wrapper">
				<img
					class="avatar"
					<?=isset($card["dynamic"])?"id='preview-avatar'":""?>
					src="<?=htmlspecialchars($card["user"]["avatar"]??"")?>">
				<?php if (
					isset($card["ranking"]) &&
					isset($card["user"]) &&
					$card["user"]["uuid"] === $card["ranking"][0]["uuid"]
				): ?>
					<img
						class="crown"
						src="/img/Crown.png"
						alt="Crown">
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="info">
			<?php if(isset($card["title_url"]) && isset($card["title"])):?>
				<a class="title" 
					href="<?=htmlspecialchars($card["title_url"])?>">
						<?=htmlspecialchars($card["title"])?>
				</a>
			<?php elseif(isset($card["title"]) || isset($card["dynamic"])):?>
				<div class="title" <?=isset($card["dynamic"])?"id='preview-name'":""?>>
					<?=htmlspecialchars($card["title"]??"")?>
				</div>
			<?php endif;?>
			<?php if(isset($card["subtitle"])):?>
				<div class="subtitle">
					<?=htmlspecialchars($card["subtitle"])?>
				</div>
			<?php endif;?>
			<?php if(isset($card["strong"])):?>
				<strong>
					<?=htmlspecialchars($card["strong"])?>
				</strong>
			<?php endif;?>
			<?php if(isset($card["text"])):?>
				<p>
					<?=htmlspecialchars($card["text"])?>
				</p>
			<?php endif;?>
			<?php if(isset($card["deadline"])):?>
				<div class="deadline">
					<?=htmlspecialchars($card["deadline"])?>
				</div>
			<?php endif;?>
		</div>
		<?php if(isset($card["extra"])):?>
			<div class="extra">
				<?=$card["extra"]?>
			</div>
		<?php endif;?>
	</div>
	<?php
	return ob_get_clean();
}