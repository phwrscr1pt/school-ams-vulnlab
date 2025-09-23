FROM php:8.2-apache

# Enable mysqli and Apache overrides for .htaccess
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli  && a2enmod rewrite &&     { echo '<Directory /var/www/html>';       echo '    AllowOverride All';       echo '    Require all granted';       echo '</Directory>'; } > /etc/apache2/conf-available/allowoverride.conf &&     a2enconf allowoverride

# Increase upload limits (lab convenience)
RUN { echo 'upload_max_filesize=20M'; echo 'post_max_size=20M'; } > /usr/local/etc/php/conf.d/uploads.ini
