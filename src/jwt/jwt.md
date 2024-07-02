```bash
sudo openssl genpkey -algorithm RSA -out ~/nginx/private.pem -pkeyopt rsa_keygen_bits:2048

sudo openssl rsa -pubout -in ~/nginx/private.key -out ~/nginx/public.pem
```

```
Add these keys to the server docker file
```