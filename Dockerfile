# Gunakan image PHP resmi
FROM php:8.2-apache

# Salin semua file ke container
COPY . /var/www/html/

# Buka port 80 untuk akses web
EXPOSE 80

# Jalankan Apache di container
CMD ["apache2-foreground"]
