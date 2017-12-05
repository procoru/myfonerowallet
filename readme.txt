Requirements and installation

1. fonero-daemon
2. fonero-wallet-rpc 
3. supervisor
4. composer
5. php7

###

compile fonero from source
install supervisor
mkdir /opt/fonero/wallets
cd /foo/bar/webwallet
composer install
cp src/settings.php src/settings.local.php # and edit src/settings.local.php

###

Append supervisor config /etc/supervisor/supervisord.conf:

[program:fonero-daemon]
directory=/foo/bar/fonero/build/bin
command=/usr/bin/sudo -u www-data /foo/bar/fonero/build/bin/fonero-daemon
autostart=true
autorestart=true
user=www-data
stderr_logfile=/var/log/fonero-daemon.err.log

[program:fonero-rpc]
directory=/foo/bar/fonero/build/bin
command=/usr/bin/sudo -u www-data /foo/bar/fonero/build/bin/fonero-wallet-rpc --rpc-bind-port 18082 --disable-rpc-login --wallet-dir /opt/fonero/wallets
autostart=true
autorestart=true
user=www-data
stderr_logfile=/var/log/fonero-rpc.err.log

###

Replace "--disable-rpc-login" on "--rpc-login username[:password]", see fonero-wallet-rpc --help

###

sudo supervisorctl start all

###


