<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\CodeMaster\Controller;

use Ksfraser\FrontAccounting\Common\App\AbstractTabController;
use Ksfraser\FrontAccounting\CodeMaster\Service\CodeMasterService;
use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\UI\DefaultRelatedRecordsRenderer;

class CodeMasterTabController extends AbstractTabController
{
    private $service;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new CodeMasterService();
    }

    protected function getPkField(): string { return 'id'; }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'code_master',
            'table'       => '0_ksf_code_master',
            'label'       => 'Code',
            'labelPlural' => 'Codes',
            'hookPrefix'  => 'code_master',
            'pk'          => 'id',
            'fields'      => [],
            'fk_ddls'   => [],
            'ddlHooks'  => [],
            'tableSettings' => [],
        ];
    }

    protected function listRows(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return array_slice($this->service->listAll(), $offset, $perPage);
    }

    protected function countRows(): int
    {
        return count($this->service->listAll());
    }

    protected function findRecord(string $pk): ?array { return null; }
    protected function createRecord(array $data): int { return 0; }
    protected function updateRecord(string $pk, array $data): void {}
    protected function deleteRecord(string $pk): void {}
    protected function fkOptions(): array { return []; }

    protected function renderSummaryTable(): void
    {
        if (!function_exists('start_table')) return;

        $page = max(1, $this->page());
        $perPage = $this->perPage;
        $rows = $this->listRows($page, $perPage);

        start_table(TABLESTYLE2, "width='95%'");
        $th = function (string $label): void {
            echo '<th>' . htmlspecialchars($this->localise($label), ENT_QUOTES) . '</th>';
        };
        echo '<tr>';
        $th(_('Code'));
        $th(_('Code Type'));
        $th(_('Name'));
        $th(_('Description'));
        $th(_('Usage Instructions'));
        $th(_('Active'));
        $th(_('Actions'));
        echo '</tr>';

        if (empty($rows)) {
            echo '<tr><td colspan="7"><em>'
                . htmlspecialchars($this->localise('No codes yet.'), ENT_QUOTES)
                . '</em></td></tr>';
        }

        foreach ($rows as $i => $row) {
            echo '<tr class="' . ($i % 2 ? 'evenrow' : 'oddrow') . '">';
            echo '<td>' . htmlspecialchars($row['fields']['code'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row['fields']['bank_type'] ?? $row['fields']['code_type'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row['fields']['name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row['fields']['description'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row['fields']['usage_instructions'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . ($row['fields']['is_active'] ?? false ? _('Yes') : _('No')) . '</td>';
            echo '<td><a href="' . htmlspecialchars($row['url'] ?? '#', ENT_QUOTES, 'UTF-8') . '" target="_blank">'
                . htmlspecialchars($this->localise('View'), ENT_QUOTES, 'UTF-8') . '</a></td>'
            . '</tr>';
        }

        end_table(0);
    }

    protected function renderNotes(): void
    {
        $lhsId = isset($_GET['id']) ? (string) $_GET['id'] : '';
        if ($lhsId === '' || !function_exists('hook_invoke')) return;

        $queryDto = new RelatedQueryDto('code_master', $lhsId);
        $queryResult = $queryDto;
        hook_invoke('ksf_FA_RelatedTo', 'RELATED_TO', $queryResult, null);

        if (!is_array($queryResult) || !isset($queryResult['related']) || !is_array($queryResult['related'])) {
            return;
        }

        $renderer = new DefaultRelatedRecordsRenderer(array(
            'css_class' => 'related-records',
            'header_class' => 'related-header',
            'table_class' => 'related-table',
            'table_border' => true,
            'type_labels' => array('code_master' => _('Code Master')),
        ));
        echo $renderer->renderRelatedRecords('code_master', $lhsId, $queryResult['related'], array(
            'css_class' => 'related-records',
            'header_class' => 'related-header',
            'table_class' => 'related-table',
            'table_border' => true,
        ));
    }
}
