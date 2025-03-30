import { test, expect } from '@playwright/test';

// See here how to get started:
// https://playwright.dev/docs/intro
test('visits the app about url', async ({ page }) => {
  await page.goto('/about');
  await expect(page.locator('h1')).toHaveText('Job listing demo web application part of an interview process.\n');
})
