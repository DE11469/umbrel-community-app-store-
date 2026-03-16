FROM debian:bookworm-slim

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
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
COPY www/icon.png /var/www/html/icon.png 2>/dev/null || true

EXPOSE 8080 24768 24761

CMD ["sh", "-c", "\
mkdir -p /data; \
if [ ! -f /data/fixedcoin.conf ]; then \
  echo 'server=1' > /data/fixedcoin.conf; \
  echo 'listen=1' >> /data/fixedcoin.conf; \
  echo 'rpcallowip=127.0.0.1' >> /data/fixedcoin.conf; \
  echo 'rpcallowip=172.16.0.0/12' >> /data/fixedcoin.conf; \
  echo 'rpcuser=umbrel' >> /data/fixedcoin.conf; \
  echo 'rpcpassword=changeme' >> /data/fixedcoin.conf; \
  echo 'rpcport=24761' >> /data/fixedcoin.conf; \
  echo 'port=24768' >> /data/fixedcoin.conf; \
fi; \
fixedcoind -daemon -conf=/data/fixedcoin.conf -datadir=/data; \
sleep 2; \
php -S 0.0.0.0:8080 -t /var/www/html & tail -f /dev/null"]
