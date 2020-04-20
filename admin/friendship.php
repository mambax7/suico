<?php

declare(strict_types=1);

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Module: Yogurt
 *
 * @category        Module
 * @package         yogurt
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GPL 2.0 or later
 * @link            https://xoops.org/
 * @since           1.0.0
 */

use Xmf\Request;
use Xmf\Module\Helper\Permission;

require __DIR__ . '/admin_header.php';
xoops_cp_header();
//It recovered the value of argument op in URL$
$op    = Request::getString('op', 'list');
$order = Request::getString('order', 'desc');
$sort  = Request::getString('sort', '');

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();
$uploadDir  = XOOPS_UPLOAD_PATH . '/yogurt/images/';
$uploadUrl  = XOOPS_UPLOAD_URL . '/yogurt/images/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(AM_YOGURT_FRIENDSHIP_LIST, 'friendship.php', 'list');
        $adminObject->displayButton('left');

        $friendshipObject = $friendshipHandler->create();
        $form             = $friendshipObject->getForm();
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('friendship.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (0 !== Request::getInt('friendship_id', 0)) {
            $friendshipObject = $friendshipHandler->get(Request::getInt('friendship_id', 0));
        } else {
            $friendshipObject = $friendshipHandler->create();
        }
        // Form save fields
        $friendshipObject->setVar('friend1_uid', Request::getVar('friend1_uid', ''));
        $friendshipObject->setVar('friend2_uid', Request::getVar('friend2_uid', ''));
        $friendshipObject->setVar('level', Request::getVar('level', ''));
        $friendshipObject->setVar('hot', Request::getVar('hot', ''));
        $friendshipObject->setVar('trust', Request::getVar('trust', ''));
        $friendshipObject->setVar('cool', Request::getVar('cool', ''));
        $friendshipObject->setVar('fan', Request::getVar('fan', ''));
        if ($friendshipHandler->insert($friendshipObject)) {
            redirect_header('friendship.php?op=list', 2, AM_YOGURT_FORMOK);
        }

        echo $friendshipObject->getHtmlErrors();
        $form = $friendshipObject->getForm();
        $form->display();
        break;

    case 'edit':
        $adminObject->addItemButton(AM_YOGURT_ADD_FRIENDSHIP, 'friendship.php?op=new', 'add');
        $adminObject->addItemButton(AM_YOGURT_FRIENDSHIP_LIST, 'friendship.php', 'list');
        $adminObject->displayButton('left');
        $friendshipObject = $friendshipHandler->get(Request::getString('friendship_id', ''));
        $form             = $friendshipObject->getForm();
        $form->display();
        break;

    case 'delete':
        $friendshipObject = $friendshipHandler->get(Request::getString('friendship_id', ''));
        if (1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('friendship.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($friendshipHandler->delete($friendshipObject)) {
                redirect_header('friendship.php', 3, AM_YOGURT_FORMDELOK);
            } else {
                echo $friendshipObject->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                [
                    'ok'            => 1,
                    'friendship_id' => Request::getString('friendship_id', ''),
                    'op'            => 'delete',
                ],
                Request::getUrl('REQUEST_URI', '', 'SERVER'),
                sprintf(
                    AM_YOGURT_FORMSUREDEL,
                    $friendshipObject->getVar('friendship_id')
                )
            );
        }
        break;

    case 'clone':

        $id_field = Request::getString('friendship_id', '');

        if ($utility::cloneRecord('yogurt_friendship', 'friendship_id', $id_field)) {
            redirect_header('friendship.php', 3, AM_YOGURT_CLONED_OK);
        } else {
            redirect_header('friendship.php', 3, AM_YOGURT_CLONED_FAILED);
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(AM_YOGURT_ADD_FRIENDSHIP, 'friendship.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                     = Request::getInt('start', 0);
        $friendshipPaginationLimit = $helper->getConfig('userpager');

        $criteria = new CriteriaCompo();
        $criteria->setSort('friendship_id ASC, friendship_id');
        $criteria->setOrder('ASC');
        $criteria->setLimit($friendshipPaginationLimit);
        $criteria->setStart($start);
        $friendshipTempRows  = $friendshipHandler->getCount();
        $friendshipTempArray = $friendshipHandler->getAll($criteria);
        /*
        //
        //
                            <th class='center width5'>".AM_YOGURT_FORM_ACTION."</th>
        //                    </tr>";
        //            $class = "odd";
        */

        // Display Page Navigation
        if ($friendshipTempRows > $friendshipPaginationLimit) {
            xoops_load('XoopsPageNav');

            $pagenav = new XoopsPageNav(
                $friendshipTempRows,
                $friendshipPaginationLimit,
                $start,
                'start',
                'op=list' . '&sort=' . $sort . '&order=' . $order . ''
            );
            $GLOBALS['xoopsTpl']->assign('pagenav', null === $pagenav ? $pagenav->renderNav() : '');
        }

        $GLOBALS['xoopsTpl']->assign('friendshipRows', $friendshipTempRows);
        $friendshipArray = [];

        //    $fields = explode('|', friendship_id:int:11::NOT NULL::primary:friendship_id|friend1_uid:int:11::NOT NULL:::friend1_uid|friend2_uid:int:11::NOT NULL:::friend2_uid|level:int:11::NOT NULL:::level|hot:tinyint:4::NOT NULL:::hot|trust:tinyint:4::NOT NULL:::trust|cool:tinyint:4::NOT NULL:::cool|fan:tinyint:4::NOT NULL:::fan);
        //    $fieldsCount    = count($fields);

        $criteria = new CriteriaCompo();

        //$criteria->setOrder('DESC');
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($friendshipPaginationLimit);
        $criteria->setStart($start);

        $friendshipCount     = $friendshipHandler->getCount($criteria);
        $friendshipTempArray = $friendshipHandler->getAll($criteria);

        //    for ($i = 0; $i < $fieldsCount; ++$i) {
        if ($friendshipCount > 0) {
            foreach (array_keys($friendshipTempArray) as $i) {
                //        $field = explode(':', $fields[$i]);

                $GLOBALS['xoopsTpl']->assign(
                    'selectorfriendship_id',
                    AM_YOGURT_FRIENDSHIP_FRIENDSHIP_ID
                );
                $friendshipArray['friendship_id'] = $friendshipTempArray[$i]->getVar('friendship_id');

                $GLOBALS['xoopsTpl']->assign('selectorfriend1_uid', AM_YOGURT_FRIENDSHIP_FRIEND1_UID);
                $friendshipArray['friend1_uid'] = strip_tags(
                    XoopsUser::getUnameFromId($friendshipTempArray[$i]->getVar('friend1_uid'))
                );

                $GLOBALS['xoopsTpl']->assign('selectorfriend2_uid', AM_YOGURT_FRIENDSHIP_FRIEND2_UID);
                $friendshipArray['friend2_uid'] = strip_tags(
                    XoopsUser::getUnameFromId($friendshipTempArray[$i]->getVar('friend2_uid'))
                );

                $GLOBALS['xoopsTpl']->assign('selectorlevel', AM_YOGURT_FRIENDSHIP_LEVEL);
                $friendshipArray['level'] = $friendshipTempArray[$i]->getVar('level');

                $GLOBALS['xoopsTpl']->assign('selectorhot', AM_YOGURT_FRIENDSHIP_HOT);
                $friendshipArray['hot'] = $friendshipTempArray[$i]->getVar('hot');

                $GLOBALS['xoopsTpl']->assign('selectortrust', AM_YOGURT_FRIENDSHIP_TRUST);
                $friendshipArray['trust'] = $friendshipTempArray[$i]->getVar('trust');

                $GLOBALS['xoopsTpl']->assign('selectorcool', AM_YOGURT_FRIENDSHIP_COOL);
                $friendshipArray['cool'] = $friendshipTempArray[$i]->getVar('cool');

                $GLOBALS['xoopsTpl']->assign('selectorfan', AM_YOGURT_FRIENDSHIP_FAN);
                $friendshipArray['fan']         = $friendshipTempArray[$i]->getVar('fan');
                $friendshipArray['edit_delete'] = "<a href='friendship.php?op=edit&friendship_id=" . $i . "'><img src=" . $pathIcon16 . "/edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='friendship.php?op=delete&friendship_id=" . $i . "'><img src=" . $pathIcon16 . "/delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='friendship.php?op=clone&friendship_id=" . $i . "'><img src=" . $pathIcon16 . "/editcopy.png alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->append_by_ref('friendshipArrays', $friendshipArray);
                unset($friendshipArray);
            }
            unset($friendshipTempArray);
            // Display Navigation
            if ($friendshipCount > $friendshipPaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new XoopsPageNav(
                    $friendshipCount,
                    $friendshipPaginationLimit,
                    $start,
                    'start',
                    'op=list' . '&sort=' . $sort . '&order=' . $order . ''
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            //                     echo "<td class='center width5'>

            //                    <a href='friendship.php?op=edit&friendship_id=".$i."'><img src=".$pathIcon16."/edit.png alt='"._EDIT."' title='"._EDIT."'></a>
            //                    <a href='friendship.php?op=delete&friendship_id=".$i."'><img src=".$pathIcon16."/delete.png alt='"._DELETE."' title='"._DELETE."'></a>
            //                    </td>";

            //                echo "</tr>";

            //            }

            //            echo "</table><br><br>";

            //        } else {

            //            echo "<table width='100%' cellspacing='1' class='outer'>

            //                    <tr>

            //                     <th class='center width5'>".AM_YOGURT_FORM_ACTION."XXX</th>
            //                    </tr><tr><td class='errorMsg' colspan='9'>There are noXXX friendship</td></tr>";
            //            echo "</table><br><br>";

            //-------------------------------------------

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar(
                    'dirname'
                ) . '/templates/admin/yogurt_admin_friendship.tpl'
            );
        }

        break;
}
require __DIR__ . '/admin_footer.php';
