# Процесинг оплат (Rafinita SALE)

Невелика бібліотека для **SALE**-запиту до Rafinita API: збір даних платежу, підпис, HTTP-виклик, розбір відповіді та **чотири сценарії** — success, declined, redirect, waiting — через набір обробників і реєстр.

Каркас перевірявся на **Linux**; з Windows зручно працювати через **WSL2** або Docker Desktop.

---

## Технології

| Що | Навіщо |
|----|--------|
| **PHP 8.1+** | `readonly`, enums, строга типізація |
| **Composer** | автозавантаження PSR-4, залежності |
| **Guzzle 7** | HTTP-клієнт до `dev-api.rafinita.com` |
| **PHPUnit 10** | тести процесора з моком шлюзу |
| **webmozart/assert** | перевірки на вході DTO |
| **Docker Compose** | однакове середовище для всіх |

Стиль коду орієнтований на **PSR-12** (відступи, `declare(strict_types=1)`, іменування).

---

## Патерни та підходи

1. **Strategy** — інтерфейс `PaymentHandlerInterface` (`canHandle` + `handle`); конкретні стратегії: `SuccessPaymentHandler`, `FailedPaymentHandler`, `PendingPaymentHandler`, `RedirectPaymentHandler`.
2. **Registry** — `PaymentHandlerRegistry` зберігає список обробників і підбирає перший, який підходить до відповіді шлюзу.
3. **Gateway / anti-corruption** — `PaymentGatewayClient` інкапсулює форму запиту, hash і JSON-відповідь; залежність через `PaymentGatewayClientInterface` (зручно підміняти в тестах).
4. **DTO** — `PaymentRequest`, `SaleResponse`, `ProcessingResult` переносять дані без логіки домену.
5. **Dependency injection (конструктор)** — `PaymentProcessor` отримує реєстр і (опційно) клієнт шлюзу.

Додатково (плюс до ТЗ): **return customer flow** — `ReturnCustomerFlowService` + `ReturnCustomerFlowResult` для callback-нотифікацій.

---

## Ключі та змінні середовища

Скопіюй `.env.example` у `.env` і заповни (або залиш значення з тестового завдання):

| Змінна | Призначення |
|--------|-------------|
| `RAFINITA_PUBLIC_KEY` | Public Key (client_key) |
| `RAFINITA_PASS` | пароль для розрахунку `hash` |
| `RAFINITA_API_URL` | URL endpoint POST |

**З ТЗ (dev, щоб не губити):**

```env
RAFINITA_PUBLIC_KEY=5b6492f0-f8f5-11ea-976a-0242c0a85007
RAFINITA_PASS=d0ec0beca8a3c30652746925d5380cf3
RAFINITA_API_URL=https://dev-api.rafinita.com/post
```

> Не коміть `.env` у репозиторій. Для продакшену ці значення мають бути лише в секретах оточення.

Docker уже підхоплює `.env` через `env_file` у `docker-compose.yml`.

---

## Як запускати

### 1. Перший раз (збірка + залежності)

```bash
make init
```

Це збирає образ і виконує `composer install` у контейнері.

### 2. Запуск контейнера

```bash
make start
```

### 3. Shell усередині контейнера

```bash
make ssh
```

### 4. Тести та приклад

Усередині контейнера (або `docker compose exec app sh`):

```sh
composer test              # PHPUnit
composer example           # php example_usage.php (потрібні змінні RAFINITA_* у .env)
```

Або напряму:

```sh
vendor/bin/phpunit
php example_usage.php
```

### Корисні команди Make

| Команда | Дія |
|---------|-----|
| `make init` | build + `composer install` |
| `make start` | `docker compose up -d` |
| `make stop` | зупинити контейнери |
| `make ssh` | інтерактивна оболонка в сервісі `app` |
| `make composer ARGS="..."` | довільна команда Composer |

### Без Make

```bash
docker compose build
docker compose run --rm app composer install
docker compose up -d
docker compose exec app sh -lc 'vendor/bin/phpunit && php example_usage.php'
```

---

## Структура `src/` (орієнтир ТЗ)

```
src/
├── DTO/
├── Entity/
├── Handlers/
├── Registry/
├── Services/
├── Enum/
└── PaymentProcessor.php
```

Додатково: `SaleResponse.php`, інтерфейс клієнта шлюзу, сервіси return flow.

---

## Приклад використання

Див. **`example_usage.php`**: реєстрація обробників, `PaymentProcessor`, виклик `process()` з масивом сирих даних і вивід `ProcessingResult` (у т.ч. дані для HTML-форми редиректу).
