# Rafinita SALE (PHP)

## Передмова

- **PHP 8.1** — запитав яку версію php можна було обрати, відповіді швидкої не було, часу було обмаль. Взяв ту, з якою мені зручно працювати.
- **Guzzle 7** — HTTP
- **webmozart/assert** — валідація в DTO
- Патерн **Strategy** — окремі обробники під тип відповіді шлюзу
- обробник **return customer flow** починав накидувати(**ReturnCustomerFlowService**), але не встигав по часу.
- Також позалишав **тудушки** там, де можна було б поімпрувати, валідація наприклад
- Накидав прості тести(**PaymentProcessorTest**), для тесту як обробляються всі типи **PaymentStatus**
- Цей файл з докою вже генерував АІ, тут сорі :), не було часу вже, а хотілося дати зручну інструкцію
- Ну і в цілому не судіть строго, поспішав, щоб встигнути без використання АІ за 4 години. Більше часу — кращий код.


## 1. Встановлення 

**Потрібно:** Git, Make, Docker Engine і плагін Compose (`docker compose`).

### 1.1 Клон і `.env`

```bash
mkdir -p ~/projects
cd ~/projects
git clone https://github.com/Vuland/akurateco.git
cd akurateco
cp .env.example .env
```

Відредагуй `.env` і задай реальні значення `RAFINITA_PUBLIC_KEY`, `RAFINITA_PASS`, `RAFINITA_API_URL` (для `composer example` вони обов’язкові).

```bash
nano .env
```

*(Замість HTTPS можна `git clone git@github.com:Vuland/akurateco.git`, якщо налаштовано SSH.)*

### 1.2 Збірка образу й залежності

```bash
make init
```

### 1.3 Запуск контейнера

```bash
make start
```

### 1.4 Тести й приклад (з хоста, один блок)

```bash
docker compose exec app sh -lc 'composer test && composer example'
```

Або зайти в контейнер і запускати окремо:

```bash
make ssh
```

```bash
composer test
composer example
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
