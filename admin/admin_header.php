<?php

declare(strict_types=1);

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use Xmf\Module\Admin;
use XoopsModules\Yogurt\Helper;
use XoopsModules\Yogurt\Utility;

include dirname(__DIR__) . '/preloads/autoloader.php';

require dirname(__DIR__, 3) . '/include/cp_header.php';
//require $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

require dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));

$helper = Helper::getInstance();
$utility = Utility::getInstance();

/** @var Admin $adminObject */
$adminObject = Admin::getInstance();

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

$db = XoopsDatabaseFactory::getDatabaseConnection();

$pathIcon16    = Admin::iconUrl('', 16);
$pathIcon32    = Admin::iconUrl('', 32);
$pathModIcon32 = $helper->getConfig('modicons32');

/** @var XoopsPersistableObjectHandler $imageHandler */
$imageHandler = $helper->getHandler('Image');
/** @var XoopsPersistableObjectHandler $friendshipHandler */
$friendshipHandler = $helper->getHandler('Friendship');
/** @var XoopsPersistableObjectHandler $visitorsHandler */
$visitorsHandler = $helper->getHandler('Visitors');
/** @var XoopsPersistableObjectHandler $videoHandler */
$videoHandler = $helper->getHandler('Video');
/** @var XoopsPersistableObjectHandler $friendrequestHandler */
$friendrequestHandler = $helper->getHandler('Friendrequest');
/** @var XoopsPersistableObjectHandler $groupsHandler */
$groupsHandler = $helper->getHandler('Groups');
/** @var XoopsPersistableObjectHandler $relgroupuserHandler */
$relgroupuserHandler = $helper->getHandler('Relgroupuser');
/** @var XoopsPersistableObjectHandler $notesHandler */
$notesHandler = $helper->getHandler('Notes');
/** @var XoopsPersistableObjectHandler $configsHandler */
$configsHandler = $helper->getHandler('Configs');
/** @var XoopsPersistableObjectHandler $suspensionsHandler */
$suspensionsHandler = $helper->getHandler('Suspensions');
/** @var XoopsPersistableObjectHandler $audioHandler */
$audioHandler = $helper->getHandler('Audio');
/** @var XoopsPersistableObjectHandler $privacyHandler */
$privacyHandler = $helper->getHandler('Privacy');

$myts = MyTextSanitizer::getInstance();
if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new XoopsTpl();
}

$pathIcon16    = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon32 = $helper->getConfig('modicons32');

// Local icons path
$xoopsTpl->assign('pathModIcon16', $pathIcon16);
$xoopsTpl->assign('pathModIcon32', $pathIcon32);
