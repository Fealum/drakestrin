# Agent Notes

## Testing

- Run Laravel feature tests from the Docker app container, not from the host PHP install.
- From the repo root, use the devop compose project:

```bash
cd devop
docker compose exec -T app php artisan test
```

- For targeted tests, pass the test paths after `php artisan test`, for example:

```bash
cd devop
docker compose exec -T app php artisan test tests/Feature/ForumModelTest.php tests/Feature/CompanyReadTest.php tests/Feature/UserReadTest.php
```

## Best practices

- Code as if you were a senior Laravel developer.
- Use model scopes and repositories.
- Use requests and DTOs.