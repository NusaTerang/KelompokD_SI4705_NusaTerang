import { expect, test } from '@playwright/test';
import { loginAsVendor } from '../helpers/auth.js';
import { createDummyImage } from '../helpers/files.js';

async function openProgressPage(page, penugasanId) {
  await page.goto(`/vendor/proyek/${penugasanId}/progress`);
  await expect(page.getByRole('heading', { name: 'Update Progress Proyek' })).toBeVisible();
}

async function uploadProgressPhoto(page, filename) {
  await page.setInputFiles('#fotos', createDummyImage(filename));
}

async function selectProgressStatus(page, status) {
  const statusInput = page.locator(`input[name="status_progress"][value="${status}"]`);
  await statusInput.locator('..').click();
  await expect(statusInput).toBeChecked();
}

test.describe('PBI21 - Vendor Project Progress', () => {
  test('TC-01 vendor berhasil submit update progress', async ({ page }) => {
    await loginAsVendor(page);
    await openProgressPage(page, 1);

    await page.getByText('50%').click();
    await page.getByLabel('Keterangan Update').fill('Instalasi separuh selesai.');
    await uploadProgressPhoto(page, 'pbi21-progress-submit.png');
    await selectProgressStatus(page, 'berjalan');
    await page.getByRole('button', { name: 'Kirim Update' }).click();

    await expect(page.getByText('Update progress berhasil dikirim')).toBeVisible();
    await expect(page.getByText('Instalasi separuh selesai.')).toBeVisible();

    await page.goto('/proyek/1');
    await expect(page.getByText('Proyek PBI21 Submit')).toBeVisible();
    await expect(page.getByText('50%').first()).toBeVisible();
    await expect(page.getByText('Instalasi separuh selesai.')).toBeVisible();
  });

  test('TC-02 validasi persentase di luar range lewat batasan UI', async ({ page }) => {
    await loginAsVendor(page);
    await openProgressPage(page, 2);

    await expect(page.locator('input[name="persentase"][value="110"]')).toHaveCount(0);
    await expect(page.locator('input[name="persentase"][value="-5"]')).toHaveCount(0);

    await page.getByLabel('Keterangan Update').fill('Invalid percent.');
    await uploadProgressPhoto(page, 'pbi21-progress-invalid.png');
    await selectProgressStatus(page, 'berjalan');
    await page.getByRole('button', { name: 'Kirim Update' }).click();

    await expect(page.getByText('Pilih salah satu persentase penyelesaian.')).toBeVisible();
    await expect(page).toHaveURL(/\/vendor\/proyek\/2\/progress/);
  });

  test('TC-03 pilih status selesai munculkan form laporan akhir', async ({ page }) => {
    await loginAsVendor(page);
    await openProgressPage(page, 3);

    await page.getByText('100%').click();
    await page.getByLabel('Keterangan Update').fill('Semua komponen sudah terpasang dan diuji.');
    await uploadProgressPhoto(page, 'pbi21-progress-complete.png');
    await page.getByRole('button', { name: 'Kirim Update' }).click();

    await expect(page.getByText('Update progress berhasil dikirim')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Laporan Akhir' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Isi Laporan Akhir' })).toBeVisible();
  });

  test('TC-04 vendor simpan draft dan tidak tampil di halaman publik', async ({ page }) => {
    await loginAsVendor(page);
    await openProgressPage(page, 4);

    await page.getByText('25%').click();
    await page.getByLabel('Keterangan Update').fill('Panel mulai dipasang.');
    await uploadProgressPhoto(page, 'pbi21-progress-draft.png');
    await selectProgressStatus(page, 'berjalan');
    await page.getByRole('button', { name: 'Simpan Draft' }).click();

    await expect(page.getByText('Draft progress tersimpan.', { exact: true })).toBeVisible();
    await expect(page.getByLabel('Keterangan Update')).toHaveValue('Panel mulai dipasang.');

    await page.goto('/proyek/4');
    await expect(page.getByText('Proyek PBI21 Draft')).toBeVisible();
    await expect(page.getByText('Panel mulai dipasang.')).toHaveCount(0);
  });
});
