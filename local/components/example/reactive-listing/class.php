<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

Bitrix\Main\Loader::includeModule("highloadblock"); 

use Bitrix\Highloadblock\HighloadBlockTable,
	Bitrix\Main\Engine\Contract\Controllerable,
	Bitrix\Main\Engine\CurrentUser,
	Bitrix\Main\UI\PageNavigation,
	Bitrix\Main\Engine\ActionFilter;

class Tasks extends CBitrixComponent implements Controllerable {

	public const PAGE = 1;
	public const PAGE_SIZE = 10;

	protected function listKeysSignedParameters() : Array
	{
		return [ 
			'TABLE',
			'FILTER',
			'ALLOWED_PAGE_SIZE'
		];
	}

	public function configureActions() : Array
	{
		return [
			'changeList' => [
				'-prefilters' => [
					ActionFilter\Authentication::class,
				],
				'prefilters' => [
					new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
					new ActionFilter\Scope(ActionFilter\Scope::AJAX)
				]
			],
		];
	}

	public function executeComponent()
	{
		$this->arResult = Self::getResult();
		$this->includeComponentTemplate();
	}

	protected function getResult($filter = [], $page = Self::PAGE, $pageSize = Self::PAGE_SIZE) : Array
	{
		$nav = new PageNavigation('pagination');

		$nav->setPageSize($pageSize);
		$nav->setCurrentPage($page);

		$dbTasks = HighloadBlockTable::compileEntity($this->arParams['TABLE'])->getDataClass()::getList([
			'select' => ['*'],
			'order' => ['ID' => 'DESC'],
			'filter' => array_merge($filter, $this->arParams['FILTER']),
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
			'data_doubling' => false,
			'count_total' => true,
		]);
		$nav->setRecordCount($dbTasks->getCount());
		while ($task = $dbTasks->Fetch()) {
			$tasks[] = $task;
			$texts[$task['UF_TEXT']] = $task['UF_TEXT'];
		}
		return [
			'TASKS' => $tasks,
			'TEXTS' => $texts,
			'NAV' => $nav,
			'ALLOWED_PAGE_SIZES' => $this->arParams['ALLOWED_PAGE_SIZE']
		];
	}

	public function changeListAction() : String
	{
		if (!empty($_POST)) {
			unset($_POST['signedParameters']);
			$_POST = array_filter($_POST, fn($value) => trim($value) != '');
		}

		[$pageString, $page, $pageSizeString, $pageSize] = explode('-', $_REQUEST['nav']);
		$arResult = Self::getResult($_POST, (int)$page, (int)$pageSize);

		return Self::render($arResult);
	}

	public function changeStatusAction($id, $complete)
	{
		$task = HighloadBlockTable::compileEntity($this->arParams['TABLE'])->getDataClass()::getList([
			'select' => ['ID'],
			'filter' => array_merge(['ID' => $id], $this->arParams['FILTER']),
		])->Fetch();

		if ($task) $result = HighloadBlockTable::compileEntity($this->arParams['TABLE'])->getDataClass()::update($id, ['UF_COMPLETE' => filter_var($complete, FILTER_VALIDATE_BOOLEAN)]);
		
		return $result->isSuccess();
	}

	protected function render($arResult) : String
	{
		ob_start();
			include(__DIR__.'/templates/.default/template.php');
			$template = ob_get_contents();
		ob_end_clean();

		return $template;
	}
}

?>