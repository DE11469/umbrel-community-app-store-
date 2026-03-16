#!/bin/bash
set -e

# FixedCoin Umbrel App Installation Script
# This runs on the Umbrel host

echo "Installing FixedCoin..."

# Create the app data directory using Umbrel's environment variable
APP_DATA_DIR="${APP_DATA_DIR:-$HOME/umbrel/app-data/fixedcoin}"

mkdir -p "$APP_DATA_DIR/data"

# Create the FixedCoin config file
cat > "$APP_DATA_DIR/data/fixedcoin.conf" << 'EOF'
server=1
listen=1
rpcallowip=127.0.0.1
rpcallowip=172.16.0.0/12
rpcuser=umbrel
rpcpassword=changeme
rpcport=24761
port=24768
dbcache=100
maxconnections=16
EOF

# Set proper permissions
chmod 600 "$APP_DATA_DIR/data/fixedcoin.conf"
chmod -R 700 "$APP_DATA_DIR/data/"

echo "FixedCoin configuration created!"
echo "RPC User: umbrel"
echo "RPC Password: changeme (CHANGE THIS!)"
echo "RPC Port: 24761"
echo "P2P Port: 24768"
