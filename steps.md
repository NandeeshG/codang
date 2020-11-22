## These are from old project
0. Download all prereqs like node vue git php composer(with php) nuxt etc etc etc
1. Install nginx
2. Install php-fpm php-xml php-mbstring
3. Add a file in /etc/nginx/sites-available/ -> codetrophs.com
4. In codetrophs.com make php socket 7.2 and root as ~/Documents/codetrophs 
4b. Add ln -S to that in sites-enabled
5. chown -R $USER:$USER in codetrophs directory
6. test and restart by sudo nginx -t and sudo service nginx restart
7. Install postgresql and postgresql-contrib
8. Change to user and start postgres by -> 1.(sudo -i -u postgres) 2.(psql) (Exit by typing \q and then exit)
9. Add more users to postgres and work in their databases only
10. download composer file and move its bin to /usr/local/bin
11. composer is used to install slim/slim and slim/psr7
11b. make index.php in public folder and write api routes
12. Copy web server settings to nginx from slim site and change port for php-fpm in www.conf or something at /etc/php/7.2/fpm/pool.d
13. install php-pgsql, activate in etc/php/7.2/php.ini and then connect with dbms
13b. Also installed php cs fixer extension and composer in directory.
14. all nginx, php7.2-fpm, postgresql services should be running. nginx default server can point to your frontend or you can have routes for it if you want to host multiple apps. No need to start php server as things get routed to fmp server automatically which I have set to be running at 9000.
15. Also I used postman and its desktop agent for backend requests.

## Added in this project
16. requests from php made with curl libcurl3 libcurl3-dev php7.2-curl
