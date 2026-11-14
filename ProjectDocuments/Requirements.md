# Business Requirements — ksf_FA_CodeMaster

**Package:** `ksfraser/ksf-fa-code-master`
**Version:** `1.0.0`
**Namespace:** `Ksfraser\FrontAccounting\CodeMaster\*`
**Module sequence:** CM (Code Master)

---

## BR-CM-001 — Common code master table

**Status:** IMPLEMENTED

The system SHALL provide `ksf_FA_CodeMaster` as the owning module for all ref codes
used across the KSF FA ecosystem. All codes live in a single `0_ksf_code_master`
table namespaced by `code_type`.

1. Each code has: code_type, code_value, code_name, code_description,
   usage_instructions, is_active, sort_order.
2. code_type + code_value is unique (no duplicate codes within a type).
3. Code types are namespaced by module (e.g., 'hrm.pay_grade', 'hrm.event_code',
   'crm.lead_status').

---

## BR-CM-002 — Table-based ref codes (not hardcoded)

**Status:** IMPLEMENTED

All ref codes SHALL be table-based and administrable through the Code Master UI,
not hardcoded in SQL seed data or PHP constants.

1. Event codes (REG, OT, HOL, etc.) live in 0_ksf_code_master with code_type
   'hrm.event_code'.
2. Pay grade codes (S5, M3, etc.) live in 0_ksf_code_master with code_type
   'hrm.pay_grade'.
3. Modules reference codes by code_type + code_value, resolved at runtime.

---

## BR-CM-003 — Resolved DTO for RelatedTo

**Status:** IMPLEMENTED

Code Master SHALL build resolved display data for RelatedTo display.

1. Resolved data wraps a `RelatedRecordDto` with resolved fields,
   overridable labels, and a URL.
2. `fieldMap()` returns the field name => display label mapping.
3. `listByType()` returns resolved codes for a given code type.

---

## BR-CM-004 — CODEMASTER_LIST hook

**Status:** IMPLEMENTED

The `CODEMASTER_LIST` hook returns all codes for a given code_type.

1. Payload: `['code_type' => string]`
2. Returns: array of resolved code DTOs with code, name, description,
   usage_instructions fields.
