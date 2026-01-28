# Installation Guide
## Dashboard Stok Bibit Persemaian Indonesia

This guide will help you install and configure the Seedling Stock Dashboard system.

## Prerequisites

Before installation, ensure you have:

- **Web Server**: Apache 2.4+ with mod_rewrite enabled
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - openssl
  - fileinfo

## Step-by-Step Installation

### 1. Download and Extract

Extract the project files to your web server directory:

**XAMPP (Windows)**:
```
C:\xampp\htdocs\seedling-dashboard\
```

**LAMP (Linux)**:
```
/var/www/html/seedling-dashboard/
```

**MAMP (Mac)**:
```
/Applications/MAMP/htdocs/seedling-dashboard/
```

### 2. Create Database

Open MySQL command line or phpMyAdmin and run:

```sql
CREATE DATABASE seedling_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import Database Schema

**Using Command Line**:
```bash
cd C:\xampp\htdocs\seedling-dashboard
mysql -u root -p seedling_dashboard < database/schema.sql
mysql -u root -p seedling_dashboard < database/sample_data.sql
```

**Using phpMyAdmin**:
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select `seedling_dashboard` database
3. Click "Import" tab
4. Choose `database/schema.sql` and import
5. Choose `database/sample_data.sql` and import

### 4. Configure Application

Edit `config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'seedling_dashboard');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your MySQL password

// Application URL
define('APP_URL', 'http://localhost/seedling-dashboard/public');
define('BASE_PATH', '/seedling-dashboard/public');

// Email Configuration (Optional - for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@seedling-dashboard.id');
```

### 5. Set File Permissions

**Windows**: Right-click folders → Properties → Security → Edit permissions

**Linux/Mac**:
```bash
chmod -R 755 seedling-dashboard/
chmod -R 777 seedling-dashboard/public/uploads/
chmod -R 777 seedling-dashboard/logs/
```

### 6. Enable Apache mod_rewrite

**XAMPP (Windows)**:
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Find `AllowOverride None` and change to `AllowOverride All`
4. Restart Apache

**Linux**:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 7. Configure Virtual Host (Optional but Recommended)

Edit Apache configuration:

**XAMPP**: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Add:
```apache
<VirtualHost *:80>
    ServerName seedling-dashboard.local
    DocumentRoot "C:/xampp/htdocs/seedling-dashboard/public"
    
    <Directory "C:/xampp/htdocs/seedling-dashboard/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edit hosts file:

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
**Linux/Mac**: `/etc/hosts`

Add:
```
127.0.0.1 seedling-dashboard.local
```

Restart Apache.

### 8. Access the Application

Open your browser and navigate to:

**Without Virtual Host**:
```
http://localhost/seedling-dashboard/public
```

**With Virtual Host**:
```
http://seedling-dashboard.local
```

## Default Login Credentials

### Administrator
- **Username**: admin
- **Password**: admin123
- **Email**: admin@seedling-dashboard.id

### BPDAS Users (10 accounts available)
- **Username**: bpdas_jabar
- **Password**: bpdas123
- **Email**: jabar@bpdas.id

Other BPDAS accounts: bpdas_aceh, bpdas_sumut, bpdas_jambi, bpdas_jateng, bpdas_jatim, bpdas_kalsel, bpdas_kaltim, bpdas_sulsel, bpdas_papua

### Public Users (3 accounts available)
- **Username**: budi_santoso
- **Password**: user123
- **Email**: budi.santoso@email.com

Other public accounts: siti_nurhaliza, agus_wijaya

**⚠️ IMPORTANT**: Change all default passwords after first login!

## Email Configuration (Optional)

To enable email notifications:

### Using Gmail:

1. Enable 2-Step Verification in your Google Account
2. Generate App Password:
   - Go to Google Account → Security
   - 2-Step Verification → App passwords
   - Generate password for "Mail"
3. Update `config/config.php`:
   ```php
   define('SMTP_USERNAME', 'your-email@gmail.com');
   define('SMTP_PASSWORD', 'generated-app-password');
   ```

### Using Other SMTP:

Update SMTP settings in `config/config.php` according to your provider.

## CSV Import

To import data from CSV files:

```bash
# Import BPDAS data
php utils/CSVImporter.php import bpdas path/to/bpdas.csv

# Import Stock data
php utils/CSVImporter.php import stock path/to/stock.csv

# Import Seedling Types
php utils/CSVImporter.php import seedlings path/to/seedlings.csv
```

CSV Format Examples:

**BPDAS CSV**:
```
name,province_code,address,phone,email,contact_person
BPDAS Example,JB,Jl. Example No. 1,022-1234567,example@bpdas.id,John Doe
```

**Stock CSV**:
```
bpdas_name,seedling_name,quantity,last_update_date,notes
BPDAS Example,Jati,5000,2024-01-15,Stock update
```

**Seedling Types CSV**:
```
name,scientific_name,category,description
Jati,Tectona grandis,Pohon Hutan,Kayu berkualitas tinggi
```

## Troubleshooting

### Error: Database connection failed
- Check MySQL service is running
- Verify database credentials in `config/config.php`
- Ensure database exists

### Error: 404 Not Found
- Check mod_rewrite is enabled
- Verify `.htaccess` files exist
- Check BASE_PATH in `config/config.php`

### Error: Permission denied
- Set correct permissions on uploads and logs folders
- Windows: Check folder security settings
- Linux/Mac: Use chmod commands

### Email not sending
- Check SMTP credentials
- Verify firewall allows SMTP port
- Check error logs in `logs/` folder

### Cannot upload files
- Check upload folder permissions
- Verify MAX_FILE_SIZE in `config/config.php`
- Check PHP upload_max_filesize setting

## Testing the Installation

1. **Test Database Connection**:
   - Try logging in with admin credentials
   - If successful, database is working

2. **Test File Upload**:
   - Login as BPDAS user
   - Try adding stock
   - Check if data saves

3. **Test Email** (if configured):
   - Submit a seedling request as public user
   - Check if BPDAS receives notification

4. **Test PDF Generation**:
   - Approve a request as BPDAS
   - Try downloading approval letter

## Production Deployment

Before deploying to production:

1. **Change all default passwords**
2. **Update config.php**:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   define('LOG_ERRORS', true);
   ```
3. **Set secure file permissions**
4. **Enable HTTPS**
5. **Configure backup system**
6. **Set up monitoring**

## Support

For issues or questions:
- Check README.md for detailed documentation
- Review error logs in `logs/` folder
- Check PROJECT_SUMMARY.md for implementation details

## Next Steps

After successful installation:

1. Login as admin
2. Change default password
3. Add real BPDAS data
4. Configure email notifications
5. Test all functionality
6. Train users

---

**Installation Complete!** 🎉

Your Seedling Stock Dashboard is now ready to use.
