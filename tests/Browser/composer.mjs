import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
const { chromium } = await import(
    process.env.PLAYWRIGHT_MODULE || 'playwright'
);
const fixture = JSON.parse(
    await readFile(
        new URL(
            '../../storage/app/testing/composer-browser.json',
            import.meta.url,
        ),
    ),
);
const browser = await chromium.launch({
    executablePath: process.env.CHROMIUM_PATH,
    args: ['--no-sandbox'],
});
const baseURL = process.env.BROWSER_BASE_URL || 'http://test-web:8001';
const context = await browser.newContext({
    baseURL,
    viewport: { width: 1440, height: 1100 },
});
const page = await context.newPage();
const errors = [];
page.on('pageerror', (error) => {
    errors.push(error.message);
    console.error('Page error:', error.message);
});
const threadUrl = `/thread/view/${fixture.thread}/2`;
const textarea = '[name="elements[0][message]"]';
const needsUnloadWarning = (target = page) => target.evaluate(() => {
    const event = new Event('beforeunload', { cancelable: true });
    window.dispatchEvent(event);
    return event.defaultPrevented;
});
const command = async (action) => {
    const started = Date.now();
    const response = page.waitForResponse(
        (response) =>
            response.request().method() === 'POST' &&
            response.request().headers()['hx-request'] === 'true',
    );
    response.catch(() => {});
    await action();
    const result = await response;
    await page.waitForFunction(
        () => !document.getElementById('post-composer').inert,
    );
    await page.waitForFunction(() => !document.querySelector('#newpost.htmx-settling'));
    assert.ok(await page.locator('.composer-element-message textarea').evaluateAll((elements) => elements.every((element) => element.clientHeight >= element.scrollHeight)));
    console.log('Composer command:', result.status(), Date.now() - started, 'ms;', result.headers()['server-timing'] || '');
    return result;
};

