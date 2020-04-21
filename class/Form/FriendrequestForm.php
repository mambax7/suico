<?php declare(strict_types=1);

namespace XoopsModules\Yogurt\Form;

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

use Xmf\Module\Helper\Permission;
use XoopsFormButton;
use XoopsFormHidden;
use XoopsFormLabel;
use XoopsFormSelectUser;
use XoopsModules\Yogurt;
use XoopsThemeForm;

require_once \dirname(__DIR__, 2) . '/include/common.php';

$moduleDirName = \basename(\dirname(__DIR__, 2));
//$helper = Yogurt\Helper::getInstance();
$permHelper = new Permission();

\xoops_load('XoopsFormLoader');

/**
 * Class FriendrequestForm
 */
class FriendrequestForm extends XoopsThemeForm
{
    public $targetObject;

    public $helper;

    /**
     * Constructor
     *
     * @param $target
     */
    public function __construct($target)
    {
        $this->helper       = $target->helper;
        $this->targetObject = $target;

        $title = $this->targetObject->isNew() ? \sprintf(AM_YOGURT_FRIENDREQUEST_ADD) : \sprintf(
            AM_YOGURT_FRIENDREQUEST_EDIT
        );
        parent::__construct($title, 'form', \xoops_getenv('SCRIPT_NAME'), 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        //include ID field, it's needed so the module knows if it is a new form or an edited form

        $hidden = new XoopsFormHidden(
            'friendpet_id',
            $this->targetObject->getVar(
                'friendpet_id'
            )
        );
        $this->addElement($hidden);
        unset($hidden);

        // Friendpet_id
        $this->addElement(
            new XoopsFormLabel(
                AM_YOGURT_FRIENDREQUEST_FRIENDPET_ID,
                $this->targetObject->getVar(
                    'friendpet_id'
                ),
                'friendpet_id'
            )
        );
        // Inviting by Friend_uid
        $this->addElement(
            new XoopsFormSelectUser(
                AM_YOGURT_FRIENDREQUEST_FRIENDREQUESTER_UID,
                'friendrequester_uid',
                false,
                $this->targetObject->getVar(
                    'friendrequester_uid'
                ),
                1,
                false
            ),
            false
        );
        // Invited Friend_uid
        $this->addElement(
            new XoopsFormSelectUser(
                \AM_YOGURT_FRIENDREQUEST_FRIENDREQUESTTO_UID,
                'friendrequestto_uid',
                false,
                $this->targetObject->getVar(
                    'friendrequestto_uid'
                ),
                1,
                false
            ),
            false
        );

        $this->addElement(new XoopsFormHidden('op', 'save'));
        $this->addElement(new XoopsFormButton('', 'submit', \_SUBMIT, 'submit'));
    }
}
