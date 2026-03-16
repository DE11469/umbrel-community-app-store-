FROM ubuntu:22.04

# Install dependencies
RUN apt-get update && apt-get install -y \
    php \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Download and install FixedCoin
RUN cd /tmp && \
    curl -sL https://github.com/Fixed-Blockchain/fixedcoin/releases/download/v29.1.2/fixedcoin-29.1.2-x86_64-linux-gnu.tar.gz | tar -xz && \
    mv fixedcoind fixedcoin-cli /usr/local/bin/ && \
    rm -rf /tmp/*

# Copy web UI
COPY www/ /var/www/html/

# Make icon accessible
COPY www/icon.png /var/www/html/icon.png 2>/dev/null || true

EXPOSE 8080 24768 24761

CMD ["bash", "-c", "fixedcoind -daemon -conf=/data/fixedcoin.conf -datadir=/data && php -S 0.0.0.0:8080 -t /var/www/html & tail -f /dev/null"]
