# RTM — ksf_FA_CodeMaster

**Module:** `ksf_FA_CodeMaster`
**Version:** `1.0.0`
**Requirement definitions:** `ProjectDocuments/Requirements.md`

| BR | Requirement | Implementation | Tests | Status |
|----|-------------|----------------|-------|--------|
| BR-CM-001 | Common code master table | `DTO/CodeMasterDTO`, `Repository/CodeMasterRepository` | `tests/Unit/CodeMasterDtoTest.php` | IMPLEMENTED |
| BR-CM-002 | Table-based ref codes | `Service/CodeMasterService` table-based code management | `tests/Unit/CodeMasterDtoTest.php` | IMPLEMENTED |
| BR-CM-003 | Resolved DTO for RelatedTo | `Service/CodeMasterService::resolveByTypeAndValue()`, `::listByType()`, `::fieldMap()` | `tests/Unit/CodeMasterDtoTest.php` | IMPLEMENTED |
| BR-CM-004 | CODEMASTER_LIST hook | `Service/CodeMasterService::listByType()`, `hooks.php::CODEMASTER_LIST` | `tests/Unit/CodeMasterDtoTest.php` | IMPLEMENTED |
