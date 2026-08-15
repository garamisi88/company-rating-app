# Cégértékelő minialkalmazás

Egyszerű review-aggregátor modul: a felhasználók véleményt írhatnak cégekről,
a vélemények nyilvánosak, és cégenkénti bontásban összesített statisztika
készül belőlük.

## Mit tud

- **Véleménylista** csillagos értékeléssel, csonkított szöveggel, lapozással
  és négyféle rendezéssel (legfrissebb / legrégebbi / legjobb / leggyengébb)
- **Vélemény beküldése** Symfony Form-mal, szerveroldali validációval
- **Vélemény részletező oldal** külön útvonalon
- **Cégstatisztika** (`/companies`): véleményszám, átlagos értékelés csökkenő
  sorrendben, és 1–5 csillagos eloszlás-diagram cégenként
- **Keresés cégnév alapján** a listán és a statisztika oldalon is
- **Spam-védelem** rejtett honeypot mezővel

## Rendszer követelmények

- **PHP 8.2** vagy újabb, `intl` és `pdo_mysql` kiterjesztéssel
- **Composer 2.x**
- **MySQL 8.x** — a repó ad hozzá Docker Compose konfigurációt
- **Symfony CLI** (opcionális, a `symfony serve` parancshoz)

---

## Telepítés

### 1. Adatbázis elindítása

A `compose.yml` egy MySQL 8.4 konténert ír le, előkészített felhasználóval és
jogosultságokkal:

```bash
docker compose up -d db
docker compose ps          # várd meg a "healthy" állapotot
```

> **Docker nélkül:** az alkalmazás sima Symfony projekt, bármilyen MySQL 8-cal
> működik. Hozz létre egy `app` adatbázist, és írd felül a kapcsolatot egy
> `.env.local` fájlban:
>
> ```dotenv
> DATABASE_URL="mysql://FELHASZNÁLÓ:JELSZÓ@127.0.0.1:3306/app?serverVersion=8.4.0&charset=utf8mb4"
> ```
>
> A megadott felhasználónak `CREATE DATABASE` jogosultsággal kell rendelkeznie,
> mert a tesztek külön adatbázist használnak (lásd a 4. lépést).

### 2. Függőségek telepítése

```bash
composer install
```

### 3. Séma és mintaadatok

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

A fixture-ök hat céget és húsz véleményt töltenek be, szándékosan változatos
értékelésekkel, így a statisztika oldal és az eloszlás-diagram első ránézésre
is értelmes adatot mutat.

### 4. Teszt-adatbázis

A tesztek külön adatbázison futnak (`app_test`), amit egyszer kell létrehozni:

```bash
php bin/console --env=test doctrine:database:create --if-not-exists
php bin/console --env=test doctrine:migrations:migrate -n
```

### 5. Szerver indítása

```bash
symfony serve -d
```

Az alkalmazás a **http://127.0.0.1:8000** címen érhető el.

> Symfony CLI nélkül a beépített PHP szerver is megteszi:
> `php -S 127.0.0.1:8000 -t public`

---

## Tesztek

```bash
php bin/phpunit
```

A `dama/doctrine-test-bundle` minden tesztet tranzakcióba csomagol és a végén
visszagördíti, így a teszt-adatbázist nem kell tisztítani a futtatások között.

| réteg        | osztály                 | mit fed le                                                |
|--------------|-------------------------|-----------------------------------------------------------|
| unit         | `ReviewSortTest`        | a rendezési paraméterek allowlistje                       |
| unit         | `CompanyStatsTest`      | százalékszámítás, kerekítés, típuskonverzió               |
| integrációs  | `ReviewRepositoryTest`  | a repository valódi adatbázison ír és olvas               |
| funkcionális | `ReviewControllerTest`  | beküldés, validáció, honeypot                             |
| funkcionális | `CompanyControllerTest` | **cégek átlag szerint csökkenő sorrendje**, cégnév szűrés |

---

## Kódminőség

```bash
composer cs        # kódstílus ellenőrzése (php-cs-fixer, @Symfony ruleset)
composer cs:fix    # automatikus javítás
composer test      # tesztek
```

---

## Tervezési döntések

A feladat kifejezetten mini alkalmazást kér, ezért
`symfony/skeleton`-ből indultam, és csomagonként húztam be azt, amire valóban
szükség volt. A `symfony/webapp-pack` messengert, mailert, notifiert is hozott
volna, amiknek itt nem lett volna funkciója.

**Saját `TimestampableTrait`-et készítettem**, mivel egy entitáshoz és két mezőhöz a
`stof/doctrine-extensions-bundle` vagy más package aránytalan függőség lett volna. A saját trait
ráadásul engedi, hogy a fixture-ök és a tesztek előre beállítsák a létrehozás
dátumát, enélkül minden rekord a betöltés pillanatának idejét kapná.

---

## Munkaidő napló

| #   | Feladat                                                            | Idő     |
|-----|--------------------------------------------------------------------|---------|
| 0   | Feladat értelmezése, adatmodell és architektúra döntések           | 25 perc |
| 1.0 | Fejlesztői környezet, Docker MySQL, Symfony skeleton, alap layout  | 35 perc |
| 1.1 | Adatmodell: `Review` entitás, migráció, fixture-ök                 | 35 perc |
| 2.1 | Vélemény beküldése: `ReviewType`, validáció, flash üzenet          | 40 perc |
| 2.2 | Véleménylista: repository, csillag-komponens, kártyák              | 45 perc |
| 2.3 | Vélemény részletező oldal                                          | 15 perc |
| 2.4 | Cégstatisztika: aggregált lekérdezés, eloszlás-diagram             | 35 perc |
| 2.5 | Keresés cégnév alapján                                             | 25 perc |
| 2.6 | Extra: lapozás és rendezés                                         | 35 perc |
| -   | **Design finomítás:** Bootstrap testreszabás, csillagok, animációk | 60 perc |
| 3.  | Kódstílus: php-cs-fixer beállítása és futtatása                    | 15 perc |
| 4.  | Tesztek: PHPUnit környezet és a teszt-készlet                      | 45 perc |
| 5.  | README                                                             | 15 perc |

Az alapfeladat a becsült 4–6 órás keretben elkészült. A többletidő a bónusz
pontokra (2.5, 2.6) és az egyedi felületre ment, tételesen elkülönítve.
