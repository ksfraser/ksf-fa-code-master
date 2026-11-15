<?php
declare(strict_types=1);
use Ksfraser\FrontAccounting\CodeMaster\Service\CodeMasterService;
use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\UI\DefaultRelatedRecordsRenderer;
$path_to_root = '../../..';
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) { display_error(_('Code Master module dependencies are not installed')); exit; }
require_once $autoload;
$service = new CodeMasterService();
$action = isset($_GET['action']) ? (string) $_GET['action'] : 'list';
$codeType = isset($_GET['code_type']) ? (string) $_GET['code_type'] : '';
if ($action === 'list') {
    page(_('Code Master'));
    $types = $service->getCodeTypes();
    echo '<p>' . _('Code Master') . '</p>';
    echo '<p>' . _('Select a code type to view its codes:') . '</p>';
    echo '<ul>';
    foreach ($types as $t) {
        echo '<li><a href="?code_type=' . urlencode($t) . '">' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</a></li>';
    }
    echo '</ul>';
    if ($codeType !== '') {
        $codes = $service->listByType($codeType);
        echo '<h3>' . htmlspecialchars($codeType, ENT_QUOTES, 'UTF-8') . '</h3>';
        echo '<table class="selection">';
        echo '<thead><tr>' . '<th>' . _('Code') . '</th>' . '<th>' . _('Name') . '</th>' . '<th>' . _('Description') . '</th>' . '<th>' . _('Usage Instructions') . '</th>' . '<th>' . _('Actions') . '</th>' . '</tr></thead><tbody>';
        foreach ($codes as $c) {
            echo '<tr>' . '<td>' . htmlspecialchars($c['fields']['code'], ENT_QUOTES, 'UTF-8') . '</td>' . '<td>' . htmlspecialchars($c['fields']['name'], ENT_QUOTES, 'UTF-8') . '</td>' . '<td>' . htmlspecialchars($c['fields']['description'], ENT_QUOTES, 'UTF-8') . '</td>' . '<td>' . htmlspecialchars($c['fields']['usage_instructions'], ENT_QUOTES, 'UTF-8') . '</td>' . '<td><a href="' . htmlspecialchars($c['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank">' . _('Edit') . '</a></td>' . '</tr>';
        }
        echo '</tbody></table>';

        // Related records rendered via RelatedTo hook
        $lhsId = isset($_GET['id']) ? (string) $_GET['id'] : '';
        if (function_exists('hook_invoke') && class_exists(DefaultRelatedRecordsRenderer::class) && $lhsId !== '') {
            $queryDto = new RelatedQueryDto($codeType, $lhsId);
            $queryResult = $queryDto;
            hook_invoke('ksf_FA_RelatedTo', 'RELATED_TO', $queryResult, null);
            if (is_array($queryResult) && isset($queryResult['related']) && is_array($queryResult['related'])) {
                $renderer = new DefaultRelatedRecordsRenderer(array(
                    'label_resolver' => function ($type, $id) { return (string) $id; },
                    'url_resolver'   => function ($type, $id) { return '#'; },
                    'type_labels'    => array($codeType => ucfirst($codeType)),
                ));
                echo $renderer->renderRelatedRecords($codeType, $lhsId, $queryResult['related'], array(
                    'css_class' => 'related-records',
                    'header_class' => 'related-header',
                    'table_class' => 'related-table',
                    'table_border' => true,
                ));
            }
        }
    }
    end_page();
    exit;
}
display_error(_('Unknown action'));
exit;
