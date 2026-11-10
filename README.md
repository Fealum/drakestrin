# Drakestrin

## Composer And Post Elements

Run the read-only migration verifier before and after the verification deployment:

```bash
docker compose -f devop/docker-compose.yml exec -T app php artisan forum:verify-post-elements
# Machine-readable output; returns a non-zero status if discrepancies exist:
docker compose -f devop/docker-compose.yml exec -T app php artisan forum:verify-post-elements --json
```

It checks message bodies and smileys, element subtypes, transfer links, scene boundaries, poll settings/options/votes/participation, and preserved character signatures. It also reports representative record IDs for discrepancies. Keep legacy message, scene-anchor, poll and signature storage through one verification deployment. Resolve reported discrepancies before a separate cleanup migration; do not drop legacy storage as part of ordinary composer changes.

## Local email

The development Docker Compose stack captures all SMTP mail with Mailpit. Its inbox is available at http://localhost:8025; SMTP remains internal at `mailpit:1025` and does not deliver messages externally.
