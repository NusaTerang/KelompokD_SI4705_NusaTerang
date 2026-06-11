import { expect, test } from '@playwright/test';

const PBI22_RUNNING_PROJECT_ID = 5;
const PBI22_FINISHED_PROJECT_ID = 6;
const PBI22_EMPTY_PROJECT_ID = 7;

async function openMonitoringPage(page, projectId) {
  await page.goto(`/projects/${projectId}/monitoring`);
  await expect(page.getByRole('heading', { name: /Proyek PBI22/ })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Timeline & Progress Updates' })).toBeVisible();
}

test.describe('PBI22 - Public Project Monitoring Timeline', () => {
  test('TC-01 publik dapat melihat monitoring proyek sedang berjalan dengan progress terbaru dan foto', async ({ page }) => {
    await openMonitoringPage(page, PBI22_RUNNING_PROJECT_ID);

    await expect(page.getByText('Proyek PBI22 Sedang Berjalan')).toBeVisible();
    await expect(page.getByText('Sedang Berjalan').first()).toBeVisible();
    await expect(page.getByText('75%').first()).toBeVisible();
    await expect(page.locator('div[style="width: 75%"]')).toBeVisible();

    const pageText = await page.locator('body').innerText();
    expect(pageText.indexOf('Panel surya PBI22 hampir selesai.')).toBeGreaterThanOrEqual(0);
    expect(pageText.indexOf('Update lama PBI22.')).toBeGreaterThanOrEqual(0);
    expect(pageText.indexOf('Panel surya PBI22 hampir selesai.')).toBeLessThan(pageText.indexOf('Update lama PBI22.'));

    await expect(page.locator('img[src*="/storage/progress/pbi22-lapangan.jpg"]')).toBeVisible();
  });

  test('TC-02 publik dapat melihat monitoring proyek selesai dan laporan akhir', async ({ page }) => {
    await openMonitoringPage(page, PBI22_FINISHED_PROJECT_ID);

    await expect(page.getByText('Proyek PBI22 Selesai')).toBeVisible();
    await expect(page.getByText('Selesai').first()).toBeVisible();
    await expect(page.getByText('100%').first()).toBeVisible();
    await expect(page.locator('div[style="width: 100%"]').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Laporan Akhir' })).toBeVisible();
    await expect(page.getByText('Instalasi PBI22 selesai dan sudah diuji bersama warga.')).toBeVisible();
  });

  test('TC-03 publik melihat pesan kosong saat proyek belum memiliki update progress', async ({ page }) => {
    await openMonitoringPage(page, PBI22_EMPTY_PROJECT_ID);

    await expect(page.getByText('Proyek PBI22 Belum Ada Update')).toBeVisible();
    await expect(page.getByText('Vendor belum mengunggah update progres')).toBeVisible();
    await expect(page.getByText('0%').first()).toBeVisible();
  });

  test('TC-04 publik mendapat 404 saat proyek tidak ditemukan', async ({ page }) => {
    const response = await page.goto('/projects/999999/monitoring');

    expect(response?.status()).toBe(404);
    await expect(page).toHaveURL(/\/projects\/999999\/monitoring$/);
  });
});
