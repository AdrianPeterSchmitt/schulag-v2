import { test, expect } from '@playwright/test';

test.describe('Navigation', () => {
  test.beforeEach(async ({ page }) => {
    // Login als Admin
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    await expect(page).toHaveURL(/.*admin/);
  });

  test('should navigate to Verwaltung', async ({ page }) => {
    await page.getByRole('link', { name: 'Verwaltung' }).click();
    
    await expect(page).toHaveURL(/.*admin/);
    await expect(page.getByRole('heading', { name: 'Verwaltung' })).toBeVisible();
  });

  test('should navigate to Klassen', async ({ page }) => {
    await page.getByRole('link', { name: 'Klassen', exact: true }).click();
    
    await expect(page).toHaveURL(/.*klassen/);
    await expect(page.getByRole('heading', { name: 'Klasse auswählen' })).toBeVisible();
  });

  test('should navigate to Losverfahren', async ({ page }) => {
    await page.getByRole('link', { name: 'Losverfahren', exact: true }).click();
    
    await expect(page).toHaveURL(/.*allocation/);
    await expect(page.getByRole('heading', { name: 'AG-Zuteilung' })).toBeVisible();
  });

  test('should show navigation menu', async ({ page }) => {
    // Navigation-Links sollten sichtbar sein
    await expect(page.getByRole('link', { name: 'Verwaltung' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Klassen', exact: true })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Losverfahren', exact: true })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Logout' })).toBeVisible();
  });
});

