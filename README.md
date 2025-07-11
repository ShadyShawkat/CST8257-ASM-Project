# CST8257 Final Project

**SETUP:**
1. Install your preferred web development stack. I'm using **XAMPP** for this
2. Edit `httpd.conf` look for the line like:
   ```apache
   #Include conf/extra/httpd-vhosts.conf
   ```
   Uncomment this line by removing the `#`. You can access the conf file from XAMPP Control Panel>Config button
3. Open and edit `httpd-vhosts.conf` and add:
	```apache
   <VirtualHost \*:80>
   	DocumentRoot "C:/Users/YourUser/Documents/my-php-social-app/public" # <--- IMPORTANT: Path to your 'public' folder
   	ServerName my-social-app.local # <--- Your custom local domain
   	ErrorLog "logs/my-social-app-error.log"
   	CustomLog "logs/my-social-app-access.log" common

   	<Directory "C:/Users/YourUser/Documents/my-php-social-app/public"> # <--- Match DocumentRoot
   		Options Indexes FollowSymLinks MultiViews
   		AllowOverride All
   		Require all granted
       </Directory>
	</VirtualHost>
	```
4. Edit the your hosts file to point to your localhost and ServerName.
5. Restart Apache if it's already running.
6. In the public folder, create a `.htaccess` file and edit it to put the following:
	```apache
	<IfModule mod_rewrite.c>
    	RewriteEngine On
    	RewriteBase /

    	# Redirect all requests not leading to actual files or directories
    	# to index.php, allowing your PHP application to handle routing.
    	RewriteCond %{REQUEST_FILENAME} !-f
    	RewriteCond %{REQUEST_FILENAME} !-d
    	RewriteRule ^(.*)$ index.php [QSA,L]
	</IfModule>
	```
7. I included a SQL file to create the schema and some sample data. You can use your preferred SQL editor to take a look, modify, and run it.
