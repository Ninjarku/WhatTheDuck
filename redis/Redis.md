Get the image
```bash
docker pull redis
```

Run the instance of docker
```bash
#sudo docker run --network host -d --name redis -p 6379:6379 redis:latest
sudo docker run --network jenkins -d --name redis redis:latest
```

Dependencies:
```bash
# Run inside /var/www/html of webserver container
composer require firebase/php-jwt predis/predis twilio/sdk phpmailer/phpmailer
```
