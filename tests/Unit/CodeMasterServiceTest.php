<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Tests\Unit;
use Ksfraser\FrontAccounting\CodeMaster\Repository\CodeMasterRepository;
use Ksfraser\FrontAccounting\CodeMaster\Service\CodeMasterService;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use PHPUnit\Framework\TestCase;
class CodeMasterServiceTest extends TestCase {
    protected function setUp(): void { \DbStub::reset(); }
    public function testResolveByTypeAndValueFound(): void {
        \DbStub::setRows([['id' => 3, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1', 'code_description' => 'First grade', 'usage_instructions' => 'Use me', 'is_active' => '1', 'sort_order' => '10']]);
        $service = new CodeMasterService();
        $result = $service->resolveByTypeAndValue('hrm.pay_grade', 'P1');
        $this->assertSame('P1', $result['fields']['code']);
        $this->assertSame('Grade 1', $result['fields']['name']);
        $this->assertSame('First grade', $result['fields']['description']);
        $this->assertSame('Use me', $result['fields']['usage_instructions']);
        $this->assertSame('Grade 1', $result['label']);
        $this->assertSame('/modules/ksf_FA_CodeMaster/pages/code_master_form.php?action=edit&code_id=3', $result['url']);
        $this->assertInstanceOf(RelatedRecordDto::class, $result['dto']);
        $this->assertSame('3', $result['dto']->rhsId);
    }
    public function testResolveByTypeAndValueNotFound(): void {
        \DbStub::setRows([]);
        $service = new CodeMasterService();
        $result = $service->resolveByTypeAndValue('hrm.pay_grade', 'NOPE');
        $this->assertSame('Code not found', $result['label']);
        $this->assertSame('#', $result['url']);
        $this->assertInstanceOf(RelatedRecordDto::class, $result['dto']);
    }
    public function testListByType(): void {
        \DbStub::setRows([
            ['id' => 1, 'code_type' => 'hrm.event_code', 'code_value' => 'REG', 'code_name' => 'Regular', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '10'],
            ['id' => 2, 'code_type' => 'hrm.event_code', 'code_value' => 'OT', 'code_name' => 'Overtime', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '20'],
        ]);
        $service = new CodeMasterService();
        $results = $service->listByType('hrm.event_code');
        $this->assertCount(2, $results);
        $this->assertSame('REG', $results[0]['fields']['code']);
        $this->assertSame('Regular', $results[0]['fields']['name']);
        $this->assertSame('OT', $results[1]['fields']['code']);
        $this->assertSame('Overtime', $results[1]['fields']['name']);
        $this->assertInstanceOf(RelatedRecordDto::class, $results[0]['dto']);
    }
    public function testListByTypeEmpty(): void {
        \DbStub::setRows([]);
        $service = new CodeMasterService();
        $this->assertSame([], $service->listByType('hrm.event_code'));
    }
    public function testGetCodeTypes(): void {
        \DbStub::setRows([
            ['id' => 1, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'G1', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '1'],
            ['id' => 2, 'code_type' => 'hrm.event_code', 'code_value' => 'REG', 'code_name' => 'Regular', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '2'],
            ['id' => 3, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P2', 'code_name' => 'G2', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '3'],
        ]);
        $service = new CodeMasterService();
        $types = $service->getCodeTypes();
        $this->assertSame(['hrm.pay_grade', 'hrm.event_code'], $types);
    }
    public function testFieldMapEqualsLabelMap(): void {
        $service = new CodeMasterService();
        $this->assertSame($service->labelMap(), $service->fieldMap());
    }
    public function testLabelMapKeysAndValues(): void {
        $service = new CodeMasterService();
        $this->assertSame([
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'usage_instructions' => 'Usage Instructions',
        ], $service->labelMap());
    }
}
