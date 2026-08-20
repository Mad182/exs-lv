# EXS.LV #

Web portal and gaming platform backend.

---

## Ātrā palaide ar Docker (Ieteicamais veids) ##

Visātrākais un ērtākais veids, kā palaist EXS.LV lokālās izstrādes vidi, ir izmantojot Docker un Docker Compose. Vides konteineri nodrošina visu nepieciešamo (PHP 8.2-Apache, MariaDB ar automātiski ielādētu datubāzes shēmu un testa datiem, kā arī Memcached).

### 1. Koda lejupielāde un submoduļu atjaunināšana ###

```bash
git clone https://github.com/Mad182/exs-lv.git && cd exs-lv
git submodule update --init --recursive
```

### 2. Konfigurācijas faila sagatavošana ###

Izveido `configdb.php` no parauga faila:

```bash
cp exs.lv/configdb.sample.php exs.lv/configdb.php
```

### 3. Konteineru palaide ###

```bash
docker compose up -d
```

Pēc konteineru palaišanas projekts būs pieejams jūsu pārlūkā:
👉 **[http://localhost:8080](http://localhost:8080)**

### Kas tiek nodrošināts automātiski: ###

* **Tīmekļa serveris (`exs-web`)**: PHP 8.2 ar Apache uz porta `8080` (`http://localhost:8080`).
* **Datubāze (`exs-db`)**: MariaDB 10.11 ar automātiski inicializētu shēmu (`dev-draza/schema.sql`), kategorijām (`dev-draza/cat.sql`) un parauga izstrādes datiem (`dev-draza/seed_dev.sql`).
* **Testa lietotāji**: Ielādēti 10 parauga lietotāji (piem., `Madars`, `Jānis`, `Pēteris`, `Artūrs`). Visu lokālo izstrādes lietotāju parole ir: `password123`.
* **Kešatmiņa (`exs-memcached`)**: Memcached serveris kešošanai.

### Noderīgas Docker komandas: ###

* **Skatīt žurnālierakstus (logs):**
  ```bash
  docker compose logs -f
  ```
* **Apturēt vidi:**
  ```bash
  docker compose down
  ```
* **Pilnībā atiestatīt datubāzi un ielādēt svaigus parauga datus:**
  ```bash
  docker compose down -v && docker compose up -d
  ```

---

## Manuālā uzstādīšana (Bez Docker) ##

### Nepieciešamā nodrošinājuma prasības ###

* **PHP 8.5** (`php8.5-cli`, `php8.5-fpm`, `php8.5-curl`, `php8.5-gd`, `php8.5-igbinary`, `php8.5-intl`, `php8.5-mbstring`, `php8.5-memcached`, `php8.5-msgpack`, `php8.5-mysql`, `php8.5-readline`, `php8.5-xml`, `php8.5-zip`)
* **Nginx** (vai Apache)
* **MariaDB** / **MySQL**
* **Memcached**

### 1. Koda lejupielāde un submoduļu atjaunināšana ###

```bash
git clone git@github.com:Mad182/exs-lv.git && cd exs-lv
git submodule update --init --recursive
```

### 2. Repozitorija un paku uzstādīšana (Debian 12 / Ubuntu) ###

Pievieno sury.org PHP krātuvi un uzstādi PHP 8.5 un pārējās servisa pakotnes:

```bash
sudo apt update
sudo apt install -y lsb-release ca-certificates curl gnupg
curl -fsSL https://packages.sury.org/php/apt.gpg | sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/debsuryorg-archive.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list

sudo apt update
sudo apt install -y php8.5-cli php8.5-common php8.5-curl php8.5-fpm php8.5-gd \
                    php8.5-igbinary php8.5-intl php8.5-mbstring php8.5-memcached \
                    php8.5-msgpack php8.5-mysql php8.5-readline php8.5-xml php8.5-zip \
                    mariadb-server memcached nginx
```

### 3. Datu bāzes izveide un importēšana ###

Izveido datubāzi un lietotāju MySQL un importē shēmu:

```bash
mysql -u root -p -e "CREATE DATABASE exs; CREATE USER 'exs'@'localhost' IDENTIFIED BY 'parole'; GRANT ALL PRIVILEGES ON exs.* TO 'exs'@'localhost';"
mysql -u exs -p exs < dev-draza/schema.sql
mysql -u exs -p exs < dev-draza/cat.sql
```

### 4. Konfigurācijas faila izveide un ceļu atjaunināšana ###

```bash
cp exs.lv/configdb.sample.php exs.lv/configdb.php
sed -i "s|/var/www/exs-lv|$PWD|g" exs.lv/configdb.php
```

Šī komanda automātiski iestata absolūtos ceļus (`ROOT_PATH`, `CORE_PATH`, `LIB_PATH` u.c.) uz pašreizējo projekta direktoriju. Nepieciešamības gadījumā failā `exs.lv/configdb.php` norādi atbilstošus datubāzes pieslēguma datus.

### 5. Nginx konfigurācija vietējai izstrādei ###

Izmanto izstrādes konfigurāciju no `dev-draza/nginx-dev.conf`:

```bash
sudo cp dev-draza/nginx-dev.conf /etc/nginx/sites-available/exs-dev
sudo ln -s /etc/nginx/sites-available/exs-dev /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Papildu rīki un informācija ##

### Attēlu apstrādes rīki (img.exs.lv) ###

Pilnvērtīgai `img.exs.lv` darbībai un bildes optimizācijai ieteicams uzstādīt:
* `imagemagick`
* `advancecomp`
* `pngcrush`
* `optipng`
* `jpegoptim`

```bash
sudo apt install -y imagemagick advancecomp pngcrush optipng jpegoptim
```

### SSL Sertifikāts (Let's Encrypt production vidē) ###

```bash
certbot certonly --webroot -w /home/www/exs.lv -d exs.lv -d www.exs.lv -d coding.lv -d www.coding.lv -d lol.exs.lv -d rs.exs.lv -d runescape.exs.lv -w /home/www/m.exs.lv -d m.coding.lv -d m.exs.lv -d mlol.exs.lv -d mrs.exs.lv -w /home/www/api.exs.lv -d api.exs.lv -d android.exs.lv -w /var/www/munin -d munin.exs.lv -w /home/www/img.exs.lv -d img.exs.lv -w /var/www/gif-avatars.com/app/webroot -d gif-avatars.com -d www.gif-avatars.com
```
