--------------------
Extra: Disable HSTS
--------------------
Version: 0.1-alpha1
Created: July 9th, 2018
Since: July 9th, 2018
Author: shelbalart
License: Apache License

Adds HTTP response header in order to clear HSTS policy of the user's browser.

This might be necessary if you're migrating to a new hosting provider from the previous one which set up HSTS, and the
new one does not allow to modify HTTP response headers via .htaccess.
