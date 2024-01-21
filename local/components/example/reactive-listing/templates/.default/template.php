<section class="container">
	<form class="filter" method="POST" id="filter">
		<div class="filter__field">
			<label for="UF_TEXT">Таск</label>
			<select name="UF_TEXT">
				<option value="">Не выбрано</option>
				<?foreach ($arResult['TEXTS'] as $text):?>
					<option value="<?=$text?>"><?=$text?></option>
				<?endforeach?>
			</select>
		</div>
		<div class="filter__field">
			<label>Статус</label>
			<select name="UF_COMPLETE">
				<option value="">Не выбрано</option>
				<option value="0">Не выполнено</option>
				<option value="1">Выполнено</option>
			</select>
		</div>
		<div class="filter__actions">
			<button type="submit">Найти</button>
			<button type="reset">Сбросить</button>
		</div>
	</form>
	<ul class="listing" id="content">
		<?foreach ($arResult['TASKS'] as $task):?>
				<li class="listing__item">
					<label>
						<input
							data-id="<?=$task['ID']?>"
							type="checkbox"
							name="UF_COMPLETE"
							<?=$task['UF_COMPLETE'] ? 'checked' : ''?>
						>
						<?=$task['UF_TEXT']?>
					</label>
				</li>
		<?endforeach?>
	</ul>
	<nav id="navigation" class="navigation">
		<ul>
			<?if ($arResult['NAV']->getCurrentPage() != 1):?>
				<li><button type="button" data-type="prev"><</button></li>
			<?endif?>
			<li><?=$arResult['NAV']->getCurrentPage()?></li>
			<?if ($arResult['NAV']->getCurrentPage() != $arResult['NAV']->getPageCount()):?>
				<li><button type="button" data-type="next">></button></li>
			<?endif?>
		</ul>
	</nav>
</section>
<?if (is_a($this, 'CBitrixComponentTemplate')):?>
	<script>
		const signedParameters = <?=Bitrix\Main\Web\Json::encode($this->getComponent()->getSignedParameters())?>;
	</script>
<?endif?>