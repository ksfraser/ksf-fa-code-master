<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Tests\Unit;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../hooks.php';
class CodeMasterHooksTest extends TestCase {
    protected function setUp(): void { \DbStub::reset(); }
    private function hooks(): \hooks_ksf_FA_CodeMaster {
        return new \hooks_ksf_FA_CodeMaster();
    }
    public function testRespondersConstant(): void {
        $responders = \hooks_ksf_FA_CodeMaster::RESPONDERS;
        $this->assertTrue(in_array('RELATED_TO_FIELDS', $responders, true));
        $this->assertTrue(in_array('CODEMASTER_RESOLVE', $responders, true));
        $this->assertTrue(in_array('CODEMASTER_FIELDS', $responders, true));
        $this->assertTrue(in_array('CODEMASTER_LIST', $responders, true));
    }
    public function testRelatedToFields(): void {
        $data = ['module' => 'ksf_FA_CodeMaster', 'type' => 'code_master', 'fields' => []];
        $this->hooks()->RELATED_TO_FIELDS($data);
        $this->assertSame('Code', $data['fields']['code']);
        $this->assertSame('Usage Instructions', $data['fields']['usage_instructions']);
    }
    public function testRelatedToFieldsMissingModuleError(): void {
        $data = ['type' => 'code_master'];
        $this->hooks()->RELATED_TO_FIELDS($data);
        $this->assertArrayHasKey('error', $data);
    }
    public function testCodeMasterResolve(): void {
        \DbStub::setRows([['id' => 3, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1', 'code_description' => 'First', 'usage_instructions' => 'Use', 'is_active' => '1', 'sort_order' => '10']]);
        $dto = new RelatedRecordDto('code_master', '0', 'code_master', '0', null);
        $data = ['code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'dto' => $dto];
        $this->hooks()->CODEMASTER_RESOLVE($data);
        $this->assertArrayHasKey('result', $data);
        $this->assertSame('P1', $data['result']['fields']['code']);
        $this->assertSame('Grade 1', $data['result']['fields']['name']);
        $this->assertSame('Grade 1', $data['result']['label']);
        $this->assertSame($dto, $data['result']['dto']);
    }
    public function testCodeMasterResolveMissingCodeTypeError(): void {
        $data = ['code_value' => 'P1', 'dto' => new RelatedRecordDto('code_master', '0', 'code_master', '0', null)];
        $this->hooks()->CODEMASTER_RESOLVE($data);
        $this->assertArrayHasKey('error', $data);
    }
    public function testCodeMasterResolveMissingCodeValueError(): void {
        $data = ['code_type' => 'hrm.pay_grade', 'dto' => new RelatedRecordDto('code_master', '0', 'code_master', '0', null)];
        $this->hooks()->CODEMASTER_RESOLVE($data);
        $this->assertArrayHasKey('error', $data);
    }
    public function testCodeMasterResolveMissingDtoError(): void {
        $data = ['code_type' => 'hrm.pay_grade', 'code_value' => 'P1'];
        $this->hooks()->CODEMASTER_RESOLVE($data);
        $this->assertArrayHasKey('error', $data);
    }
    public function testCodeMasterFields(): void {
        $data = ['fields' => []];
        $this->hooks()->CODEMASTER_FIELDS($data);
        $this->assertSame('Code', $data['fields']['code']);
        $this->assertSame('Description', $data['fields']['description']);
    }
    public function testCodeMasterList(): void {
        \DbStub::setRows([
            ['id' => 1, 'code_type' => 'hrm.event_code', 'code_value' => 'REG', 'code_name' => 'Regular', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '10'],
            ['id' => 2, 'code_type' => 'hrm.event_code', 'code_value' => 'OT', 'code_name' => 'Overtime', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '20'],
        ]);
        $data = ['code_type' => 'hrm.event_code'];
        $this->hooks()->CODEMASTER_LIST($data);
        $this->assertArrayHasKey('result', $data);
        $this->assertCount(2, $data['result']);
        $this->assertSame('REG', $data['result'][0]['fields']['code']);
        $this->assertSame('OT', $data['result'][1]['fields']['code']);
    }
    public function testCodeMasterListMissingCodeTypeError(): void {
        $data = [];
        $this->hooks()->CODEMASTER_LIST($data);
        $this->assertArrayHasKey('error', $data);
    }
}
