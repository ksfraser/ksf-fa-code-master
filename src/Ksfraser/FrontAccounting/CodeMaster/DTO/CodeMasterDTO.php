<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\CodeMaster\DTO;
/**
 * @BABOK Related: BR-CM-001
 * @BABOK Related: BR-CM-002
 */
final class CodeMasterDTO {
    public $id;
    public $codeType;
    public $codeValue;
    public $codeName;
    public $codeDescription;
    public $usageInstructions;
    public $isActive;
    public $sortOrder;
    public $createdAt;
    /**
     * @BABOK Related: BR-CM-001
     */
    public function __construct(
        int $id,
        string $codeType = '',
        string $codeValue = '',
        string $codeName = '',
        string $codeDescription = '',
        string $usageInstructions = '',
        bool $isActive = true,
        int $sortOrder = 0,
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->codeType = $codeType;
        $this->codeValue = $codeValue;
        $this->codeName = $codeName;
        $this->codeDescription = $codeDescription;
        $this->usageInstructions = $usageInstructions;
        $this->isActive = $isActive;
        $this->sortOrder = $sortOrder;
        $this->createdAt = $createdAt;
    }
    /**
     * @BABOK Related: BR-CM-003
     */
    public function getDisplayLabel(): string {
        if ($this->codeName !== '') { return $this->codeName; }
        if ($this->codeValue !== '') { return $this->codeValue; }
        return _('Code #') . (string) $this->id;
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public function toArray(): array {
        return array(
            'id' => $this->id,
            'code_type' => $this->codeType,
            'code_value' => $this->codeValue,
            'code_name' => $this->codeName,
            'code_description' => $this->codeDescription,
            'usage_instructions' => $this->usageInstructions,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
            'created_at' => $this->createdAt,
        );
    }
    /**
     * @BABOK Related: BR-CM-001
     */
    public static function fromArray(array $data): self {
        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['code_type'] ?? ''),
            (string) ($data['code_value'] ?? ''),
            (string) ($data['code_name'] ?? ''),
            (string) ($data['code_description'] ?? ''),
            (string) ($data['usage_instructions'] ?? ''),
            !empty($data['is_active']),
            (int) ($data['sort_order'] ?? 0),
            (string) ($data['created_at'] ?? '')
        );
    }
}
