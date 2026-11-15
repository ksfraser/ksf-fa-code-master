import { test, expect } from '@playwright/test';

test.describe('ksf_FA_CodeMaster Code Master Module', () => {
  test('should load code master list page', async ({ page }) => {
    await page.goto('/modules/ksf_FA_CodeMaster/pages/code_master.php');
    await expect(page.locator('h1, h2')).toContainText(/Code Master/i);
  });
});
