<?php

include_once "includes/util.php";

function buildStatus(string $type) {
	return
		"<span class='status ".
		colorClass($type).
		"'>".
		htmlspecialchars($type).
		"</span>";
}

function buidEventTitle(array $event): string {
	return
		htmlspecialchars($event["title"]).
		(!empty($event["edition"])?
		" - ".toRoman((int)$event["edition"]):"");
}

function buildAButton(string $color, string $href, string $label) {
	return
		"<a class='button ".$color.
			"' href='".htmlspecialchars($href)."'>".
				htmlspecialchars($label).
		"</a>";
}

function buildFormButton(string $color, string $action, string $label) {
	return
		"<button".
			" class='button ".$color."'".
			" type='submit'".
			" name='action'".
			" value='".$action."'>".
			htmlspecialchars($label).
		"</button>";
}

function buildAvatar(array $user, $ranking = null, $dynamic = false) {
	ob_start();
	?>
		<div class="avatar-wrapper">
			<img
				class="avatar"
				<?=$dynamic?"id='preview-avatar'":""?>
				src="<?=htmlspecialchars($user["avatar"]??"")?>">
			<?php if (
				!empty($ranking) &&
				!empty($user) &&
				$user["uuid"] === $ranking[0]["uuid"]
			): ?>
				<img
					class="crown"
					src="/img/Crown.png"
					alt="Crown">
			<?php endif; ?>
		</div>
	<?php
	return ob_get_clean();
}

function buildSmallCard(array $card) {
	$dynamic = isset($card["dynamic"]);
	$user = $card["user"]??[];
	ob_start();
	?>
		<div
			id="<?=htmlspecialchars($card["id"] ?? "")?>"
			class="small-card
				<?=htmlspecialchars($card["color"] ?? "")?>
				<?=$dynamic? "hidden" : ""?>">
			<?php if (isset($card["ranking-position"])): ?>
				<div class="ranking-position">
					#<?=$card["ranking-position"]?>
				</div>
			<?php endif; ?>
			<?php if (isset($card["user"]) || $dynamic): ?>
				<?=buildAvatar(
					$user,
					$card["ranking"]??null,
					$dynamic)?>
			<?php endif; ?>
			<div class="info">
				<?php if(isset($card["title_url"]) && isset($card["title"])):?>
					<a class="title"
						href="<?=htmlspecialchars($card["title_url"])?>">
							<?=htmlspecialchars($card["title"])?>
					</a>
				<?php elseif(isset($card["title"]) || $dynamic):?>
					<div class="title" <?=$dynamic?"id='preview-name'":""?>>
						<?=htmlspecialchars($card["title"]??"")?>
					</div>
				<?php endif;?>
				<?php if(isset($card["subtitle"])):?>
					<div class="subtitle">
						<?=htmlspecialchars($card["subtitle"])?>
					</div>
				<?php endif;?>
				<?php if(isset($card["status"])):?>
					<?=buildStatus($card["status"])?>
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

function buildBigCard(array $card) {
	$user = $card["user"]??[];
	ob_start();
	?>
		<div class="big-card">
			<?php if(isset($card["user"])):?>
				<?=buildAvatar(
					$user,
					$card["ranking"]??null
				)?>
			<?php endif;?>
			<div class="info">
				<?php if(isset($card["title"])):?>
					<div class="title">
						<?=htmlspecialchars($card["title"])?>
					</div>
				<?php endif;?>
				<?php if(isset($card["subtitle"])):?>
					<div class="subtitle">
						<?=htmlspecialchars($card["subtitle"])?>
					</div>
				<?php endif;?>
				<?php if(isset($card["rating"]) && $card["rating"] != "0"):?>
					<div class="rating">
						<?php
							echo str_repeat("★",
								(int)$card["rating"]);
							echo str_repeat("☆",
								5 - (int)$card["rating"]);
						?>
					</div>
				<?php endif;?>
				<?php if(isset($card["status"])):?>
					<?=buildStatus($card["status"])?>
				<?php endif;?>
				<?php if(isset($card["labelsText"]) && is_array($card["labelsText"])):?>
					<?php foreach($card["labelsText"] as $labelText):?>
						<p>
							<b><?=htmlspecialchars($labelText[0])?></b>
							<?=nl2br(htmlspecialchars($labelText[1]))?>
						</p>
					<?php endforeach;?>
				<?php endif;?>
				<?php if (isset($card["big-text"]) && $card["big-text"] !== ""): ?>
					<p><?= nl2br(
							htmlspecialchars($card["big-text"]))
						?></p>
				<?php endif; ?>
				<?php if (isset($card["quote"]) && $card["quote"] !== ""): ?>
					<blockquote>
						<?= nl2br(
							htmlspecialchars($card["quote"]))
						?>
					</blockquote>
				<?php endif; ?>
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