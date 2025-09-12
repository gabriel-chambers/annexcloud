# WordPress Boilerplate with "berg" plugin and themes

If you are going to setup "**realm**" project, set `IS_A_REALM_PROJECT` of`composer-post.sh` to `true`.

Check berg packages are up-to date with [this](https://docs.google.com/spreadsheets/d/1faeBpTX-u4JKfmv9xNejbi-E7ebyXLYkYfE3mgKfVaI/edit#gid=0).

Run `./install.sh` and drink a cup of ☕ until everything gets downloaded(Keep and eye on npm builds for any errors).

Modify `wp-config-sample.php` and add,
  1.  auth keys. Use [this](https://api.wordpress.org/secret-key/1.1/salt/) link to generate new ones.
  2.  S3 configuration constants
  3. `require_once ABSPATH . 'vendor/autoload.php';` just above the last line of the file(`require_once ABSPATH . 'wp-settings.php';`)
  4. set `define( 'SCRIPT_DEBUG', true );` in dev environments for easy debugging.

**IMPORTANT** - Make sure to commit `composer.lock` file. Do not ever modify `require` and `require-dev` sections of `composer.json` manually unless it's absolutely necessary.

When creating pipeline ask dev-ops engineer to add `--no-dev` option to the `composer install` command to prevent potential S3 plugin errors.

This [project setup checklist](https://docs.google.com/spreadsheets/d/1q6v9fd1hFeDr8CRpgE3vfoVcFB90T-qhHpcVbCk2RjM/edit?usp=sharing) will get reviewed in the initial code review of the project. So make sure to complete all the tasks of it.
