<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Tests\Unit;
use Ksfraser\FrontAccounting\CodeMaster\Repository\CodeMasterRepository;
use PHPUnit\Framework\TestCase;
class CodeMasterRepositoryTest extends TestCase {
    protected function setUp(): void { \DbStub::reset(); }
    public function testFindAllReturnsRows(): void {
        \DbStub::setRows([[
            'id' => 1, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1',
            'code_description' => 'First grade', 'usage_instructions' => 'Use', 'is_active' => '1', 'sort_order' => '10',
        ]]);
        $repo = new CodeMasterRepository();
        $rows = $repo->findAll();
        $this->assertCount(1, $rows);
        $this->assertSame('hrm.pay_grade', $rows[0]['code_type']);
        $this->assertStringContainsString('ksf_code_master', \DbStub::$lastSql);
    }
    public function testFindAllDbFailureReturnsEmpty(): void {
        \DbStub::$queryResult = false;
        $repo = new CodeMasterRepository();
        $this->assertSame([], $repo->findAll());
    }
    public function testFindByIdReturnsRow(): void {
        \DbStub::setRows([['id' => 3, 'code_type' => 'hrm.event_code', 'code_value' => 'OT', 'code_name' => 'Overtime', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '20']]);
        $repo = new CodeMasterRepository();
        $row = $repo->findById(3);
        $this->assertSame(3, $row['id']);
        $this->assertStringContainsString('WHERE id = 3', \DbStub::$lastSql);
    }
    public function testFindByIdNotFoundReturnsNull(): void {
        \DbStub::setRows([]);
        $repo = new CodeMasterRepository();
        $this->assertNull($repo->findById(99));
    }
    public function testFindByTypeAndValueReturnsRow(): void {
        \DbStub::setRows([['id' => 2, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P2', 'code_name' => 'Grade 2', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '15']]);
        $repo = new CodeMasterRepository();
        $row = $repo->findByTypeAndValue('hrm.pay_grade', 'P2');
        $this->assertSame(2, $row['id']);
        $this->assertSame('P2', $row['code_value']);
        $this->assertStringContainsString("code_type = ", \DbStub::$lastSql);
        $this->assertStringContainsString("code_value = ", \DbStub::$lastSql);
        $this->assertStringContainsString('LIMIT 1', \DbStub::$lastSql);
    }
    public function testFindByTypeAndValueNotFoundReturnsNull(): void {
        \DbStub::setRows([]);
        $repo = new CodeMasterRepository();
        $this->assertNull($repo->findByTypeAndValue('hrm.pay_grade', 'NOPE'));
    }
    public function testFindByTypeReturnsRows(): void {
        \DbStub::setRows([
            ['id' => 1, 'code_type' => 'hrm.event_code', 'code_value' => 'REG', 'code_name' => 'Regular', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '10'],
            ['id' => 2, 'code_type' => 'hrm.event_code', 'code_value' => 'OT', 'code_name' => 'Overtime', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '20'],
        ]);
        $repo = new CodeMasterRepository();
        $rows = $repo->findByType('hrm.event_code');
        $this->assertCount(2, $rows);
        $this->assertStringContainsString("code_type = ", \DbStub::$lastSql);
        $this->assertStringContainsString('is_active = 1', \DbStub::$lastSql);
    }
    public function testFindByTypeEmpty(): void {
        \DbStub::setRows([]);
        $repo = new CodeMasterRepository();
        $this->assertSame([], $repo->findByType('hrm.event_code'));
    }
    public function testSaveNewReturnsInsertId(): void {
        \DbStub::$insertId = 21;
        \DbStub::setRows([]); // no existing -> insert path
        $repo = new CodeMasterRepository();
        $id = $repo->save(['code_type' => 'hrm.event_code', 'code_value' => 'HOL', 'code_name' => 'Holiday', 'code_description' => '', 'usage_instructions' => '', 'is_active' => 1, 'sort_order' => 30]);
        $this->assertSame(21, $id);
        $this->assertSame(2, \DbStub::$queryCalls);
        $this->assertStringContainsString('INSERT INTO', \DbStub::$lastSql);
        $this->assertStringContainsString('ksf_code_master', \DbStub::$lastSql);
    }
    public function testSaveExistingTypeValueReturnsExistingIdUniqueness(): void {
        \DbStub::setRows([['id' => 5, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1', 'code_description' => '', 'usage_instructions' => '', 'is_active' => '1', 'sort_order' => '10']]);
        $repo = new CodeMasterRepository();
        $id = $repo->save(['code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1', 'code_description' => '', 'usage_instructions' => '', 'is_active' => 1, 'sort_order' => 10, 'id' => 99]);
        $this->assertSame(5, $id);
        // Only the uniqueness SELECT ran; no INSERT/UPDATE.
        $this->assertSame(1, \DbStub::$queryCalls);
        $this->assertStringContainsString('SELECT', \DbStub::$lastSql);
    }
    public function testSaveWithExistingIdUpdates(): void {
        \DbStub::setRows([]); // no conflicting type/value
        $repo = new CodeMasterRepository();
        $id = $repo->save(['id' => 7, 'code_type' => 'hrm.pay_grade', 'code_value' => 'P1', 'code_name' => 'Grade 1', 'code_description' => '', 'usage_instructions' => '', 'is_active' => 1, 'sort_order' => 10]);
        $this->assertSame(7, $id);
        $this->assertSame(2, \DbStub::$queryCalls);
        $this->assertStringContainsString('UPDATE', \DbStub::$lastSql);
    }
    public function testUpdateCallsDbQuery(): void {
        $repo = new CodeMasterRepository();
        $repo->update(3, ['code_name' => 'Renamed']);
        $this->assertStringContainsString('UPDATE', \DbStub::$lastSql);
        $this->assertStringContainsString('ksf_code_master', \DbStub::$lastSql);
        $this->assertStringContainsString('WHERE id = 3', \DbStub::$lastSql);
    }
    public function testUpdateNoSetsDoesNothing(): void {
        $repo = new CodeMasterRepository();
        $repo->update(3, []);
        $this->assertNull(\DbStub::$lastSql);
    }
    public function testDeleteCallsDbQuery(): void {
        $repo = new CodeMasterRepository();
        $repo->delete(4);
        $this->assertStringContainsString('DELETE FROM', \DbStub::$lastSql);
        $this->assertStringContainsString('ksf_code_master', \DbStub::$lastSql);
        $this->assertStringContainsString('WHERE id = 4', \DbStub::$lastSql);
    }
}
