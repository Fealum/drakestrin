# Drakestrin

## Testing

Feature tests use a separate MySQL service (`db-test`), account, and volume. There is no published port or access to the development database. The initial database is built from `devop/database/usr_l3s658_1.sql` and the application migrations, because the migrations extend the imported legacy schema.

Prepare it once, and again after adding migrations:

```bash
sh devop/prepare-tests.sh
```

Then run tests from the application container:

```bash
cd devop
docker compose exec -T app php artisan test
```

PHPUnit forces the dedicated test credentials. The test bootstrap rejects cached Laravel configuration and any database host, name, or account other than the dedicated test connection. Run `php artisan config:clear` in the app container before tests if configuration was cached. Test mail uses the array mailer.

The browser regression creates a disposable user/thread in the test database and runs Chromium with and without JavaScript:

```bash
sh devop/run-browser-tests.sh
```

It installs Playwright and Chromium inside a temporary Docker container, checks autosave/commands/quoting/source selection/errors/conflicts and ordinary form publication, and writes desktop/mobile screenshots under `storage/app/testing`. The script removes its database fixture and stops the test web server afterwards. Avoid running this browser script concurrently with the feature suite, which changes global permission defaults in some tests.

## Composer And Post Elements

The composer uses ordinary POST forms with Alpine and HTMX enhancement. HTMX commands return composer HTML, validation failures return an error fragment, and successful publication redirects to the published post. Autosave waits for the previous save; explicit commands wait for autosave. Cross-device conflicts preserve the local form and require opening the newer draft before further writes.

With JavaScript, navigating away warns while edits or commands remain unconfirmed, including after failed saves or cross-device conflicts. Confirmed publication and ordinary form submission do not trigger this warning. A response for an older autosave revision never marks newer edits as saved; they retain the unsaved status until their own save succeeds.

Messages accept the optional placeholders `[transfer /]`, `[poll /]`, and `[scene /]` (the space is optional; `[action /]` remains an alias). The **speichern** button applies pending changes and replaces placeholders across all messages with configurable draft elements. Publishing also applies pending changes, but if it expands placeholders it returns to the composer instead of publishing. Newly inserted blocks receive a yellow halo that fades over 2.7 seconds, without an insertion notice. This also works without JavaScript; reduced-motion preferences replace the fade with a stationary timed highlight. Fill in and review the new elements before publishing again. Scene placeholders begin a scene outside one and normally end it inside one; before a following transfer they instead change the scene and deliberately bind following draft transfers to the new context. Existing transfers still cannot be moved across scene boundaries. Conversion respects permissions and scene boundaries, leaves quoted/code examples literal, and rejects splitting inside BBCode formatting. Errors save the original text without partially converting it. JavaScript autosave never converts placeholders or applies staged removals or positions. Adjacent text sections merge on save when their smiley settings match, without redrawing the editor during autosave.

The composer displays neutral text slots before, between, and after non-message elements. Empty slots fade with CSS and are never persisted as draft or published content. Text sections have no move or removal controls; clear their contents to remove them. Textareas use native CSS `field-sizing: content`, with manual resizing available as a fallback. Without JavaScript, placeholders replace insertion buttons, while non-message blocks offer position selectors and removal checkboxes. Checked blocks collapse visually and can be restored by unchecking them before saving. Saving or publishing applies all staged edits together and validates the final scene layout; position ties resolve deterministically in original order, with moved blocks placed before or after their destination according to movement direction. JavaScript offers one shared formatting toolbar that stays below the navigation while scrolling, inserts blocks at the active text cursor through the same server-side marker conversion, and retains valid-destination arrows and immediate removal icons. New scene blocks default to the current date and time; deliberately cleared or previously unset dates stay empty and must be filled before publication.

Configured non-message blocks collapse to compact previews after an explicit save; completed transfers also collapse when focus leaves their block with JavaScript. Native details controls reopen their fields with or without JavaScript. Incomplete blocks and blocks returned with validation errors remain expanded. Preview readiness is only a display hint: publication still validates permissions, scene state, poll settings, and stock independently. Poll previews contain no voting controls, and transfer previews use only inventory already visible to the composer. Autosave never replaces the open editor or its selection, but refreshes server-calculated movement and insertion options. Empty text slots do not count as movement destinations.

Scene controls offer only context-valid start/end/change choices. Location radios use searchable results in pages of twenty, retaining any selection outside the current results; ordinary search and paging buttons work without JavaScript. JavaScript fetches just the results and immediately applies scene-context changes. Story-time inputs and displayed timestamps both use the configured application timezone (`Europe/Berlin`). The author portrait stays beside the editor while scrolling, and the sticky toolbar softly fades the content below it.

