import { expect, test } from '@playwright/test';
import {
  loginAsAdmin,
  loginAsDonatur,
  loginAsOtherVendor,
  loginAsVendor,
} from '../helpers/auth.js';
import { createDummyImage } from '../helpers/files.js';

const PBI25_HAPPY_PROJECT_ID = 12;
const PBI25_HAPPY_ASSIGNMENT_ID = 8;
const PBI25_DRAFT_PROJECT_ID = 13;
const PBI25_DRAFT_ASSIGNMENT_ID = 9;
const PBI25_VALIDATION_ASSIGNMENT_ID = 10;
const PBI25_DUPLICATE_ASSIGNMENT_ID = 11;
const PBI25_ACCESS_ASSIGNMENT_ID = 12;

async function openVendorProject(page, assignmentId, projectTitle) {
  await page.goto(`/vendor/proyek/${assignmentId}`);
  await expect(page.getByText(projectTitle, { exact: true })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Form Laporan Akhir' })).toBeVisible();
}

async function fillFinalReport(page, { description, capacity = '15', unit = 'kWp', note = null, photoName = null }) {
  if (description !== undefined) {
    await page.locator('textarea[name="deskripsi"]').fill(description);
  }

  if (capacity !== undefined) {
    await page.locator('input[name="kapasitas_terpasang"]').fill(capacity);
  }

  await page.locator('select[name="satuan_kapasitas"]').selectOption(unit);

  if (note !== null) {
    await page.locator('textarea[name="catatan"]').fill(note);
  }

  if (photoName) {
    await page.locator('input[name="fotos[]"]').setInputFiles(createDummyImage(photoName));
  }
}

async function clearSession(page) {
  await page.context().clearCookies();
}

async function submitFinalReportPostFromCurrentSession(page, assignmentId) {
  const token = await page.locator('input[name="_token"]').first().inputValue();

  await page.evaluate(
    ({ assignmentId: targetAssignmentId, csrfToken }) => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/vendor/proyek/${targetAssignmentId}/laporan-akhir`;

      const tokenInput = document.createElement('input');
      tokenInput.type = 'hidden';
      tokenInput.name = '_token';
      tokenInput.value = csrfToken;
      form.appendChild(tokenInput);

      const descriptionInput = document.createElement('input');
      descriptionInput.type = 'hidden';
      descriptionInput.name = 'deskripsi';
      descriptionInput.value = 'Akses tidak valid mencoba submit laporan.';
      form.appendChild(descriptionInput);

      const capacityInput = document.createElement('input');
      capacityInput.type = 'hidden';
      capacityInput.name = 'kapasitas_terpasang';
      capacityInput.value = '10';
      form.appendChild(capacityInput);

      const unitInput = document.createElement('input');
      unitInput.type = 'hidden';
      unitInput.name = 'satuan_kapasitas';
      unitInput.value = 'kWp';
      form.appendChild(unitInput);

      document.body.appendChild(form);
      form.submit();
    },
    { assignmentId, csrfToken: token },
  );
}

async function assertProjectCompletedNotification(page, projectTitle) {
  await page.goto('/notifications');
  await expect(page.getByRole('heading', { name: 'Notifikasi' })).toBeVisible();
  await expect(page.getByText('Proyek Selesai', { exact: true }).first()).toBeVisible();
  await expect(page.getByText(projectTitle).first()).toBeVisible();
}

test.describe('PBI25 - Vendor Final Report', () => {
  test('TC-01 vendor berhasil submit laporan akhir', async ({ page }) => {
    await loginAsVendor(page);
    await openVendorProject(page, PBI25_HAPPY_ASSIGNMENT_ID, 'Proyek PBI25 Happy Path');

    await fillFinalReport(page, {
      description: 'Seluruh instalasi panel surya selesai dan sudah diuji warga.',
      capacity: '15',
      unit: 'kWp',
      note: 'Sistem siap digunakan warga.',
      photoName: 'pbi25-final-report-submit.png',
    });
    await page.getByRole('button', { name: 'Submit Laporan Akhir' }).click();

    await expect(page.getByText('Laporan akhir berhasil dikirim. Proyek ditandai selesai.')).toBeVisible();
    await expect(page.getByText('Laporan akhir sudah dikirim.')).toBeVisible();

    await page.goto(`/proyek/${PBI25_HAPPY_PROJECT_ID}`);
    await expect(page.getByRole('heading', { name: 'Proyek PBI25 Happy Path' })).toBeVisible();
    await expect(page.getByText('Selesai').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Laporan Akhir' })).toBeVisible();
    await expect(page.getByText('Seluruh instalasi panel surya selesai dan sudah diuji warga.')).toBeVisible();
    await expect(page.getByText('15 kWp')).toBeVisible();

    await clearSession(page);
    await loginAsAdmin(page);
    await assertProjectCompletedNotification(page, 'Proyek PBI25 Happy Path');

    await clearSession(page);
    await loginAsDonatur(page);
    await assertProjectCompletedNotification(page, 'Proyek PBI25 Happy Path');
  });

  test('TC-02 vendor simpan draft laporan akhir', async ({ page }) => {
    await loginAsVendor(page);
    await openVendorProject(page, PBI25_DRAFT_ASSIGNMENT_ID, 'Proyek PBI25 Draft');

    await fillFinalReport(page, {
      description: 'Instalasi selesai dan perangkat sedang dicek ulang.',
      capacity: '12.5',
      unit: 'kWp',
      note: 'Menunggu pengecekan akhir admin.',
      photoName: 'pbi25-final-report-draft.png',
    });
    await page.getByRole('button', { name: 'Simpan Draft' }).click();

    await expect(page.getByText('Draft laporan akhir tersimpan.')).toBeVisible();
    await expect(page.locator('textarea[name="deskripsi"]')).toHaveValue('Instalasi selesai dan perangkat sedang dicek ulang.');

    await page.goto(`/proyek/${PBI25_DRAFT_PROJECT_ID}`);
    await expect(page.getByRole('heading', { name: 'Proyek PBI25 Draft' })).toBeVisible();
    await expect(page.getByText('Sedang Berjalan').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Laporan Akhir' })).toHaveCount(0);
    await expect(page.getByText('Instalasi selesai dan perangkat sedang dicek ulang.')).toHaveCount(0);
  });

  test('TC-03 validasi field wajib deskripsi dan foto', async ({ page }) => {
    await loginAsVendor(page);
    await openVendorProject(page, PBI25_VALIDATION_ASSIGNMENT_ID, 'Proyek PBI25 Validasi');

    await fillFinalReport(page, {
      description: '',
      capacity: '15',
      unit: 'kWp',
      photoName: 'pbi25-final-report-validation.png',
    });
    await page.getByRole('button', { name: 'Submit Laporan Akhir' }).click();

    await expect(page.getByText('Deskripsi laporan wajib diisi').first()).toBeVisible();

    await fillFinalReport(page, {
      description: 'Laporan validasi tanpa foto dokumentasi.',
      capacity: '15',
      unit: 'kWp',
    });
    await page.getByRole('button', { name: 'Submit Laporan Akhir' }).click();

    await expect(page.getByText('Minimal 1 foto dokumentasi akhir wajib diunggah').first()).toBeVisible();
  });

  test('TC-04 validasi pencegahan duplikasi laporan', async ({ page }) => {
    await loginAsVendor(page);
    await page.goto(`/vendor/proyek/${PBI25_DUPLICATE_ASSIGNMENT_ID}`);

    await expect(page.getByText('Proyek PBI25 Duplikasi', { exact: true })).toBeVisible();
    await expect(page.getByText('Laporan akhir sudah dikirim.')).toBeVisible();

    await submitFinalReportPostFromCurrentSession(page, PBI25_DUPLICATE_ASSIGNMENT_ID);
    await expect(page.getByText('Laporan akhir sudah pernah disubmit')).toBeVisible();
  });

  test('TC-05 validasi hak akses RBAC dan kepemilikan', async ({ page }) => {
    await loginAsAdmin(page);
    await page.goto('/admin/dashboard');
    await submitFinalReportPostFromCurrentSession(page, PBI25_ACCESS_ASSIGNMENT_ID);
    await expect(page).toHaveURL(/\/vendor\/proyek\/12\/laporan-akhir$/);
    await expect(page.locator('body')).toContainText('403');

    await clearSession(page);
    await loginAsOtherVendor(page);
    await page.goto('/vendor/dashboard');
    await submitFinalReportPostFromCurrentSession(page, PBI25_ACCESS_ASSIGNMENT_ID);
    await expect(page).toHaveURL(/\/vendor\/proyek\/12\/laporan-akhir$/);
    await expect(page.locator('body')).toContainText('403');
  });
});
