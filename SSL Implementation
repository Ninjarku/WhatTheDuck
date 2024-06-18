Video tutorial: https://www.youtube.com/watch?v=eqrDHkIFe8U
Written tutorial: https://neutrondev.com/how-to-set-up-ssl-certificate-localhost-xampp
Ubuntu Install SSL: https://www.ssldragon.com/how-to/install-ssl-certificate/ubuntu/
#### Step 1 Create a SSL Certificate using `makecert.bat`:
`makecert.bat`
```powershell
@echo off
set OPENSSL_CONF=./conf/openssl.cnf

if not exist .\conf\ssl.crt mkdir .\conf\ssl.crt
if not exist .\conf\ssl.key mkdir .\conf\ssl.key

bin\openssl req -new -out server.csr
bin\openssl rsa -in privkey.pem -out server.key
bin\openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 1825 -extfile v3.ext

set OPENSSL_CONF=
del .rnd
del privkey.pem
del server.csr

move /y server.crt .\conf\ssl.crt
move /y server.key .\conf\ssl.key

echo.
echo -----
echo What the duck is this.
echo The certificate was provided.
echo.
pause
```

`v3.ext`
```
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names
[alt_names]
DNS.1 = localhost
DNS.2 = *.whattheduck.com
DNS.3 = whattheduck.com
DNS.4 = 127.0.0.1
DNS.5 = 127.0.0.2
```
Run the script and it should generate
	Our FQDN is `whattheduck.com` everything else can be dummy information
	We should have the certificates after running

We should have the following:
- certificate.crt
- Ca-bundle.crt
- Private.key
Copy `certificate.crt` and `ca_bundle.crt` to `/etc/ssl/` and `private.key` to `/etc/ssl/private/`.

Rename the `certificate.crt` to `whattheduck.crt`
Rename the `private.key` to `whattheduck.key`

#### Step 2 Edit Apache Config file:
 **/etc/apache2/sites-enabled/your_site_name**
```
sudo a2ensite whattheduck.com
```

#### Step 3: Configure the Virtual Host block
This makes the site only accessible to HTTPS
```
DocumentRoot /var/www/site  
ServerName whattheduck.com  
SSLEngine on  
SSLCertificateFile /etc/ssl/whattheduck.crt  
SSLCertificateKeyFile  /etc/ssl/private/whattheduck.key  
SSLCertificateChainFile /etc/ssl/ca_bundle.crt
```
**Note**: If the SSLCertificateFile directive doesn’t work, use the SSLCACertificateFile instead.
Double-check the Virtual Host block, and save the .config file.

#### Step 4: Test your new .config file
Run `apachectlConfigtest`
```
apachectlConfigtest
```

### Step 5: Restart the Apache
```bash
apach ectl stop
apa chectl start
```

#### Step 6 Install the SSL Certificate onto our machine:
For our case we will need to do mounting of certs:
```bash
docker run -v /host/path/to/certs:/container/path/to/certs -d IMAGE_ID "update-ca-certificates"
```
The -v is to bind volumes to the docker container
