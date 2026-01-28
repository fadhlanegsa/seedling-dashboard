# Deployment Guide
Dashboard Stok Bibit Persemaian Indonesia

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server with mod_rewrite enabled
- Composer (optional, for dependency management)

## Installation Steps

### 1. Upload Files

Upload all files to your web server:
```
/var/www/html/seedling-dashboard/
```

Or for local development:
```
C:/xampp/htdocs/seedling-dashboard/
```

### 2. Configure Apache

Ensure mod_rewrite is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Configure virtual host (optional but recommended):
```apache
<VirtualHost *:80>
    ServerName seedling-dashboard.local
    DocumentRoot /var/www/html/seedling-dashboard/public
    
    <Directory /var/www/html/seedling-dashboard/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/seedling-error.log
    CustomLog ${APACHE_LOG_DIR}/seedling-access.log combined
</VirtualHost>
```

### 3. Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE seedling_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'seedling_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON seedling_dashboard.* TO 'seedling_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Import Database Schema

```bash
mysql -u seedling_user -p seedling_dashboard < database/schema.sql
mysql -u seedling_user -p seedling_dashboard < database/sample_data.sql
```

### 5. Configure Application

Edit `config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'seedling_dashboard');
define('DB_USER', 'seedling_user');
define('DB_PASS', 'your_secure_password');

// Application URL
define('APP_URL', 'http://your-domain.com/seedling-dashboard/public');
define('BASE_PATH', '/seedling-dashboard/public');

// Email Configuration (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@your-domain.com');
```

### 6. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/seedling-dashboard

# Set directory permissions
sudo find /var/www/html/seedling-dashboard -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/seedling-dashboard -type f -exec chmod 644 {} \;

# Set writable directories
sudo chmod -R 775 /var/www/html/seedling-dashboard/public/uploads
sudo chmod -R 775 /var/www/html/seedling-dashboard/logs
```

### 7. Test Installation

Visit: `http://your-domain.com/seedling-dashboard/public`

Default admin credentials:
- Username: `admin`
- Password: `admin123`

**IMPORTANT:** Change the default admin password immediately after first login!

## Post-Installation

### 1. Change Default Passwords

Login as admin and change:
- Admin password
- All BPDAS account passwords

### 2. Configure Email

Test email functionality:
- Go to Admin Dashboard
- Send a test email notification
- Verify email delivery

### 3. Upload Logo

Upload ministry logo for PDF generation:
```
public/images/logo-kementerian.png
```

### 4. Configure Backup

Set up automated database backups:

```bash
# Create backup script
sudo nano /usr/local/bin/backup-seedling-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/seedling-dashboard"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR
mysqldump -u seedling_user -p'your_password' seedling_dashboard > $BACKUP_DIR/backup_$DATE.sql
find $BACKUP_DIR -name "backup_*.sql" -mtime +30 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-seedling-db.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
0 2 * * * /usr/local/bin/backup-seedling-db.sh
```

### 5. Enable SSL (Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Obtain SSL certificate
sudo certbot --apache -d your-domain.com
```

### 6. Configure Firewall

```bash
# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## Security Checklist

- [ ] Change all default passwords
- [ ] Enable SSL/HTTPS
- [ ] Configure firewall
- [ ] Set proper file permissions
- [ ] Disable error display in production (config.php)
- [ ] Enable logging
- [ ] Configure automated backups
- [ ] Update SMTP credentials
- [ ] Review and update CSRF token settings
- [ ] Configure session timeout
- [ ] Enable rate limiting (optional)

## Troubleshooting

### Issue: 404 Error on all pages

**Solution:** Enable mod_rewrite
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Issue: Database connection failed

**Solution:** Check database credentials in `config/config.php`

### Issue: Permission denied errors

**Solution:** Set correct permissions
```bash
sudo chown -R www-data:www-data /var/www/html/seedling-dashboard
sudo chmod -R 775 public/uploads logs
```

### Issue: Email not sending

**Solution:** 
1. Check SMTP credentials in config.php
2. Enable "Less secure app access" for Gmail (or use App Password)
3. Check firewall allows outbound SMTP connections

### Issue: Charts not displaying

**Solution:** Ensure Chart.js CDN is accessible
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```

## Maintenance

### Regular Tasks

**Daily:**
- Monitor error logs: `logs/error_*.log`
- Check system performance

**Weekly:**
- Review user registrations
- Check pending requests
- Verify backup completion

**Monthly:**
- Update stock data
- Review and archive old requests
- Database optimization
- Security updates

### Database Optimization

```sql
-- Optimize tables
OPTIMIZE TABLE users, bpdas, stock, requests, seedling_types;

-- Analyze tables
ANALYZE TABLE users, bpdas, stock, requests, seedling_types;

-- Check table status
CHECK TABLE users, bpdas, stock, requests, seedling_types;
```

### Log Rotation

```bash
# Create logrotate config
sudo nano /etc/logrotate.d/seedling-dashboard
```

```
/var/www/html/seedling-dashboard/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```

## Monitoring

### Key Metrics to Monitor

1. **System Performance**
   - Page load times
   - Database query performance
   - Server resource usage

2. **Application Metrics**
   - User registrations
   - Request submissions
   - Approval rates
   - Stock updates

3. **Security**
   - Failed login attempts
   - Suspicious activities
   - Error rates

### Monitoring Tools (Optional)

- **New Relic** - Application performance monitoring
- **Sentry** - Error tracking
- **Google Analytics** - User behavior tracking
- **Uptime Robot** - Uptime monitoring

## Support

For technical support or questions:
- Email: support@seedling-dashboard.id
- Documentation: See README.md and INSTALLATION.md
- Issue Tracker: [GitHub Issues]

## License

Copyright © 2024 Kementerian Lingkungan Hidup dan Kehutanan
All rights reserved.
