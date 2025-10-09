import { test, expect } from '@playwright/test';

test.describe('Admin Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    await expect(page).toHaveURL(/.*admin/);
  });

  test('should display admin dashboard with statistics', async ({ page }) => {
    // Statistik-Karten sollten sichtbar sein (spezifischer Selector)
    await expect(page.getByRole('main').getByText('Klassen', { exact: true })).toBeVisible();
    await expect(page.getByRole('main').getByText('Schüler', { exact: true })).toBeVisible();
    await expect(page.getByRole('main').getByText('AGs', { exact: true })).toBeVisible();
    
    // Zahlen sollten angezeigt werden
    await expect(page.getByText('19', { exact: true }).first()).toBeVisible(); // Klassen
    await expect(page.getByText('91', { exact: true }).first()).toBeVisible(); // Schüler
  });

  test('should display Schnellzugriff section', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Schnellzugriff' })).toBeVisible();
    await expect(page.getByRole('link', { name: /Klassen verwalten/ })).toBeVisible();
    await expect(page.getByRole('link', { name: /AGs verwalten/ })).toBeVisible();
    await expect(page.getByRole('link', { name: /Losverfahren.*Zuteilung/ })).toBeVisible();
  });

  test('should navigate to Klassen management', async ({ page }) => {
    await page.getByRole('link', { name: /Klassen verwalten/ }).first().click();
    
    await expect(page).toHaveURL(/.*admin\/klassen/);
  });

  test('should navigate to AGs management', async ({ page }) => {
    await page.getByRole('link', { name: /AGs verwalten/ }).first().click();
    
    await expect(page).toHaveURL(/.*admin\/clubs/);
  });
});

test.describe('Losverfahren', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
    await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
    await page.getByRole('button', { name: 'Anmelden' }).click();
    await page.getByRole('link', { name: 'Losverfahren', exact: true }).click();
  });

  test('should display Losverfahren dashboard', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'AG-Zuteilung' })).toBeVisible();
    await expect(page.getByText('Schüler mit Wahlen')).toBeVisible();
    await expect(page.getByText('Klassen vollständig')).toBeVisible();
    await expect(page.getByText('AG-Angebote')).toBeVisible();
  });

  test('should show Losverfahren button (disabled when incomplete)', async ({ page }) => {
    const button = page.getByRole('button', { name: 'Losverfahren starten' });
    await expect(button).toBeVisible();
    
    // Button sollte disabled sein wenn nicht alle Klassen vollständig
    await expect(button).toBeDisabled();
  });

  test('should display Schnellzugriff links', async ({ page }) => {
    await expect(page.getByRole('link', { name: /Ergebnisse anzeigen/ })).toBeVisible();
    await expect(page.getByRole('link', { name: /Tausche verwalten/ })).toBeVisible();
    await expect(page.getByRole('link', { name: /Statistiken/ })).toBeVisible();
  });
});

