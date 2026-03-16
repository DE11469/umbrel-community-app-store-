FROM nginx:alpine

# Copy web UI files
COPY www/ /usr/share/nginx/html/

# Copy icon
COPY www/icon.png /usr/share/nginx/html/ 2>/dev/null || true

# Expose port
EXPOSE 80
