#!/bin/bash

# FixedCoin Umbrel App Installation Script

echo "Installing FixedCoin..."

# Create the app data directory
mkdir -p /home/umbrel/umbrel/app-data/fixedcoin/data

# Create the FixedCoin config file
cat > /home/umbrel/umbrel/app-data/fixedcoin/data/fixedcoin.conf << 'EOF'
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
chmod 600 /home/umbrel/umbrel/app-data/fixedcoin/data/fixedcoin.conf
chmod -R 700 /home/umbrel/umbrel/app-data/fixedcoin/data/

echo "FixedCoin configuration created!"
