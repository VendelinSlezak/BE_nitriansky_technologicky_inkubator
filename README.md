# Nitriansky Technologický Inkubátor (Nitra Technology Incubator)

This repository contains the **backend** for the **Nitra Technology Incubator full-stack student project**. This platform aims to serve as a professional bridge between IT students and the corporate world, helping students launch their own startups while providing a interface for university-industry collaboration.

## Key Features:
**Startup Support:** Providing resources and a platform for IT students to establish their own ventures.
**Corporate Integration:** Creating a direct link between IT companies and the university ecosystem.
**Task Collaboration:** Enabling students to gain real-world experience by working on professional tasks provided by partner companies.

## Tech Stack:
* Laravel
* SQL
* Docker

## How to run

```
docker compose up
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan queue:work
```

and backend will be on localhost:8080

### Authors
Vendelín Slezák
Pavol Marko
