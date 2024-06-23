To contribute to bmlt-wp, fork, make your changes and send a pull request to the master branch.

Take a look at the issues for bugs that you might be able to help fix.

Once your pull request is merged it will be released in the next version.

We are using [bmlt-wordpress-deploy](https://github.com/bmlt-enabled/bmlt-wordpress-deploy/blob/main/README.md) to deploy the plugin to SVN.

To get things going in your local environment.

`docker-compose up`

Get your wordpress installation going.  Remember your admin password.  Once it's up, login to admin and activate the "BMLT Satellite Plugin" plugin.

Now you can make edits to the bmlt-wordpress-satellite-plugin.php file and it will instantly take effect.

In order to pull in the necessary updated dependencies run `composer update`

Please make note of the .editorconfig file and adhere to it as this will minimise the amount of formatting errors.  If you are using PHPStorm you will need to install the EditorConfig plugin.
