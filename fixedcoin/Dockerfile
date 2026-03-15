FROM ubuntu:22.04

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies including PHP
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    sudo \
    php \
    php-curl \
    php-cli \
    && rm -rf /var/lib/apt/lists/*

# Create fixedcoin user
RUN useradd -m -s /bin/bash fixedcoin

# Download and install FixedCoin
ENV FIXEDCOIN_VERSION=29.1.2

RUN cd /tmp && \
    wget -q https://github.com/Fixed-Blockchain/fixedcoin/releases/download/v${FIXEDCOIN_VERSION}/fixedcoin-${FIXEDCOIN_VERSION}-x86_64-linux-gnu.tar.gz && \
    tar -xzf fixedcoin-${FIXEDCOIN_VERSION}-x86_64-linux-gnu.tar.gz && \
    mv fixedcoind fixedcoin-cli /usr/local/bin/ && \
    rm -rf /tmp/*

# Create data directory
RUN mkdir -m 755 /home/fixedcoin/.fixedcoin
RUN chown -R fixedcoin:fixedcoin /home/fixedcoin

# Copy icon for web UI
COPY icon.png /app/icon.png

# Switch to fixedcoin user
USER fixedcoin
WORKDIR /home/fixedcoin

# Copy web UI files
COPY --chown=fixedcoin:fixedcoin www /var/www/html

# Expose ports
EXPOSE 24768 24761 8080

# Create startup script
COPY << 'EOF' /home/fixedcoin/start.sh
#!/bin/bash
set -e

# Wait for blockchain to sync
echo "Starting FixedCoin node..."

# Start fixedcoind in background
/usr/local/bin/fixedcoind -daemon -conf=/data/fixedcoin.conf -datadir=/data

# Wait for RPC to be ready
echo "Waiting for RPC to be ready..."
until /usr/local/bin/fixedcoin-cli -conf=/data/fixedcoin.conf -datadir=/data getblockchaininfo &> /dev/null; do
    sleep 1
done

echo "FixedCoin node is ready!"
echo ""
echo "Your wallet address:"
/usr/local/bin/fixedcoin-cli -conf=/data/fixedcoin.conf -datadir=/data getnewaddress

# Copy icon to web folder
cp /app/icon.png /var/www/html/ 2>/dev/null || true

# Start PHP web server
echo "Starting web interface..."
cd /var/www/html
php -S 0.0.0.0:8080 &

# Keep container running
tail -f /dev/null
EOF

RUN chmod +x /home/fixedcoin/start.sh

# Mount data volume
VOLUME ["/data"]

CMD ["/home/fixedcoin/start.sh"]
