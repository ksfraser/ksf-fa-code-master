<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Service;
use Ksfraser\FrontAccounting\CodeMaster\DTO\CodeMasterDTO;
use Ksfraser\FrontAccounting\CodeMaster\Repository\CodeMasterRepository;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
/**
 * @BABOK Related: BR-CM-001
 * @BABOK Related: BR-CM-002
 * @BABOK Related: BR-CM-003
 * @BABOK Related: BR-CM-004
 */
final class CodeMasterService {
    private $repo;
    /**
     * @BABOK Related: BR-CM-001
     */
    public function __construct(?CodeMasterRepository $repo = null) { $this->repo = $repo ?? new CodeMasterRepository(); }
    /**
     * @BABOK Related: BR-CM-002
     * @BABOK Related: BR-CM-003
     */
    public function resolveByTypeAndValue(string $codeType, string $codeValue, ?RelatedRecordDto $dto = null): array {
        $row = $this->repo->findByTypeAndValue($codeType, $codeValue);
        if ($row === null) {
            return array('dto' => new RelatedRecordDto('code_master', '0', 'code_master', '0', null), 'fields' => $this->fieldMap(), 'labels' => $this->labelMap(), 'url' => '#', 'label' => _('Code not found'));
        }
        $c = CodeMasterDTO::fromArray($row);
        $dto = $dto ?? new RelatedRecordDto('code_master', (string) $c->id, 'code_master', (string) $c->id, null);
        return array('dto' => $dto, 'fields' => array('code' => $c->codeValue, 'name' => $c->codeName, 'description' => $c->codeDescription, 'usage_instructions' => $c->usageInstructions), 'labels' => $this->labelMap(), 'url' => '/modules/ksf_FA_CodeMaster/pages/code_master_form.php?action=edit&code_id=' . $c->id, 'label' => $c->getDisplayLabel());
    }
    /**
     * @BABOK Related: BR-CM-002
     * @BABOK Related: BR-CM-003
     * @BABOK Related: BR-CM-004
     */
    public function listByType(string $codeType): array {
        $rows = $this->repo->findByType($codeType);
        $results = array();
        foreach ($rows as $row) {
            $c = CodeMasterDTO::fromArray($row);
            $results[] = array('id' => $c->id, 'dto' => new RelatedRecordDto('code_master', (string) $c->id, 'code_master', (string) $c->id, null), 'fields' => array('code' => $c->codeValue, 'name' => $c->codeName, 'description' => $c->codeDescription, 'usage_instructions' => $c->usageInstructions), 'labels' => $this->labelMap(), 'url' => '/modules/ksf_FA_CodeMaster/pages/code_master_form.php?action=edit&code_id=' . $c->id, 'label' => $c->getDisplayLabel());
        }
        return $results;
    }
    /**
     * @BABOK Related: BR-CM-003
     */
    public function fieldMap(): array { return $this->labelMap(); }
    /**
     * @BABOK Related: BR-CM-003
     */
    public function labelMap(): array {
        return array('code' => _('Code'), 'name' => _('Name'), 'description' => _('Description'), 'usage_instructions' => _('Usage Instructions'));
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function getCodeTypes(): array {
        $all = $this->repo->findAll();
        $types = array();
        foreach ($all as $row) {
            $types[$row['code_type']] = true;
        }
        return array_keys($types);
    }
}
