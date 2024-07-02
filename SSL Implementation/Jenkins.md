Install certbot using snap
```bash
sudo 
snap install --classic certbot
apt install certbot
```


```bash
sudo ln -s /snap/bin/certbot /usr/bin/certbot

#sudo certbot --nginx
# Get the cert from certbot
sudo certbot certonly --nginx

sudo certbot renew --dry-run
```


```bash
apt install python3-certbot-nginx
certbot --nginx
certbot certonly --nginx

/etc/letsencrypt/live/whattheduck.ddns.net/fullchain.pem
```