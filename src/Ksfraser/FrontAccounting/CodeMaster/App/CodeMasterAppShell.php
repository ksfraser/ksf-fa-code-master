<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\CodeMaster\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use Ksfraser\FrontAccounting\CodeMaster\Controller\CodeMasterTabController;

class CodeMasterAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('code_master', 'index.php', 'view', 'code_master');

        $this->add_module(_("Code Master"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_CodeMaster/index.php',
            'view',
            ''
        );

        $menu->addItem('code_master', _("Code Master"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_CODEMASTER_VIEW');

        $this->add_extensions();
    }
}
