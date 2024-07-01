#### Step 1 Create a self signed cert
Generate the SSL key and crt
```bash
mkdir ~/nginx
mkdir ~/nginx/ssl
#sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt

sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ~/nginx/ssl/nginx-selfsigned.key -out ~/nginx/ssl/nginx-selfsigned.crt
```
Things to fill
```
SG
Singapore
SG
WhatTheDuck
WTD
18.224.18.18
admin@whattheduck.com
```
![[Pasted image 20240627145419.png]]

#### Step 2 Create a Diffie Helman Group to secure communications
Create a strong diffie helman group 
```bash
#sudo openssl dhparam -out /etc/nginx/dhparam.pem 4096
sudo openssl dhparam -out ~/nginx/ssl/dhparam.pem 4096

#sudo nano /etc/nginx/snippets/self-signed.conf
sudo nano ~/nginx/ssl/self-signed.conf
```

Contents of `self-signed.conf`
```
ssl_certificate /etc/ssl/certs/nginx-selfsigned.crt;
ssl_certificate_key /etc/ssl/private/nginx-selfsigned.key;
```

Create `ssl-params.conf`
```bash
#sudo nano /etc/nginx/snippets/ssl-params.conf
sudo nano ~/nginx/ssl/ssl-params.conf
```

```bash
ssl_protocols TLSv1.3;
ssl_prefer_server_ciphers on;
ssl_dhparam /etc/nginx/dhparam.pem; 
ssl_ciphers EECDH+AESGCM:EDH+AESGCM;
ssl_ecdh_curve secp384r1;
ssl_session_timeout  10m;
ssl_session_cache shared:SSL:10m;
ssl_session_tickets off;
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 1.1.1.1 valid=300s;
resolver_timeout 5s;
# Disable strict transport security for now. You can uncomment the following
# line if you understand the implications.
#add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";
add_header X-Frame-Options DENY;
add_header X-Content-Type-Options nosniff;
add_header X-XSS-Protection "1; mode=block";
```

#### Step 3 Create the site configs
The config will be typically in the site-available
whattheduck and whattheduck.ddns.net
```bash
#sudo cp /etc/nginx/sites-available/whattheduck /etc/nginx/sites-available/whattheduck.bak

## current one is at default
# sudo nano /etc/nginx/sites-available/whattheduck
sudo nano ~/nginx/ssl/whattheduck
```

`/etc/nginx/sites-available/whattheduck
```bash
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    include snippets/self-signed.conf;
    include snippets/ssl-params.conf;

	root /var/www/html;
    index index.php index.html index.nginx-debian.html;
    server_name whattheduck.ddns.net;
    
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
	    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```
Or try echo
```bash
echo "server {
    listen 443 ssl;
    listen [::]:443 ssl;
    include snippets/self-signed.conf;
    include snippets/ssl-params.conf;

	root /var/www/html;
    index index.php index.html index.nginx-debian.html;
    server_name whattheduck.ddns.net;
    
    location / {
	    try_files \$uri \$uri/ =404;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}" >  ~/nginx/ssl/whattheduck
```

`/etc/nginx/sites-available/whattheduck.ddns.net`
```bash
echo "server {
    listen 80;
    listen [::]:80;

    server_name whattheduck.com www.whattheduck.com whattheduck.ddns.net;

    return 301 https://\$server_name\$request_uri;
}" > ~/nginx/ssl/whattheduck.ddns.net
```

#### Step 4 Configure NGINX config file

```bash
nano ~/nginx/ssl/nginx.conf
```


```bash
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
        worker_connections 768;
        # multi_accept on;
}

http {

        ##
        # Basic Settings
        ##
        sendfile on;
        tcp_nopush on;
        types_hash_max_size 2048;
        # server_tokens off;
        # server_names_hash_bucket_size 64;
        # server_name_in_redirect off;

        include /etc/nginx/mime.types;
        default_type application/octet-stream;

        ##
        # SSL Settings
        ##

        ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3; # Dropping SSLv3, ref: POODLE
        ssl_prefer_server_ciphers on;

        ##
        # Logging Settings
        ##

        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/error.log;
        
        ##
        # Gzip Settings
        ##
		
        gzip on;

        # gzip_vary on;
        # gzip_proxied any;
        # gzip_comp_level 6;
        # gzip_buffers 16 8k;
        # gzip_http_version 1.1;
        # gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

        ##
        # Virtual Host Configs
        ##

        include /etc/nginx/conf.d/*.conf;
        include /etc/nginx/sites-enabled/*;
        include /etc/nginx/sites-available/*.conf;
}
```

#### Step 5 Configure the Dockerfile

```bash
sudo nano Dockerfile
```

```dockerfile
# Use the official PHP-FPM image as the base image
FROM php:7.4-fpm

# Install Nginx and necessary dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    openssl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql
#     ufw \

# Copy db-config.ini
COPY db-config.ini /var/www/private/db-config.ini

# Copy custom nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy your PHP application code
COPY . /var/www/html

# SSL certificate
COPY ssl/nginx-selfsigned.crt  /etc/ssl/certs/nginx-selfsigned.crt
COPY ssl/nginx-selfsigned.key /etc/ssl/private/nginx-selfsigned.key
COPY ssl/dhparam.pem /etc/nginx/dhparam.pem
COPY ssl/self-signed.conf /etc/nginx/snippets/self-signed.conf
COPY ssl/ssl-params.conf /etc/nginx/snippets/ssl-params.conf
COPY ssl/whattheduck /etc/nginx/sites-available/whattheduck
COPY ssl/whattheduck.ddns.net /etc/nginx/sites-available/whattheduck.ddns.net


# Set the working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
EXPOSE 443

# Start PHP-FPM and Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]

# Linking to sites-enabled
RUN rm /etc/nginx/sites-enabled/default
#RUN ln -s /etc/nginx/sites-available/whattheduck /etc/nginx/sites-enabled/default
RUN ln -s /etc/nginx/sites-available/whattheduck /etc/nginx/sites-enabled/whattheduck
RUN ln -s /etc/nginx/sites-available/whattheduck /etc/nginx/sites-enabled/whattheduck.ddns.net
```

#### Step 6 build the image
Docker build image
```bash
sudo docker build . -t php-docker

sudo docker run --name php-docker --network jenkins -p 80:80 -p 443:443 -v ~/docker-volumes/php-docker/whattheduck:/var/www/html -d php-docker

# Check for container id and status, if it works it should not say exited
sudo docker container ls -a

# For ease of using container later (Replace the id accordingly)
DOCKER=Image_id  

# If any error, use this to check the logs
sudo docker logs $DOCKER 
```

If need restart
```bash
sudo docker restart  $DOCKER  
```

If need Remove
```bash
sudo docker stop $DOCKER

sudo docker container rm $DOCKER

sudo docker image rm php-docker
```

References:
https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu
https://www.digitalocean.com/community/tutorials/php-fpm-nginx

Firewall rules:
```bash
# For listing
ufw app list
ufw allow 'Nginx Full'
ufw delete allow 'Nginx HTTP'
```