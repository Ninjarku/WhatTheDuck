# How to install private public key pair to put in server
Generate keys to use for JWT signing
```bash
sudo openssl genpkey -algorithm RSA -out private.pem -pkeyopt rsa_keygen_bits:2048
sudo openssl rsa -pubout -in pub.pem -out public_key.pem
```

If ran inside the container, can be done this way to directly generate them into needed directory
```bash
sudo openssl genpkey -algorithm RSA -out /var/www/private/private.pem -pkeyopt rsa_keygen_bits:2048
sudo openssl rsa -pubout -in /var/www/private/private.pem -out /var/www/private/public.pem
```

Transfer the ownership to www-data since it needs it
```bash
sudo chown www-data /var/www/private/private.pem
sudo chown www-data /var/www/private/public.pem
```