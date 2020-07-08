# Birch Contemporary gallery website

## Requirements

- Apache web server
- MySQL database
- PHP 5.5

## Deployment

1. Replace *** placeholders with your database configuration in [database.php](https://github.com/jakubdolejs/birchcontemporary.com/blob/4b0e8fe3e10dda272ea628f83ef762794b249ffe/application/config/database.php#L54).
2. Run database migration by executing the following command in the project's directory: `php index.php migrate`.
3. Check your database to make sure tables have been created.
4. Configure Apache web server to respond to the requests for your domain name.
5. Start Apache web server. The exact command will depend on the server's operating system, e.g., `apachectl -k start`.
