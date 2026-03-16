FROM php:8.2-fpm-alpine

# Copy web UI files
COPY www/ /var/www/html/

# Copy icon
COPY www/icon.png /var/www/html/ 2>/dev/null || true

# Expose port
EXPOSE 9000
