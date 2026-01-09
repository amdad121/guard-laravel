# Upgrade Guide

Current Version: **v1.0.0**

## Quick Upgrade

1. **Update the package**

   ```bash
   composer update amdadulhaq/guard-laravel
   ```

2. **Publish new migrations**

   ```bash
   php artisan vendor:publish --tag="guard-migrations" --force
   php artisan migrate
   ```

3. **Update config (optional)**

   ```bash
   php artisan vendor:publish --tag="guard-config" --force
   ```

4. **Clear cache**
   ```bash
   php artisan cache:clear
   ```

## That's It!

No breaking changes. Your application should work immediately after these steps.

## Need Help?

If you encounter any issues:

1. Check the [CHANGELOG](CHANGELOG.md) for changes
2. Clear all caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
3. Open an issue on [GitHub](https://github.com/amdad121/guard-laravel/issues)
