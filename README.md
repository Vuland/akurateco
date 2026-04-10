# Rafinita SALE (PHP)

## 1. Встановлення

**Docker**

```bash
cp .env.example .env   # треба заповнити RAFINITA_* константи
make init             
make start
```

---

## 2. Make

| Команда | Дія |
|---------|-----|
| `make init` | `docker compose build` + `composer install` |
| `make start` | `docker compose up -d` |
| `make stop` | `docker compose down` |
| `make restart` | `stop` + `start` |
| `make ssh` | shell у сервісі `app` |
| `make composer ARGS="…"` | `composer …` у контейнері |

---

## 3. Тести та приклад

У контейнері (`make ssh`) або `docker compose exec app sh`:

```bash
composer test     
composer example   
```

Без Composer-скриптів:

```bash
vendor/bin/phpunit
php example_usage.php
```

---

## 4. Підходи та бібліотеки, які використав

- Guzzle 7 — HTTP
- webmozart/assert — валідація в DTO
- Патерн Strategy — окремі обробники під тип відповіді шлюзу
- обробник return customer flow починав накидувати, але не встигав по часу.
- Також позалишав тудушки там, де можна було б поімпрувати, валідація наприклад
