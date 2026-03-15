#!/bin/bash
set -e

# FixedCoin Umbrel App Installation Script

echo "Installing FixedCoin..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install Docker first."
    exit 1
fi

# Create the app directory
mkdir -p $HOME/umbrel/app-data/fixedcoin/data

# Create the FixedCoin config file
cat > $HOME/umbrel/app-data/fixedcoin/data/fixedcoin.conf << 'EOF'
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
chmod 600 $HOME/umbrel/app-data/fixedcoin/data/fixedcoin.conf
chmod -R 700 $HOME/umbrel/app-data/fixedcoin/data/

echo "FixedCoin configuration created!"
echo "RPC User: umbrel"
echo "RPC Password: changeme (CHANGE THIS!)"
echo "RPC Port: 24761"
echo "P2P Port: 24768"
echo ""
echo "Now you can start the node with: docker-compose up -d"