try {
    await page.goto(threadUrl);
    await page
        .locator('#userbar input[name="email"]')
        .fill('composer-browser@example.test');
    await page
        .locator('#userbar input[name="password"]')
        .fill('browser-test-only');
    await Promise.all([
        page.waitForNavigation(),
        page.locator('#userbar input[type="submit"]').click(),
    ]);
    await page.goto(threadUrl);
    await page.waitForFunction(
        () =>
            window.Alpine &&
            window.htmx &&
            document.querySelector('#newpost')._x_dataStack,
    );
    if (!process.env.NO_JS_ONLY) {
        assert.equal(await needsUnloadWarning(), false);
        assert.equal(await page.locator('button[value="convert_markers"]').count(), 0);
        assert.equal(await page.locator('.composer-submit button[type="submit"]').count(), 2);
        assert.equal(await page.locator('.composer-element-message button[type="submit"], .composer-element-message select[name^="move_targets"]').count(), 0);
        assert.ok(await page.locator('.composer-element-message').evaluate((element) => Number(getComputedStyle(element).opacity) < 1));
        assert.equal(await page.locator('.composer-author-name').textContent(), 'Browser First');
        const editorBounds = await page.locator('#composer-elements').boundingBox();
        const footerBounds = await page.locator('#post-composer footer').boundingBox();
        const editorGap = await page.locator('#post-composer').evaluate((form) => parseFloat(getComputedStyle(form).columnGap));
        assert.ok(Math.abs(footerBounds.y - editorBounds.y - editorBounds.height - editorGap) < 2, 'Submit controls keep the same gap as the portrait');
        assert.ok(await page.locator('.composer-author-portrait').evaluate((image) => image.complete && image.naturalWidth > 0));
        assert.equal(await page.locator('#post-composer .bbcode-toolbar').count(), 1);
        assert.equal(await page.locator('.composer-insert').count(), 0);
        await page.locator(textarea).fill('A selected word.');
        await page.locator(textarea).evaluate((element) => {
            element.focus();
            element.setSelectionRange(2, 10);
            element.dispatchEvent(new Event('select', { bubbles: true }));
        });
        await page.getByRole('button', { name: 'Fett', exact: true }).click();
        assert.equal(await page.locator(textarea).inputValue(), 'A [b]selected[/b] word.');
        assert.equal(await page.getByRole('button', { name: 'Umfrage einfügen', exact: true }).isDisabled(), true);
        let delayed = false;
        let failNextCommand = null;
        let delayNextCommand = 0;
        let failNextAutosave = false;
        await page.route(`**/thread/${fixture.thread}/draft`, async (route) => {
            const contentType = route.request().headers()['content-type'] || '';
            const fields = contentType.includes('multipart/form-data')
                ? await new Request(route.request().url(), { method: 'POST', headers: { 'content-type': contentType }, body: route.request().postDataBuffer() }).formData()
                : new URLSearchParams(route.request().postData());
            const intent = fields.get('intent');
            if (intent === 'autosave' && failNextAutosave) {
                failNextAutosave = false;
                await route.fulfill({ status: 500, contentType: 'application/json', body: JSON.stringify({ message: 'Test: Speichern fehlgeschlagen.' }) });
                return;
            }
            if (route.request().headers()['hx-request'] === 'true') {
                await new Promise((resolve) => setTimeout(resolve, delayNextCommand || 150));
                delayNextCommand = 0;
                if (failNextCommand?.intent === intent) {
                    const failure = failNextCommand;
                    failNextCommand = null;
                    if (failure.status === 'abort') await route.abort();
                    else await route.fulfill({
                        status: failure.status, contentType: 'text/html',
                        headers: failure.status === 422 ? { 'HX-Retarget': '#composer-feedback', 'HX-Reswap': 'innerHTML' } : {},
                        body: '<p role="alert">Test: Änderung abgelehnt.</p>',
                    });
                    return;
                }
            }
            if (!delayed && intent === 'autosave') {
                delayed = true;
                await new Promise((resolve) => setTimeout(resolve, 800));
            }
            if (route.request().postData()?.includes('insert')) {
                await new Promise((resolve) => setTimeout(resolve, 350));
            }
            await route.continue();
        });
        await page.locator(textarea).fill('Draft before autosave');
        assert.equal(await needsUnloadWarning(), true);
        const unloadDialog = page.waitForEvent('dialog');
        await page.evaluate(() => { setTimeout(() => window.location.reload(), 0); });
        const dialog = await unloadDialog;
        assert.equal(dialog.type(), 'beforeunload');
        await dialog.dismiss();
        assert.equal(await page.locator(textarea).inputValue(), 'Draft before autosave');
        delayed = false;
        await page.locator(textarea).fill('Draft before autosave');
        await page.waitForFunction(() =>
            document
                .querySelector('#draft-save-status')
                .textContent.includes('Wird gespeichert'),
        );
        const pendingVersion = await page.locator('[name="version"]').inputValue();
        await page.locator(textarea).fill('Text edited during autosave');
        await page.waitForFunction((version) => document.querySelector('[name="version"]').value !== version, pendingVersion);
        assert.equal(await page.locator('#draft-save-status').textContent(), 'Ungespeicherte Änderungen');
        assert.equal(await needsUnloadWarning(), true);
        const navigations = [];
        page.on('framenavigated', (frame) => {
            if (frame === page.mainFrame()) navigations.push(frame.url());
        });
        assert.equal(await page.locator('button[value^="insert:add_message:"]').count(), 0);
        delayNextCommand = 600;
        await command(async () => {
            await page.getByRole('button', { name: 'Übergabe einfügen', exact: true }).click();
            await page.locator('.composer-loading').waitFor();
            assert.equal(await page.locator('.composer-insertion-preview .composer-loading').textContent(), 'Übergabe wird eingefügt ...');
            assert.equal(await page.locator('.composer-pending-text').first().textContent(), 'Text edited during autosave');
            assert.equal(await page.locator('#post-composer').evaluate((form) => form.inert), true);
            await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-pending-insertion.png' });
        });
        await page.waitForSelector('.composer-transfer-source');
        assert.equal(await needsUnloadWarning(), false, 'Confirmed fragment saves remove the dirty warning and old listeners');
        assert.equal(await page.locator('.composer-element-transfer [data-direction]').count(), 2);
        assert.equal(await page.locator('.composer-element-transfer [data-direction="down"]').isVisible(), true);
        assert.equal(await page.locator('.composer-element-transfer [data-direction="down"]').isDisabled(), true);
        assert.equal(await page.locator('.composer-element-transfer [data-direction="down"] .option').evaluate((icon) => getComputedStyle(icon).backgroundColor), 'rgb(226, 226, 226)');
        assert.match(await page.locator('.composer-element-transfer .move-up').evaluate((icon) => getComputedStyle(icon).maskImage), /move_up\.svg/);
        assert.match(await page.locator('.composer-element-transfer .move-down').evaluate((icon) => getComputedStyle(icon).maskImage), /move_down\.svg/);
        assert.equal(await page.getByText('Bausteine wurden eingefügt.', { exact: false }).count(), 0);
        assert.equal(await page.locator('.composer-element-transfer[data-inserted]').count(), 1);
        assert.equal(await page.locator('.composer-element-transfer').evaluate((element) => getComputedStyle(element).animationName), 'composer-inserted');
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-inserted-halo.png' });
        await page.waitForFunction(() => getComputedStyle(document.querySelector('.composer-element-transfer')).boxShadow === 'none');
        assert.equal(await page.locator('.composer-remove-label input').count(), 0);
        assert.equal(await page.locator('.composer-remove').getAttribute('aria-label'), 'Baustein entfernen');
        assert.equal(
            await page.locator(textarea).inputValue(),
            'Text edited during autosave',
        );
        assert.equal(navigations.length, 0);
        assert.ok(page.url().endsWith(threadUrl));
        assert.equal(await page.locator('.composer-element-message').count(), 2);
        await page
            .locator('[name="elements[2][message]"]')
            .fill('Second text block');
        failNextCommand = { intent: 'move:1:2', status: 422 };
        delayNextCommand = 600;
        const failedMove = await command(async () => {
            await page.locator('button[value="move:1:2"]').click();
            await page.waitForFunction(() => document.querySelector('.composer-element-transfer').style.order === '3');
            const moved = await page.locator('.composer-element-transfer').boundingBox();
            const textAfter = await page.locator('[data-index="2"]').boundingBox();
            assert.ok(moved.y > textAfter.y, 'Block moves visually before server confirmation');
        });
        assert.equal(failedMove.status(), 422);
        assert.equal(await page.locator('.composer-element-transfer').evaluate((element) => element.style.order), '');
        assert.equal(await page.locator('[name="elements[2][message]"]').inputValue(), 'Second text block');
        await command(() => page.locator('button[value="move:1:2"]').click());
        assert.equal(
            await page.locator(textarea).inputValue(),
            'Text edited during autosave\n\nSecond text block',
        );
        const quote = await page
            .locator('button[value^="quote:"]')
            .first()
            .getAttribute('value');
        await command(() => page.locator(`button[value="${quote}"]`).click());
        assert.match(
            await page.locator('[name="elements[2][message]"]').inputValue(),
            /\[q=/,
        );
        await command(() => page.locator(`label[for="char-${fixture.characters[1]}"]`).click());
        assert.equal(await page.locator('.composer-author-name').textContent(), 'Browser Second');
        assert.equal(await page.locator('.composer-transfer-source[value="character"]').locator('..').isVisible(), false);
        assert.match(await page.locator(textarea).inputValue(), /Second text block/);
        await command(() => page.locator(`label[for="char-${fixture.characters[0]}"]`).click());
        assert.equal(await page.locator('.composer-author-name').textContent(), 'Browser First');
        const sourceAvatar = page.locator('.composer-transfer-source[value="character"]').locator('..').locator('img');
        assert.equal((await sourceAvatar.boundingBox()).width, 32);
        assert.ok(await page.locator('.composer-inventory:not([hidden]) > legend').evaluate((legend) => getComputedStyle(legend).clipPath !== 'none'));
        const pickerTop = (await page.locator('.composer-character-picker').boundingBox()).y;
        await command(() => page.locator('label[for="char-new"]').click());
        assert.equal(await page.locator('#composer-new-character-name').isVisible(), true);
        assert.ok(Math.abs((await page.locator('.composer-character-picker').boundingBox()).y - pickerTop) < 2, 'New-character input keeps the picker position stable');
        await command(() => page.locator(`label[for="char-${fixture.characters[0]}"]`).click());
        const search = page.locator('.entity-picker-search');
        await search.fill('Recipient 3');
        assert.equal(await page.locator('.entity-picker-option:visible .composer-transfer-recipient').count(), 1);
        await page.locator('.entity-picker-option:visible .composer-transfer-recipient').locator('..').click();
        await search.fill('nothing matches this');
        assert.equal(await page.locator('.composer-transfer-recipient:checked').count(), 1);
        await search.fill('');
        await page.locator(`.composer-transfer-source[value="location:${fixture.location}"]`).locator('..').click();
        assert.equal(
            await page.locator('.composer-inventory:not([hidden])').count(),
            1,
        );
        assert.equal(
            await page
                .locator('.composer-inventory:not([hidden])')
                .getAttribute('data-source'),
            'location',
        );
        assert.equal(await page.locator('.entity-picker-option:visible .composer-transfer-recipient').count(), 1);
        assert.equal(await page.locator('.composer-transfer-recipient:checked').inputValue(), 'character');
        await page.locator('.composer-transfer-source[value="character"]').locator('..').click();
        await page.locator(`.composer-transfer-recipient[value="location:${fixture.location}"]`).locator('..').click();
        const selectedItem = page.locator('.composer-inventory:not([hidden]) input[type="checkbox"]').first();
        await selectedItem.locator('..').click();
        await page.locator(textarea).focus();
        await page.waitForFunction(() => !document.querySelector('.composer-element-transfer details').open);
        await command(() => page.locator('.composer-submit button[value="save"]').click());
        assert.equal(await page.locator('.composer-element-transfer details').getAttribute('open'), null);
        assert.equal(await page.locator('.composer-transfer-preview').isVisible(), true);
        assert.equal(await page.locator('.composer-element-transfer > header > strong').isVisible(), false);
        const editCrest = page.locator('.composer-element-transfer summary');
        assert.equal(await editCrest.isVisible(), true);
        assert.match(await editCrest.locator('.edit').evaluate((icon) => getComputedStyle(icon).maskImage), /edit\.svg/);
        const editBounds = await editCrest.boundingBox();
        const upBounds = await page.locator('.composer-element-transfer [data-direction="up"]').boundingBox();
        assert.ok(Math.abs(editBounds.y - upBounds.y) < 2 && editBounds.x + editBounds.width <= upBounds.x, 'Edit crest sits beside the movement crests');
        assert.ok(upBounds.x - editBounds.x - editBounds.width <= 2, 'Crest controls use a compact gap');
        assert.ok(editBounds.y - (await page.locator('.composer-element-transfer').boundingBox()).y < 7, 'Crest controls sit near the top edge');
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-preview-desktop.png' });
        await page.setViewportSize({ width: 390, height: 844 });
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-preview-mobile.png' });
        assert.ok(await page.locator('#composer-elements').evaluate((element) => element.scrollWidth <= element.clientWidth));
        await page.setViewportSize({ width: 1440, height: 1100 });
        await page.locator('.composer-element-transfer summary').click();
        assert.equal(await page.locator('.composer-element-transfer > header > strong').isVisible(), true);
        assert.ok(Math.abs((await page.locator('.composer-element-transfer summary').boundingBox()).y - (await page.locator('.composer-element-transfer [data-direction="up"]').boundingBox()).y) < 2, 'Expanded blocks keep the crest row aligned');
        // Some browsers do not focus radios on mouse clicks, leaving focus on the document.
        await page.locator('.composer-transfer-recipient[value^="character:"]:not(:disabled)').first().evaluate((radio) => {
            document.activeElement.blur();
            radio.click();
        });
        await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve))));
        assert.equal(await page.locator('.composer-element-transfer details').getAttribute('open'), '', 'A radio edit must not collapse the reopened transfer');
        const transferQuantity = page.locator('.composer-inventory:not([hidden]) .inventory-item-quantity');
        await transferQuantity.fill('1tn');
        const editedVersion = await page.locator('[name="version"]').inputValue();
        await page.waitForFunction((version) => document.querySelector('[name="version"]').value !== version, editedVersion);
        assert.equal(await page.locator('.composer-element-transfer details').getAttribute('open'), '', 'Quantity editing and autosave keep the editor open');
        assert.equal(await transferQuantity.inputValue(), '1tn');
        await page.locator(textarea).focus();
        await page.waitForFunction(() => !document.querySelector('.composer-element-transfer details').open);
        await page.locator('.composer-element-transfer summary').click();
        await page.locator('.composer-inventory:not([hidden]) input[type="checkbox"]').first().locator('..').click();
        const invalid = await command(() =>
            page.locator('button[value="publish"]').click(),
        );
        assert.equal(invalid.status(), 422);
        await page
            .locator('#composer-feedback')
            .getByText(/mindestens einen Gegenstand/)
            .waitFor();
        assert.equal(
            await page.locator(textarea).inputValue(),
            'Text edited during autosave\n\nSecond text block',
        );
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-editor-desktop.png' });
        await page.setViewportSize({ width: 390, height: 844 });
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-editor-mobile.png' });
        assert.ok(await page.locator('#composer-elements').evaluate((element) => element.scrollWidth <= element.clientWidth));
        await page.setViewportSize({ width: 1440, height: 1100 });
        failNextCommand = { intent: 'remove:1', status: 500 };
        delayNextCommand = 600;
        const failedRemoval = await command(async () => {
            await page.locator('button[value="remove:1"]').click();
            await page.waitForFunction(() => document.querySelector('.composer-element-transfer').style.display === 'none');
        });
        assert.equal(failedRemoval.status(), 500);
        assert.equal(await page.locator('.composer-element-transfer').isVisible(), true);
        assert.match(await page.locator('#draft-save-status').textContent(), /nicht bestätigt/);
        assert.equal(await page.locator('.composer-command-error').isVisible(), true);
        failNextCommand = { intent: 'remove:1', status: 'abort' };
        await page.locator('button[value="remove:1"]').click();
        await page.waitForFunction(() => !document.querySelector('#post-composer').inert);
        assert.equal(await page.locator('.composer-element-transfer').isVisible(), true);
        assert.match(await page.locator('#draft-save-status').textContent(), /fehlgeschlagen/);
        await command(() => page.locator('button[value="remove:1"]').click());
        assert.equal(await page.locator('.composer-element-message').count(), 1);
        assert.match(await page.locator(textarea).inputValue(), /Second text block[\s\S]*\[q=/);
        const beforeFailedInsertion = await page.locator(textarea).inputValue();
        failNextCommand = { intent: 'save', status: 500 };
        delayNextCommand = 600;
        const failedInsertion = await command(async () => {
            await page.getByRole('button', { name: 'Übergabe einfügen', exact: true }).click();
            await page.locator('.composer-insertion-preview').waitFor();
        });
        assert.equal(failedInsertion.status(), 500);
        assert.equal(await page.locator(textarea).isVisible(), true);
        assert.equal((await page.locator(textarea).inputValue()).replace('[transfer /]', ''), beforeFailedInsertion);
        assert.equal(await page.locator('.composer-insertion-preview').count(), 0);
        await page.locator(textarea).fill('Grüße vor 🙂 danach');
        await page.locator(textarea).evaluate((element) => {
            element.focus();
            element.setSelectionRange('Grüße vor 🙂'.length, 'Grüße vor 🙂'.length);
            element.dispatchEvent(new Event('select', { bubbles: true }));
        });
        await command(() => page.getByRole('button', { name: 'Übergabe einfügen', exact: true }).click());
        assert.equal(await page.locator(textarea).inputValue(), 'Grüße vor 🙂');
        assert.equal(await page.locator('[name="elements[2][message]"]').inputValue(), ' danach');
        await page.waitForFunction(() => document.activeElement?.closest('.composer-element-transfer'));
        await command(() => page.locator('button[value="remove:1"]').click());
        await page.locator(textarea).focus();
        await command(() => page.getByRole('button', { name: 'Übergabe einfügen', exact: true }).click());
        await page.locator(textarea).focus();
        await command(() => page.getByRole('button', { name: 'Szene einfügen', exact: true }).click());
        assert.equal(await page.locator('.composer-element-scene_transition input[name$="[scene_action]"]:checked').inputValue(), 'start');
        assert.equal(await page.locator('.composer-element-scene_transition .composer-remove').isDisabled(), true);
        assert.equal(await page.locator('.composer-element-scene_transition .composer-remove .option').evaluate((icon) => getComputedStyle(icon).backgroundColor), 'rgb(226, 226, 226)');
        assert.match(await page.locator('.composer-element-scene_transition .composer-remove .option').evaluate((icon) => getComputedStyle(icon).maskImage), /delete\.svg/);
        const locationResponse = page.waitForResponse((response) => response.url().includes('/draft/locations?'));
        await page.locator('.composer-location-search input').fill('Composer Browser Test');
        assert.equal((await locationResponse).status(), 200);
        const locationChoice = page.locator(`input.composer-scene-place[value="${fixture.location}"]`);
        await locationChoice.waitFor({ state: 'attached' });
        await command(() => locationChoice.locator('..').click());
        assert.equal(await page.locator('.composer-element-scene_transition details').getAttribute('open'), null);
        assert.equal(await page.getByRole('button', { name: 'Szenenkontext übernehmen', exact: true }).count(), 0);
        await command(() => page.locator('.composer-element-transfer .composer-remove').click());
        await command(() => page.locator('.composer-element-scene_transition .composer-remove').click());
        await page.locator(textarea).focus();
        await command(() => page.getByRole('button', { name: 'Szene einfügen', exact: true }).click());
        assert.equal(await page.locator('.composer-element-scene_transition input[name$="[scene_action]"]:checked').inputValue(), 'end');
        assert.notEqual(await page.locator('.composer-element-scene_transition input[type="datetime-local"]').inputValue(), '');
        await command(() => page.locator('.composer-submit button[value="save"]').click());
        assert.equal(await page.locator('.composer-element-scene_transition details').getAttribute('open'), null);
        await page.locator('[name="elements[2][message]"]').focus();
        assert.equal(await page.getByRole('button', { name: 'Übergabe einfügen', exact: true }).isDisabled(), true);
        assert.equal(await page.getByRole('button', { name: 'Umfrage einfügen', exact: true }).isDisabled(), false);
        await command(() => page.getByRole('button', { name: 'Umfrage einfügen', exact: true }).click());
        await page.locator('.composer-element-poll input[name$="[question]"]').fill('Which path?');
        await page.locator('.composer-element-poll input[name$="[options][0]"]').fill('Forest');
        await page.locator('.composer-element-poll input[name$="[options][1]"]').fill('River');
        for (const viewport of [{ width: 1440, height: 1100 }, { width: 390, height: 844 }]) {
            await page.setViewportSize(viewport);
            await page.locator('.composer-element-poll').screenshot({ path: `/artifacts/composer-poll-editor-${viewport.width}.png` });
            assert.ok(await page.locator('.composer-element-poll').evaluate((element) => element.scrollWidth <= element.clientWidth), 'Poll fields fit their block');
        }
        await page.setViewportSize({ width: 1440, height: 1100 });
        await command(() => page.locator('.composer-submit button[value="save"]').click());
        assert.equal(await page.locator('.composer-element-poll details').getAttribute('open'), null);
        await page.locator('.composer-element-message').nth(1).locator('textarea').focus();
        assert.equal(await page.getByRole('button', { name: 'Szene einfügen', exact: true }).isDisabled(), true);
        assert.equal(await page.locator('.composer-block-preview input, .composer-block-preview form').count(), 0);
        await page.locator('#composer-elements').screenshot({ path: '/artifacts/composer-poll-scene.png' });
        await command(() => page.locator('.composer-element-poll .composer-remove').click());
        await command(() => page.locator('.composer-element-scene_transition .composer-remove').click());
        const longText = Array.from({ length: 35 }, (_, i) => `Paragraph ${i + 1}: A longer piece of writing.`).join('\n\n');
        await page.locator(textarea).fill(longText);
        await page.locator(textarea).evaluate((element) => element.setSelectionRange(20, 20));
        const autosaveVersion = await page.locator('[name="version"]').inputValue();
        await page.waitForFunction((version) => document.querySelector('[name="version"]').value !== version, autosaveVersion);
        assert.equal(await page.locator(textarea).evaluate((element) => element.selectionStart), 20);
        assert.ok(await page.locator(textarea).evaluate((element) => element.clientHeight >= element.scrollHeight));
        assert.equal(await page.locator(textarea).evaluate((element) => getComputedStyle(element).borderTopWidth), '0px');
        assert.equal(await page.locator(textarea).evaluate((element) => getComputedStyle(element).outlineStyle), 'none');
        assert.equal(await page.locator(textarea).evaluate((element) => getComputedStyle(element).fieldSizing), 'content');
        for (const viewport of [{ width: 1440, height: 1100 }, { width: 390, height: 844 }]) {
            await page.setViewportSize(viewport);
            await page.locator(textarea).evaluate((element) => window.scrollTo(0, element.getBoundingClientRect().top + window.scrollY + 150));
            await page.waitForTimeout(150);
            const toolbar = await page.locator('.composer-toolbar').boundingBox();
            const header = await page.locator('body > header').boundingBox();
            assert.ok(Math.abs(toolbar.y - (header.y + header.height)) < 2, 'Toolbar stays below the main navigation');
            assert.ok(toolbar.x >= 0 && toolbar.x + toolbar.width <= viewport.width, 'Toolbar fits the viewport');
            const portrait = await page.locator('.composer-author-portrait').boundingBox();
            const gap = await page.locator('#post-composer').evaluate((form) => parseFloat(getComputedStyle(form).columnGap));
            assert.ok(Math.abs(portrait.y - (header.y + header.height + gap)) < 2, 'Author portrait keeps the composer gap below the main navigation');
            assert.equal(await page.locator('.composer-toolbar').evaluate((element) => element.classList.contains('is-stuck')), true);
            await page.screenshot({ path: `/artifacts/composer-sticky-${viewport.width}.png` });
        }
        await page.setViewportSize({ width: 1440, height: 1100 });
        failNextAutosave = true;
        await page.locator(textarea).fill('A quiet paragraph editor.\n\nAnother paragraph, without a separate block heading or controls.');
        await page.locator(textarea).blur();
        await page.locator('#draft-save-status').getByText('Speichern fehlgeschlagen.').waitFor();
        assert.equal(await needsUnloadWarning(), true, 'Failed saves keep the navigation warning active');
        await command(() => page.locator('.composer-submit button[value="save"]').click());
        assert.equal(await needsUnloadWarning(), false);
        assert.equal(await page.locator('.composer-loading').count(), 0);
        await page.screenshot({
            path: '/artifacts/composer-desktop.png',
            fullPage: true,
        });
        await page.setViewportSize({ width: 390, height: 844 });
        await page.screenshot({
            path: '/artifacts/composer-mobile.png',
            fullPage: true,
        });
        const other = await context.newPage();
        await other.goto(threadUrl);
        await other.waitForFunction(
            () => document.querySelector('#newpost')._x_dataStack,
        );
        const staleVersion = await other
            .locator('[name="version"]')
            .inputValue();
        const priorVersion = await page
            .locator('[name="version"]')
            .inputValue();
        await page.locator(textarea).fill('Newer device text');
        await page.waitForFunction(
            (v) => document.querySelector('[name="version"]').value !== v,
            priorVersion,
        );
        await other.locator(textarea).fill('Stale local text must survive');
        await other
            .locator('#draft-save-status')
            .getByText(/nicht überschrieben/)
            .waitFor();
        assert.equal(
            await other.locator('[name="version"]').inputValue(),
            staleVersion,
        );
        assert.equal(
            await other.locator(textarea).inputValue(),
            'Stale local text must survive',
        );
        assert.equal(await needsUnloadWarning(other), true, 'Conflicting local edits remain protected');
        await other.close();
        assert.deepEqual(errors, []);
        await Promise.all([
            page.waitForNavigation(),
            page.locator('button[value="publish"]').click(),
        ]);
        assert.equal(await page.locator('.postcontent').filter({ hasText: 'Newer device text' }).count(), 1);
        await page.goto(threadUrl);
        await page.waitForFunction(() => document.querySelector('#newpost')._x_dataStack);
    }
    const plainContext = await browser.newContext({
        baseURL,
        javaScriptEnabled: false,
        storageState: await context.storageState(),
    });
    const plain = await plainContext.newPage();
    await plain.goto(threadUrl);
    assert.equal(await plain.getByText('Smileys anzeigen').count(), 0);
    assert.equal(await plain.locator('button[value^="insert:add_message:"]').count(), 0);
    assert.equal(await plain.locator('.composer-insert').count(), 0);
    assert.equal(await plain.locator('button[value^="move:"]').count(), 0);
    assert.equal(await plain.locator('button[value="convert_markers"]').count(), 0);
    assert.equal(await plain.locator('button[value^="move_selected:"]').count(), 0);
    await plain.locator(textarea).fill('A post composed without JavaScript[action/]');
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('button[value="publish"]').click(),
    ]);
    assert.equal(await plain.getByText('Bausteine wurden eingefügt.', { exact: false }).count(), 0);
    assert.equal(await plain.locator('.composer-element-transfer[data-inserted]').count(), 1);
    assert.equal(await plain.locator('.composer-element-transfer').count(), 1);
    assert.equal(await plain.locator('.entity-picker-search').count(), 0);
    await plain.locator(`.composer-transfer-source[value="location:${fixture.location}"]`).locator('..').click();
    await Promise.all([
        plain.waitForNavigation(),
        plain.getByRole('button', { name: 'Quelle übernehmen' }).click(),
    ]);
    assert.equal(await plain.locator('.composer-inventory:not([hidden])').getAttribute('data-source'), 'location');
    assert.equal(await plain.locator('.entity-picker-option:not([hidden]) .composer-transfer-recipient').count(), 1);
    await plain.locator('.composer-transfer-source[value="character"]').locator('..').click();
    await Promise.all([
        plain.waitForNavigation(),
        plain.getByRole('button', { name: 'Quelle übernehmen' }).click(),
    ]);
    assert.equal(await plain.locator('.composer-remove').count(), 0);
    await plain.locator('[name="elements[2][message]"]').fill('Another message');
    await plain.locator('[name="move_targets[1]"]').selectOption({ value: '0' });
    assert.equal(await plain.locator('[name="elements[2][message]"]').inputValue(), 'Another message');
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('.composer-submit button[value="save"]').click(),
    ]);
    assert.equal(await plain.locator('[name="elements[2][message]"]').inputValue(), 'A post composed without JavaScript\n\nAnother message');
    await plain.locator('[name="move_targets[1]"]').selectOption({ value: '2' });
    await plain.locator('[name="remove_elements[1]"]').check();
    assert.equal(await plain.locator('.composer-element-transfer .composer-element-content').isVisible(), false);
    assert.equal(await plain.locator('.composer-element-transfer header').isVisible(), true);
    await plain.locator('[name="remove_elements[1]"]').uncheck();
    assert.equal(await plain.locator('.composer-element-transfer .composer-element-content').isVisible(), true);
    await plain.locator('[name="remove_elements[1]"]').check();
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('.composer-submit button[value="save"]').click(),
    ]);
    assert.equal(await plain.locator('.composer-element-message').count(), 1);
    assert.equal(await plain.locator('.composer-element-message button[type="submit"], .composer-element-message select').count(), 0);
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('.composer-submit button[value="save"]').click(),
    ]);
    assert.equal(
        await plain.locator(textarea).inputValue(),
        'A post composed without JavaScript\n\nAnother message',
    );
    assert.ok(plain.url().includes(threadUrl));
    await plain.locator(textarea).fill('A post composed without JavaScript\n\nAnother message[scene /][poll /]');
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('.composer-submit button[value="save"]').click(),
    ]);
    assert.equal(await plain.locator('.composer-element-scene_transition details').getAttribute('open'), null);
    assert.equal(await plain.locator('.composer-element-poll details').getAttribute('open'), '');
    await plain.locator('.composer-element-poll input[name$="[question]"]').fill('Where do we go next?');
    await plain.locator('.composer-element-poll input[name$="[options][0]"]').fill('North');
    await plain.locator('.composer-element-poll input[name$="[options][1]"]').fill('South');
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('.composer-submit button[value="save"]').click(),
    ]);
    assert.equal(await plain.locator('.composer-element-poll details').getAttribute('open'), null);
    assert.equal(await plain.locator('.composer-element-poll > header > strong').isVisible(), false);
    assert.equal(await plain.locator('.composer-block-preview input, .composer-block-preview form').count(), 0);
    assert.equal(await plain.locator('.composer-element-poll .composer-block-preview').isVisible(), true);
    await plain.locator('.composer-element-poll summary').click();
    assert.equal(await plain.locator('.composer-element-poll input[name$="[question]"]').inputValue(), 'Where do we go next?');
    await plain.locator('.composer-element-poll summary').click();
    await plain.screenshot({ path: '/artifacts/composer-nojs.png', fullPage: true });
    await plain.evaluate(() => document.querySelectorAll('link[rel="stylesheet"], style').forEach((element) => element.remove()));
    assert.equal(await plain.locator('.composer-insert').count(), 0);
    assert.equal(await plain.locator('.composer-submit button[value="save"]').isVisible(), true);
    await plain.screenshot({ path: '/artifacts/composer-unstyled.png', fullPage: true });
    await Promise.all([
        plain.waitForNavigation(),
        plain.locator('button[value="publish"]').click(),
    ]);
    await plain
        .locator('.postcontent')
        .filter({ hasText: 'A post composed without JavaScript' })
        .waitFor();
    await plainContext.close();
    assert.deepEqual(errors, []);
    console.log(
        'Browser checks passed: fragment commands, pending autosave, move, quote, sources, visible errors, cross-tab conflict, desktop/mobile rendering.',
    );
} catch (error) {
    console.error('Browser errors:', errors);
    console.error(
        'Composer status:',
        await page
            .locator('#draft-save-status')
            .textContent()
            .catch(() => 'unavailable'),
    );
    await page.screenshot({
        path: '/artifacts/composer-failure.png',
        fullPage: true,
    });
    throw error;
} finally {
    await browser.close();
}
