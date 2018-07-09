# Disable HSTS

This is a plugin for MODX Revo.

Adds HTTP response header in order to clear HSTS policy of the user's browser.

This might be necessary if you're migrating to a new hosting provider from the previous one which set up HSTS, and the
new one does not allow to modify HTTP response headers via `.htaccess`.

This simple plugin just calls standard PHP function [`header()`](http://php.net/manual/en/function.header.php) before MODX rendering a webpage in order to add the appropriate `Strict-Transport-Security: max-age=0; includeSubDomains; preload` HTTP header.
