<?php
declare(strict_types=1);
require_once __DIR__ . '/ComposerDependencies.php';
\Ksfraser\FrontAccounting\CodeMaster\Utils\ComposerDependencies::ensure(__DIR__);
$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) { require_once $autoload_path; }
require_once __DIR__ . '/includes/common.inc';
require_once __DIR__ . '/includes/events.inc';
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
define('SS_ksf_FA_CodeMaster', 172 << 8);
if (!defined('SA_CODEMASTER_VIEW')) {
    define('SA_CODEMASTER_VIEW', SS_ksf_FA_CodeMaster | 1);
    define('SA_CODEMASTER_MANAGE', SS_ksf_FA_CodeMaster | 2);
}
class hooks_ksf_FA_CodeMaster extends hooks {
    const RESPONDERS = array('RELATED_TO_FIELDS', 'CODEMASTER_RESOLVE', 'CODEMASTER_FIELDS', 'CODEMASTER_LIST');
    var $module_name = 'ksf_FA_CodeMaster';
    var $version = '1.0.0';
    function install_tabs($app) { }
    function install_options($app) { }
    function install_access() {
        $security_sections[SS_ksf_FA_CodeMaster] = _("Code Master Management");
        $security_areas['SA_CODEMASTER_VIEW'] = array(SA_CODEMASTER_VIEW, _("View code master"));
        $security_areas['SA_CODEMASTER_MANAGE'] = array(SA_CODEMASTER_MANAGE, _("Manage code master"));
        return array($security_areas, $security_sections);
    }
    function activate_extension($company, $check_only = true) {
        if ($check_only) { return true; }
        \Ksfraser\FrontAccounting\CodeMaster\Utils\ComposerDependencies::ensure(__DIR__);
        $this->update_databases($company, array('0_ksf_code_master.sql' => array('ksf_code_master')), false);
        return true;
    }
    private function ensure_composer_dependencies() {
        \Ksfraser\FrontAccounting\CodeMaster\Utils\ComposerDependencies::ensure(__DIR__);
    }
    public function getModuleConstants(&$data, $opts = null) {
        $data['constants'] = array('SS_ksf_FA_CodeMaster' => SS_ksf_FA_CodeMaster, 'SA_CODEMASTER_VIEW' => SA_CODEMASTER_VIEW, 'SA_CODEMASTER_MANAGE' => SA_CODEMASTER_MANAGE);
        return true;
    }
    public function getModuleCapabilities(&$data, $opts = null) {
        $data['capabilities'] = self::RESPONDERS;
        return true;
    }
    public function hasCapability(&$data, $opts) {
        $capability = is_array($opts) && isset($opts['capability']) ? $opts['capability'] : null;
        if ($capability === null && isset($data['capability'])) { $capability = $data['capability']; }
        $data['has_capability'] = in_array($capability, self::RESPONDERS, true);
        return $data['has_capability'];
    }
    public function respondToCapabilityRequest(&$data, $opts = null) {
        $request = is_array($opts) && isset($opts['request']) ? $opts['request'] : (isset($data['request']) ? $data['request'] : 'capabilities');
        $data['request'] = $request;
        $data['module'] = $this->module_name;
        if (strpos($request, 'has:') === 0) { return $this->hasCapability($data, array('capability' => substr($request, 4))); }
        switch ($request) {
            case 'capabilities': return $this->getModuleCapabilities($data, $opts);
            case 'constants': return $this->getModuleConstants($data, $opts);
            default: $data['error'] = 'Unknown request type: ' . $request; return null;
        }
    }
    public function RELATED_TO_FIELDS(&$data, $opts = null) {
        if (!isset($data['module']) || !is_string($data['module'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[module]'; return null; }
        if (!isset($data['type']) || !is_string($data['type'])) { $data['error'] = 'RELATED_TO_FIELDS requires data[type]'; return null; }
        if (!isset($data['fields']) || !is_array($data['fields'])) { $data['fields'] = array(); }
        $data['fields'] = array_merge($data['fields'], array(
            'code' => _('Code'),
            'name' => _('Name'),
            'description' => _('Description'),
            'usage_instructions' => _('Usage Instructions'),
        ));
        return null;
    }
    public function CODEMASTER_RESOLVE(&$data, $opts = null) {
        if (!isset($data['code_type']) || !isset($data['code_value'])) { $data['error'] = 'CODEMASTER_RESOLVE requires data[code_type] and data[code_value]'; return null; }
        if (!isset($data['dto']) || !$data['dto'] instanceof RelatedRecordDto) { $data['error'] = 'CODEMASTER_RESOLVE requires data[dto] as RelatedRecordDto'; return null; }
        $service = new \Ksfraser\FrontAccounting\CodeMaster\Service\CodeMasterService();
        $data['result'] = $service->resolveByTypeAndValue($data['code_type'], $data['code_value'], $data['dto']);
        return null;
    }
    public function CODEMASTER_FIELDS(&$data, $opts = null) {
        $data['fields'] = array(
            'code' => _('Code'),
            'name' => _('Name'),
            'description' => _('Description'),
            'usage_instructions' => _('Usage Instructions'),
        );
        return null;
    }
    public function CODEMASTER_LIST(&$data, $opts = null) {
        if (!isset($data['code_type']) || !is_string($data['code_type'])) { $data['error'] = 'CODEMASTER_LIST requires data[code_type]'; return null; }
        $service = new \Ksfraser\FrontAccounting\CodeMaster\Service\CodeMasterService();
        $data['result'] = $service->listByType($data['code_type']);
        return null;
    }
}