JavaScript commands keep the form locked while its response may replace it. Insertion shows a temporary text split and loading placeholder at the cursor; removal hides the block immediately; movement applies the server-provided destination through CSS order without reordering indexed form fields. All visual changes are restored after rejection, server errors, or network failure. Hidden pending fields remain in the submitted form. Ordinary HTML submissions remain authoritative without JavaScript.

Composer POST requests do not prepare account-bar/sidebar queries or run subscription-mail catch-up. Online activity is still updated, publication still invokes its normal notification handler, and ordinary page visits retain globally throttled mail catch-up for installations without cron. Permission fetching uses its existing invalidated caches and eager-loads permit definitions on cache misses. Post indexes cover character/thread history and thread post cursors; stock and scene checks are never cached across requests.

Run `sh devop/profile-composer.sh` for an isolated, repeatable composer profile against the testing database. It creates and removes its own browser fixture, reports SQL counts, middleware timings and slow queries for cold/warm text saves and action insertion. This is an in-process server diagnostic, not a browser/network benchmark. Do not run it concurrently with other tests or browser fixtures.

Action sources and recipients use the reusable `entity-picker` Blade component: native radio choices with portraits or icons, plus local JavaScript filtering when there are more than six eligible recipients. Filtering never changes eligibility or clears a selected recipient. JavaScript source changes immediately switch the preloaded inventory; character changes refresh the composer while preserving message text. Without JavaScript, use the source/character confirmation buttons or save the draft to refresh its context. The author header shows the selected portrait and name alongside the character choices and creation tile; on small screens the editor uses the full available width.

Run the read-only migration verifier before and after the verification deployment:

```bash
docker compose -f devop/docker-compose.yml exec -T app php artisan forum:verify-post-elements
# Machine-readable output; returns a non-zero status if discrepancies exist:
docker compose -f devop/docker-compose.yml exec -T app php artisan forum:verify-post-elements --json
```

It checks message bodies and smileys, element subtypes, transfer links, scene boundaries, poll settings/options/votes/participation, and preserved character signatures. It also reports representative record IDs for discrepancies. Keep legacy message, scene-anchor, poll and signature storage through one verification deployment. Resolve reported discrepancies before a separate cleanup migration; do not drop legacy storage as part of ordinary composer changes.

## Local email

The development Docker Compose stack captures all SMTP mail with Mailpit. Its inbox is available at http://localhost:8025; SMTP remains internal at `mailpit:1025` and does not deliver messages externally.

## Deployment

Deployer runs inside the application container, which has the deployment SSH key mounted as a Docker secret. The recipe in `deploy.php` deploys `master` to `zeus06.de:2222` under `~/html/drakestrin`.

Set `SSH_KEY` in `devop/.env` to the absolute path of the private key authorized by the deployment host, for example `SSH_KEY=/home/username/.ssh/hosting_key`. The placeholder file cannot authenticate. Passphrase-protected keys must also be loaded into the host SSH agent with `ssh-add`; Compose forwards the agent into the application container.

Before deploying, commit and push everything Deployer should publish. It fetches the repository from GitHub and cannot see uncommitted local files.

```bash
git status
git push origin master
docker compose -f devop/docker-compose.yml up -d app
docker exec -it drakestrin_app vendor/bin/dep deploy --branch=master zeus06.de
```

The deployment creates a release, installs production Composer dependencies, links the shared `.env`, `storage`, and `public/img`, caches Laravel configuration, routes, views, and events, runs `php artisan migrate --force`, and atomically switches the `current` symlink. Three releases are retained.

The web server's document root must point to `~/html/drakestrin/current/public`. On the first deployment, configure `~/html/drakestrin/shared/.env` with the production application, database, and SMTP settings. In particular, use `APP_ENV=production`, `APP_DEBUG=false`, the public `APP_URL`, and the real production mail server rather than Mailpit.

Useful commands:

```bash
# Show deployed releases
docker exec -it drakestrin_app vendor/bin/dep releases zeus06.de

# Show production migration status
docker exec -it drakestrin_app vendor/bin/dep artisan:migrate:status zeus06.de

# Roll the current symlink back to the preceding release
docker exec -it drakestrin_app vendor/bin/dep rollback zeus06.de

# Clear a stale deployment lock after an interrupted deployment
docker exec -it drakestrin_app vendor/bin/dep deploy:unlock zeus06.de
```

A rollback changes the deployed code and symlink only. It does not reverse database migrations; incompatible migrations must be handled separately and deliberately.
