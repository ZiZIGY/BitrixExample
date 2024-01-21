<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->ShowHead();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Engine\CurrentUser,
    Bitrix\Main\UI\Extension;

Extension::load("example.particles");


Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/main.css');?>
<body>
    <header class="header">
        <div id="panel" class="header__bitrix-panel"><?$APPLICATION->ShowPanel()?></div>
    </header>
    <main>
        <?$APPLICATION->IncludeComponent(
            "example:reactive-listing", ".default", [
                'TABLE' => 'Tasks',
                'FILTER' => [
                    'UF_USER' => CurrentUser::get()->getID()
                ],
                'ALLOWED_PAGE_SIZE' => [5, 10, 20, 50, 100],
                'DEFAULT_PAGE_SIZE' => 5,
                'DEFAULT_PAGE' => 1
            ]
        );?>
    </main>