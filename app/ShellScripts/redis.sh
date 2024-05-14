echo 'Installing redis'
mkdir ~/.redis/
echo -e "unixsocket /home/$(whoami)/.redis/sock\ndaemonize no\nport 0\nsave \"\"" > ~/.redis/conf
echo -e "[program:redis]\ncommand=redis-server %(ENV_HOME)s/.redis/conf\ndirectory=%(ENV_HOME)s/.redis\nautostart=yes\nautorestart=yes\nstartsecs=30" > ~/etc/services.d/redis.ini
supervisorctl reread
supervisorctl update
supervisorctl status
echo 'Finished installing redis'
