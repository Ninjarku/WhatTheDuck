#### Create a self signed cert
https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu
put in ssl file first, we will copy it to the docker later
```bash
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

Create a strong diffie helman group put in ssl file first, we will copy it to the docker later
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

Create the site config, the config will be typically the site-available
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
    index.php index.html index.nginx-debian.html;
    server_name whattheduck.com www.whattheduck.com whattheduck.ddns.net;
    
    
    location / {
	    try_files $uri $uri/ =404;
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
    index.php index.html index.nginx-debian.html;
    server_name whattheduck.com www.whattheduck.com whattheduck.ddns.net;
    
    location / {
	    try_files \$uri \$uri/ =404;
    }
}" >  ~/nginx/ssl/whattheduck
```

```bash
#sudo nano ~/nginx/ssl/whattheduck.ddns.net
```

`/etc/nginx/sites-available/whattheduck.ddns.net`
```bash
echo "server {
    listen 80;
    listen [::]:80;

    server_name whattheduck.com www.whattheduck.com whattheduck.ddns.net;

    return 302 https://\$server_name\$request_uri;
}" > ~/nginx/ssl/whattheduck.ddns.net
```

In dockerfile:
```bash
# For listing
ufw app list
ufw allow 'Nginx Full'
ufw delete allow 'Nginx HTTP'
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
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
CMD ["sh", "-c", "rm -f /etc/nginx/sites-enabled/default;"]
CMD ["sh", "-c", "ln -s /etc/nginx/sites-available/whattheduck /etc/nginx/sites-enabled/default;"]
#CMD ["sh", "-c", "ufw allow 'Nginx Full';"]
#CMD ["sh", "-c", "ufw delete allow 'Nginx HTTP';"]
```

Docker build image
```bash
sudo docker build . -t php-docker
```

```bash
ln -s default /etc/nginx/sites-available/whattheduck
```