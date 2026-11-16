<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\CodeMaster\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

class CodeMasterFrontController extends AbstractFrontController
{
    public function __construct(
        CodeMasterAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
