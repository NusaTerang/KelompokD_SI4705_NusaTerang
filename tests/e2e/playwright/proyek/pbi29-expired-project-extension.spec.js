import { expect, test } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import {
  loginAsAdmin,
  loginAsDonatur,
  loginAsOtherVendor,
  loginAsVendor,
} from '../helpers/auth.js';

const PBI29_FUNDED_PROJECT_ID = 18;
const PBI29_CONTINUE_PROJECT_ID = 19;
const PBI29_REFUND_PROJECT_ID = 20;
const PBI29_CONTINUE_ASSIGNMENT_ID = 14;
const PBI29_REFUND_ASSIGNMENT_ID = 15;
const PBI29_ACCESS_ASSIGNMENT_ID = 16;

function runArtisan(command) {
  execFileSync('php', ['artisan', command], {
    cwd: process.cwd(),
    env: process.env,
    stdio: 'pipe',
  });
}

async function clearSession(page) {
  await page.context().clearCookies();
}

async function openVendorProjects(page) {
  await page.goto('/vendor/proyek');
  await expect(page.getByRole('heading', { name: 'Proyek Saya' })).toBeVisible();
}

function vendorProjectCard(page, projectTitle) {
  return page
    .getByRole('heading', { name: projectTitle })
    .locator('xpath=ancestor::div[contains(@class, "bg-white")][1]');
}

async function openExpiryDecision(page, assignmentId, projectTitle) {
  await page.goto(`/vendor/proyek/${assignmentId}/expiry-decision`);
  await expect(page.getByRole('heading', { name: 'Keputusan Proyek Berakhir' })).toBeVisible();
  await expect(page.getByRole('heading', { name: projectTitle })).toBeVisible();
}

async function assertNotification(page, title, projectTitle) {
  await page.goto('/notifications');
  await expect(page.getByRole('heading', { name: 'Notifikasi' })).toBeVisible();
  await expect(page.getByText(title, { exact: true }).first()).toBeVisible();
  await expect(page.getByText(projectTitle).first()).toBeVisible();
}

async function assertPublicStatus(page, projectId, projectTitle, statusText) {
  await page.goto(`/proyek/${projectId}`);
  await expect(page.getByRole('heading', { name: projectTitle })).toBeVisible();
  await expect(page.getByText(statusText).first()).toBeVisible();
}

test.describe('PBI29 - Expired Project Extension', () => {
  test('TC-01 cron job memproses proyek underfunded yang batas waktunya habis', async ({ page }) => {
    runArtisan('proyek:extend-expired');

    await loginAsVendor(page);
    await openVendorProjects(page);
    const projectCard = vendorProjectCard(page, 'Proyek PBI29 Cron Underfunded');
    await expect(projectCard).toContainText('Menunggu Keputusan Refund/Lanjut');

    await assertNotification(page, 'Proyek Ditugaskan', 'Proyek PBI29 Cron Underfunded');

    await clearSession(page);
    await loginAsAdmin(page);
    await assertNotification(page, 'Proyek Ditugaskan', 'Proyek PBI29 Cron Underfunded');
  });

  test('TC-02 cron job tidak memproses proyek funded yang batas waktunya habis', async ({ page }) => {
    runArtisan('proyek:extend-expired');

    await loginAsVendor(page);
    await openVendorProjects(page);
    const projectCard = vendorProjectCard(page, 'Proyek PBI29 Cron Funded');
    await expect(projectCard).toContainText('Diterima');
    await expect(projectCard).not.toContainText('Menunggu Keputusan Refund/Lanjut');

    await assertPublicStatus(page, PBI29_FUNDED_PROJECT_ID, 'Proyek PBI29 Cron Funded', 'Sedang Berjalan');
  });

  test('TC-03 vendor memutuskan lanjutkan proyek', async ({ page }) => {
    await loginAsVendor(page);
    await openExpiryDecision(page, PBI29_CONTINUE_ASSIGNMENT_ID, 'Proyek PBI29 Lanjutkan');

    await page.getByRole('button', { name: 'Lanjutkan Proyek' }).click();
    await expect(page.getByText('Proyek dilanjutkan dengan dana terkumpul saat ini.')).toBeVisible();
    await expect(page).toHaveURL(/\/vendor\/proyek$/);

    await assertPublicStatus(page, PBI29_CONTINUE_PROJECT_ID, 'Proyek PBI29 Lanjutkan', 'Selesai');

    await clearSession(page);
    await loginAsAdmin(page);
    await assertNotification(page, 'Proyek Selesai', 'Proyek PBI29 Lanjutkan');

    await clearSession(page);
    await loginAsDonatur(page);
    await assertNotification(page, 'Proyek Selesai', 'Proyek PBI29 Lanjutkan');
  });

  test('TC-04 vendor memutuskan ajukan refund', async ({ page }) => {
    await loginAsVendor(page);
    await openExpiryDecision(page, PBI29_REFUND_ASSIGNMENT_ID, 'Proyek PBI29 Refund');

    await page.getByRole('button', { name: 'Ajukan Refund' }).click();
    await expect(page.getByText('Status proyek diubah menjadi refund.')).toBeVisible();
    await expect(page).toHaveURL(/\/vendor\/proyek$/);

    const refundCard = vendorProjectCard(page, 'Proyek PBI29 Refund');
    await expect(refundCard).toContainText('Refund');
    await expect(refundCard).toContainText('Selesai Diproses');

    await clearSession(page);
    await loginAsAdmin(page);
    await assertNotification(page, 'Laporan Refund Proyek', 'Proyek PBI29 Refund');

    await clearSession(page);
    await loginAsDonatur(page);
    await assertNotification(page, 'Refund Donasi Berhasil', 'Proyek PBI29 Refund');
  });

  test('TC-05 validasi hak akses keputusan RBAC dan kepemilikan', async ({ page }) => {
    await loginAsDonatur(page);
    let response = await page.goto(`/vendor/proyek/${PBI29_ACCESS_ASSIGNMENT_ID}/expiry-decision`);
    expect(response?.status()).toBe(403);

    await clearSession(page);
    await loginAsAdmin(page);
    response = await page.goto(`/vendor/proyek/${PBI29_ACCESS_ASSIGNMENT_ID}/expiry-decision`);
    expect(response?.status()).toBe(403);

    await clearSession(page);
    await loginAsOtherVendor(page);
    response = await page.goto(`/vendor/proyek/${PBI29_ACCESS_ASSIGNMENT_ID}/expiry-decision`);
    expect(response?.status()).toBe(404);
  });

  test('TC-06 cron job reminder 3 hari mengirim notifikasi ke vendor', async ({ page }) => {
    runArtisan('proyek:remind-pending-decision');

    await loginAsVendor(page);
    await assertNotification(page, 'Proyek Ditugaskan', 'Proyek PBI29 Reminder');
  });
});
