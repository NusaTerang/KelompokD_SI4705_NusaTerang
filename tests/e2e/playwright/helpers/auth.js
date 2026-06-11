import { expect } from '@playwright/test';

export async function login(page, email, expectedPathPattern = null) {
  await page.goto('/login');
  await page.getByLabel('Email').fill(email);
  await page.getByLabel('Password').fill('password');
  await page.getByRole('button', { name: 'Masuk' }).click();

  if (expectedPathPattern) {
    await expect(page).toHaveURL(expectedPathPattern);
  }
}

export async function loginAsVendor(page) {
  await login(page, 'vendor.e2e@nusa.test', /\/vendor\/dashboard/);
}

export async function loginAsOtherVendor(page) {
  await login(page, 'vendor-lain.e2e@nusa.test', /\/vendor\/dashboard/);
}

export async function loginAsAdmin(page) {
  await login(page, 'admin.e2e@nusa.test', /\/admin\/dashboard/);
}

export async function loginAsDonatur(page) {
  await login(page, 'donatur.e2e@nusa.test');
}

export async function loginAsDonaturB(page) {
  await login(page, 'donatur-b.e2e@nusa.test');
}

export async function loginAsEmptyDonatur(page) {
  await login(page, 'empty.e2e@nusa.test');
}
