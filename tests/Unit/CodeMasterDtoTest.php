<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Tests\Unit;
use Ksfraser\FrontAccounting\CodeMaster\DTO\CodeMasterDTO;
use PHPUnit\Framework\TestCase;
class CodeMasterDtoTest extends TestCase {
    public function testGetDisplayLabelWithName(): void {
        $dto = new CodeMasterDTO(7, 'hrm.event_code', 'OT', 'Overtime', 'Overtime at 1.5x', 'Use for overtime', true, 2, '');
        $this->assertSame('Overtime', $dto->getDisplayLabel());
    }
    public function testGetDisplayLabelWithCodeOnly(): void {
        $dto = new CodeMasterDTO(3, 'hrm.pay_grade', 'S5', '', '', '', true, 0, '');
        $this->assertSame('S5', $dto->getDisplayLabel());
    }
    public function testGetDisplayLabelMinimal(): void {
        $dto = new CodeMasterDTO(1, '', '', '', '', '', true, 0, '');
        $this->assertSame('Code #1', $dto->getDisplayLabel());
    }
    public function testToArrayShape(): void {
        $dto = new CodeMasterDTO(7, 'hrm.event_code', 'OT', 'Overtime', 'Overtime at 1.5x', 'Use for overtime', true, 2, '');
        $array = $dto->toArray();
        $this->assertSame(7, $array['id']);
        $this->assertSame('hrm.event_code', $array['code_type']);
        $this->assertSame('OT', $array['code_value']);
        $this->assertSame('Overtime', $array['code_name']);
        $this->assertTrue($array['is_active']);
    }
    public function testFromArrayReconstructs(): void {
        $dto = CodeMasterDTO::fromArray(array(
            'id' => 9, 'code_type' => 'crm.lead_status', 'code_value' => 'NEW', 'code_name' => 'New Lead',
            'code_description' => 'A newly created lead', 'usage_instructions' => 'Initial status for all leads',
            'is_active' => true, 'sort_order' => 1, 'created_at' => ''
        ));
        $this->assertSame(9, $dto->id);
        $this->assertSame('crm.lead_status', $dto->codeType);
        $this->assertSame('NEW', $dto->codeValue);
        $this->assertSame('New Lead', $dto->codeName);
        $this->assertSame('A newly created lead', $dto->codeDescription);
        $this->assertSame('Initial status for all leads', $dto->usageInstructions);
        $this->assertTrue($dto->isActive);
    }
}
