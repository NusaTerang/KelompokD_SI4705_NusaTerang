import { expect, test } from '@playwright/test';
import {
  loginAsAdmin,
  loginAsDonatur,
  loginAsDonaturB,
  loginAsEmptyDonatur,
  loginAsVendor,
} from '../helpers/auth.js';

async function openNotifications(page) {
  await page.goto('/notifications');
  await expect(page.getByRole('heading', { name: 'Notifikasi' })).toBeVisible();
}

async function assertNotificationVisible(page, title, projectTitle) {
  await expect(page.getByText(title, { exact: true }).first()).toBeVisible();
  await expect(page.getByText(projectTitle).first()).toBeVisible();
}

async function clearSession(page) {
  await page.context().clearCookies();
}

test.describe('PBI23 - Notifications', () => {
  test('TC-01 notifikasi event terkirim ke role yang tepat', async ({ page }) => {
    await loginAsVendor(page);
    await openNotifications(page);
    await assertNotificationVisible(page, 'Proyek Ditugaskan', 'Proyek PBI21 Submit');
    await expect(page.getByText('Detail Proyek Diisi')).toHaveCount(0);
    await expect(page.getByText('Target Dana Tercapai')).toHaveCount(0);

    await clearSession(page);

    await loginAsAdmin(page);
    await openNotifications(page);
    await assertNotificationVisible(page, 'Detail Proyek Diisi', 'Proyek PBI21 Submit');
    await expect(page.getByText('Proyek Ditugaskan')).toHaveCount(0);
    await expect(page.getByText('Target Dana Tercapai')).toHaveCount(0);

    await clearSession(page);
    await loginAsDonatur(page);
    await openNotifications(page);
    await assertNotificationVisible(page, 'Target Dana Tercapai', 'Proyek PBI21 Submit');
    await assertNotificationVisible(page, 'Update Progress Proyek', 'Proyek PBI21 Submit');
    await expect(page.getByText('Proyek Ditugaskan')).toHaveCount(0);
    await expect(page.getByText('Detail Proyek Diisi')).toHaveCount(0);
  });

  test('TC-02 user dapat membaca satu notifikasi lalu menandai semua dibaca', async ({ page }) => {
    await loginAsDonatur(page);

    await expect(page.getByLabel('Notifikasi')).toContainText('3');

    await openNotifications(page);
    await expect(page.getByText('Belum dibaca')).toHaveCount(3);

    const bodyText = await page.locator('body').innerText();
    expect(bodyText.indexOf('Proyek PBI23 Alur Ketiga')).toBeLessThan(bodyText.indexOf('Proyek PBI23 Alur Kedua'));
    expect(bodyText.indexOf('Proyek PBI23 Alur Kedua')).toBeLessThan(bodyText.indexOf('Proyek PBI23 Alur Pertama'));

    await page.getByRole('button', { name: /Proyek PBI23 Alur Ketiga/ }).click();
    await expect(page).toHaveURL(/\/proyek\/\d+$/);
    await expect(page.getByText('Proyek PBI23 Alur Ketiga')).toBeVisible();
    await expect(page.getByLabel('Notifikasi')).toContainText('2');

    await openNotifications(page);
    await expect(page.getByText('Belum dibaca')).toHaveCount(2);
    await page.getByRole('button', { name: 'Tandai semua dibaca' }).click();
    await expect(page).toHaveURL(/\/notifications$/);
    await expect(page.getByText('Belum dibaca')).toHaveCount(0);
    await expect(page.getByLabel('Notifikasi')).not.toContainText('1');
    await expect(page.getByLabel('Notifikasi')).not.toContainText('2');
    await expect(page.getByLabel('Notifikasi')).not.toContainText('3');
  });

  test('TC-03 notifikasi donatur terisolasi antar pengguna', async ({ page }) => {
    await loginAsDonatur(page);
    await openNotifications(page);
    await expect(page.getByText('Proyek Rahasia Donatur B')).toHaveCount(0);

    await clearSession(page);
    await loginAsDonaturB(page);
    await openNotifications(page);
    await expect(page.getByText('Proyek Rahasia Donatur B')).toBeVisible();
    await expect(page.getByText('Proyek PBI23 Alur Ketiga')).toHaveCount(0);
  });

  test('TC-04 akun tanpa aktivitas melihat empty state notifikasi', async ({ page }) => {
    await loginAsEmptyDonatur(page);
    await openNotifications(page);

    await expect(page.getByText('Tidak ada notifikasi')).toBeVisible();
    await expect(page.getByText('Notifikasi proyek akan muncul di sini.')).toBeVisible();
    await expect(page.getByText('Belum dibaca')).toHaveCount(0);
  });
});
