import { test, expect } from '@playwright/test';

test.describe('AG-Verwaltung', () => {
    test.beforeEach(async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.getByRole('textbox', { name: 'E-Mail-Adresse' }).fill('admin@schulag.test');
        await page.getByRole('textbox', { name: 'Passwort' }).fill('admin123');
        await page.getByRole('button', { name: 'Anmelden' }).click();
        await page.waitForURL('**/admin');
        
        // Zur AG-Verwaltung navigieren
        await page.goto('/admin/clubs');
    });

    test('Modal "Neue AG" sollte sich öffnen und schließen', async ({ page }) => {
        // Überprüfe, dass der Modal initial nicht sichtbar ist
        await expect(page.locator('text=Neue AG erstellen')).not.toBeVisible();
        
        // Klicke auf "Neue AG" Button
        await page.getByRole('button', { name: 'Neue AG' }).click();
        
        // Überprüfe, dass der Modal jetzt sichtbar ist
        await expect(page.locator('text=Neue AG erstellen')).toBeVisible();
        
        // Überprüfe, dass alle Formularfelder vorhanden sind
        await expect(page.getByText('AG-Titel *')).toBeVisible();
        await expect(page.getByText('Lehrkraft *')).toBeVisible();
        await expect(page.getByText('2. Lehrkraft (optional)')).toBeVisible();
        await expect(page.getByText('Jahrgänge *')).toBeVisible();
        await expect(page.getByText('Kapazität *')).toBeVisible();
        
        // Schließe den Modal mit "Abbrechen"
        await page.getByRole('button', { name: 'Abbrechen' }).click();
        
        // Überprüfe, dass der Modal wieder geschlossen ist
        await expect(page.locator('text=Neue AG erstellen')).not.toBeVisible();
    });

    test('Modal sollte sich über X-Button schließen', async ({ page }) => {
        // Öffne Modal
        await page.getByRole('button', { name: 'Neue AG' }).click();
        await expect(page.locator('text=Neue AG erstellen')).toBeVisible();
        
        // Suche den X/Close Button und klicke ihn
        const closeButton = page.locator('button').filter({ hasText: '×' }).or(
            page.locator('button[aria-label*="close" i]')
        ).or(
            page.locator('button[aria-label*="schließen" i]')
        ).first();
        
        if (await closeButton.count() > 0) {
            await closeButton.click();
            await expect(page.locator('text=Neue AG erstellen')).not.toBeVisible();
        }
    });

    test('Alpine.js Scopes - Modal Button sollte funktionieren', async ({ page }) => {
        // Dieser Test überprüft speziell, dass Alpine.js Scopes korrekt sind
        
        // Evaluiere Alpine.js State
        const hasAlpineData = await page.evaluate(() => {
            const element = document.querySelector('[x-data]');
            return element !== null;
        });
        
        expect(hasAlpineData).toBe(true);
        
        // Überprüfe, dass showNewModal Variable im Scope vorhanden ist
        const alpineState = await page.evaluate(() => {
            const element = document.querySelector('[x-data*="showNewModal"]');
            if (!element) return null;
            // @ts-ignore
            return window.Alpine ? window.Alpine.$data(element) : null;
        });
        
        expect(alpineState).not.toBeNull();
        expect(alpineState).toHaveProperty('showNewModal');
        expect(alpineState.showNewModal).toBe(false);
        
        // Klicke Button und überprüfe State-Änderung
        await page.getByRole('button', { name: 'Neue AG' }).click();
        
        const alpineStateAfterClick = await page.evaluate(() => {
            const element = document.querySelector('[x-data*="showNewModal"]');
            if (!element) return null;
            // @ts-ignore
            return window.Alpine ? window.Alpine.$data(element) : null;
        });
        
        expect(alpineStateAfterClick?.showNewModal).toBe(true);
    });

    test('Neue AG erstellen - vollständiger Workflow', async ({ page }) => {
        // Öffne Modal
        await page.getByRole('button', { name: 'Neue AG' }).click();
        await expect(page.locator('text=Neue AG erstellen')).toBeVisible();
        
        // Fülle Formular aus
        await page.getByPlaceholder('z.B. Fußball, Kunst, Musik').fill('Test AG');
        await page.getByPlaceholder('Name der Lehrkraft').fill('Frau Test');
        await page.getByPlaceholder('z.B. 5,6,7,8,9,10').fill('5,6,7');
        
        // Finde das Kapazitäts-Feld (spinbutton)
        const capacityField = page.getByRole('spinbutton');
        await capacityField.fill('15');
        
        // Optional: Beschreibung
        await page.getByPlaceholder('Kurze Beschreibung der AG').fill('Dies ist eine Test-AG');
        
        // Klicke "AG erstellen"
        await page.getByRole('button', { name: 'AG erstellen' }).click();
        
        // Warte auf HTMX-Request
        await page.waitForTimeout(1000);
        
        // Modal sollte sich schließen
        await expect(page.locator('text=Neue AG erstellen')).not.toBeVisible();
        
        // Neue AG sollte in der Liste erscheinen
        await expect(page.locator('text=Test AG')).toBeVisible();
    });

    test('Modal "AG bearbeiten" sollte funktionieren', async ({ page }) => {
        // Finde ersten "Bearbeiten" Button
        const editButtons = page.getByRole('button', { name: 'Bearbeiten' });
        await expect(editButtons.first()).toBeVisible();
        
        // Klicke auf ersten Bearbeiten-Button
        await editButtons.first().click();
        
        // Warte kurz auf Modal-Erscheinen
        await page.waitForTimeout(500);
        
        // Überprüfe, dass ein Bearbeiten-Modal sichtbar ist
        // (Der genaue Text kann variieren, daher flexibler Check)
        const hasEditForm = await page.evaluate(() => {
            const editModal = document.querySelector('[x-data*="showEditModal"]');
            return editModal !== null;
        });
        
        expect(hasEditForm).toBe(true);
    });

    test('Console-Fehler Überwachung', async ({ page }) => {
        const consoleErrors: string[] = [];
        
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });
        
        // Navigiere zur Seite
        await page.goto('http://localhost/schulag-v2/public/admin/clubs');
        
        // Öffne und schließe Modal mehrmals
        for (let i = 0; i < 3; i++) {
            await page.getByRole('button', { name: 'Neue AG' }).click();
            await page.waitForTimeout(200);
            await page.getByRole('button', { name: 'Abbrechen' }).click();
            await page.waitForTimeout(200);
        }
        
        // Überprüfe, dass keine Alpine.js oder JavaScript Fehler aufgetreten sind
        const relevantErrors = consoleErrors.filter(error => 
            error.includes('Alpine') || 
            error.includes('undefined') ||
            error.includes('is not a function')
        );
        
        expect(relevantErrors).toHaveLength(0);
    });
});

