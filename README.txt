````markdown
# Docker Compose – základné príkazy (Laravel projekt)

Toto je stručný zoznam príkazov, ktoré používame pri práci s Laravel projektom v Dockeri.

## Spustenie / vypnutie projektu

Build a spustenie na pozadí (prvýkrát alebo po zmene Dockerfile):
```powershell
docker compose up -d --build
````

Spustenie (keď už image existujú):

```powershell
docker compose up
```

Vypnutie projektu (zastaví a odstráni kontajnery):

```powershell
docker compose down
```

## Inštalácia Laravelu do priečinka `src`

Vytvorenie novej Laravel aplikácie priamo v kontajneri:

```powershell
docker compose exec app composer create-project laravel/laravel .
```

## Bežné Laravel príkazy

Vyčistenie cache:

```powershell
docker compose exec app php artisan cache:clear
```

Migrácie do databázy (s detailným výpisom):

```powershell
docker compose exec app php artisan migrate -v
```

## Fix na Windows: oprava práv pre `storage` a `bootstrap/cache`

Ak Laravel padá na „Permission denied“ (Blade views / cache), spustíme:

```powershell
docker compose exec app bash -lc "mkdir -p storage/framework/views storage/framework/cache storage/logs bootstrap/cache && chmod -R 777 storage bootstrap/cache"
```

```
```
