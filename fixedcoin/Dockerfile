FROM php:8.2-fpm-alpine

# Install nginx for serving PHP
RUN apk add --no-cache nginx

# Copy web UI files
COPY www/ /var/www/html/

# Copy icon
COPY www/icon.png /var/www/html/ 2>/dev/null || true

# Copy nginx config
RUN echo 'server { listen 8080; root /var/www/html; index index.php index.html; location ~ \.php$ { fastcgi_pass 127.0.0.1:9000; fastcgi_index index.php; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; include fastcgi_params; } }' > /etc/nginx/http.d/default.conf

EXPOSE 8080

CMD sh -c "php-fpm -R -F & nginx & tail -f /dev/null"
