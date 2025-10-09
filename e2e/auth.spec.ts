import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
  test('should display login page', async ({ page }) => {
    await page.goto('/');
    
    // Sollte zu /login redirecten
    await expect(page).toHaveURL(/.*login/);
    
    // Login-Formular sollte sichtbar sein
    await expect(page.getByRole('heading', { name: 'Anmelden' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'E-Mail-Adresse' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Passwort' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Anmelden' })).toBeVisible();
  });

  test('should login successfully as admin', async ({ page }) => {
    await page.goto('/login');
    
    // Login-Formular ausfüllen
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    
    // Sollte zu /admin redirecten
    await expect(page).toHaveURL(/.*admin/);
    
    // Admin-Dashboard sollte sichtbar sein
    await expect(page.getByRole('heading', { name: 'Verwaltung' })).toBeVisible();
    await expect(page.getByText('Admin')).toBeVisible();
  });

  test('should fail with wrong credentials', async ({ page }) => {
    await page.goto('/login');
    
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('wrong@email.com');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('wrongpassword');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    
    // Sollte auf Login-Seite bleiben
    await expect(page).toHaveURL(/.*login/);
  });

  test('should logout successfully', async ({ page }) => {
    // Erst einloggen
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    
    await expect(page).toHaveURL(/.*admin/);
    
    // Logout klicken
    await page.getByRole('link', { name: 'Logout' }).click();
    
    // Sollte zu /login redirecten
    await expect(page).toHaveURL(/.*login/);
  });

  test('should redirect unauthenticated users', async ({ page }) => {
    await page.goto('/admin');
    
    // Sollte zu /login redirecten
    await expect(page).toHaveURL(/.*login/);
  });
});

