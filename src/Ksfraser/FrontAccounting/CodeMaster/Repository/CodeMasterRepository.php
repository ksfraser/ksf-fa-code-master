<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\Repository;
/**
 * @BABOK Related: BR-CM-001
 */
final class CodeMasterRepository {
    private string $table = 'ksf_code_master';
    /**
     * @BABOK Related: BR-CM-001
     */
    public function findAll(): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT * FROM " . TB_PREF . $this->table . " ORDER BY code_type ASC, sort_order ASC, code_value ASC";
        $result = db_query($sql, 'Could not load code master');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function findById(int $id): ?array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return null; }
        $sql = "SELECT * FROM " . TB_PREF . $this->table . " WHERE id = " . (int) $id;
        $result = db_query($sql, 'Could not load code master');
        if ($result === false) { return null; }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function findByTypeAndValue(string $codeType, string $codeValue): ?array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return null; }
        $sql = "SELECT * FROM " . TB_PREF . $this->table
            . " WHERE code_type = " . $this->escape($codeType)
            . " AND code_value = " . $this->escape($codeValue)
            . " LIMIT 1";
        $result = db_query($sql, 'Could not load code master by type and value');
        if ($result === false) { return null; }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function findByType(string $codeType): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT * FROM " . TB_PREF . $this->table
            . " WHERE code_type = " . $this->escape($codeType)
            . " AND is_active = 1"
            . " ORDER BY sort_order ASC, code_value ASC";
        $result = db_query($sql, 'Could not load code master by type');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function save(array $data): int {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return 0; }
        // Ensure uniqueness of code_type + code_value
        $existing = $this->findByTypeAndValue($data['code_type'] ?? '', $data['code_value'] ?? '');
        if ($existing !== null && (int) $existing['id'] !== (int) ($data['id'] ?? 0)) {
            return (int) $existing['id']; // already exists, return existing id
        }
        if (!empty($data['id'])) {
            $this->update((int) $data['id'], $data);
            return (int) $data['id'];
        }
        $sql = "INSERT INTO " . TB_PREF . $this->table . " (
                code_type, code_value, code_name, code_description, usage_instructions, is_active, sort_order)
            VALUES ("
            . $this->escape($data['code_type'] ?? '') . ", "
            . $this->escape($data['code_value'] ?? '') . ", "
            . $this->escape($data['code_name'] ?? '') . ", "
            . $this->escape($data['code_description'] ?? '') . ", "
            . $this->escape($data['usage_instructions'] ?? '') . ", "
            . (isset($data['is_active']) ? (int)$data['is_active'] : 1) . ", "
            . $this->escape($data['sort_order'] ?? 0) . ")";
        db_query($sql, 'Could not save code master');
        return db_insert_id();
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function update(int $id, array $data): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        $sets = [];
        foreach (['code_type', 'code_value', 'code_name', 'code_description', 'usage_instructions', 'is_active', 'sort_order'] as $col) {
            if (array_key_exists($col, $data)) {
                $val = ($col === 'is_active' || $col === 'sort_order') ? (int)$data[$col] : $this->escape($data[$col]);
                $sets[] = "$col = $val";
            }
        }
        if (empty($sets)) { return; }
        $sql = "UPDATE " . TB_PREF . $this->table . " SET " . implode(', ', $sets) . " WHERE id = " . (int) $id;
        db_query($sql, 'Could not update code master');
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function delete(int $id): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        $sql = "DELETE FROM " . TB_PREF . $this->table . " WHERE id = " . (int) $id;
        db_query($sql, 'Could not delete code master');
    }
    private function escape($value): string {
        return function_exists('db_escape') ? db_escape($value) : addslashes((string) $value);
    }
}
