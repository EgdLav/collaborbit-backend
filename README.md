# Collaborbit Backend

Backend API for Collaborbit — a real-time collaboration platform where users can create projects, communicate, and work together.

## Stack

- Laravel
- MySQL
- Laravel Reverb
- REST API
- Docker
- Nginx
- PHP-FPM

## Features

- Authentication
- User management
- Project management
- Real-time chat
- WebSocket events with Laravel Reverb

## Installation

### Requirements

- Docker
- Docker Compose

Clone the repository:

```bash
git clone github.com/EgdLav/collaborbit-backend
cd collaborbit-backend
````

Create environment file:

```bash
cp .env.example .env
```

Start containers:

```bash
docker compose up -d
```

Install dependencies:

```bash
docker compose exec backend composer install
```

Generate application key:

```bash
docker compose exec backend php artisan key:generate
```

Run migrations:

```bash
docker compose exec backend php artisan migrate
```

Create storage link:

```bash
docker compose exec backend php artisan storage:link
```

## Architecture

```text
                 Frontend
                    |
                    |
              REST API
                    |
                    |
              Laravel App
                    |
        -------------------------
        |                       |
      MySQL              Laravel Reverb
                                |
                          WebSocket Server
```

## Realtime Chat Flow

```text
User A
  |
  | Sends message
  ↓
Laravel Controller
  |
  ↓
Message saved to MySQL
  |
  ↓
Broadcast Event
  |
  ↓
Laravel Reverb
  |
  ↓
WebSocket connection
  |
  ↓
User B receives message
```

## Environment

Configure `.env` based on `.env.example`.

Main settings:

* Database connection
* Application URL
* Reverb configuration
* Broadcasting settings

## Development

Start project:

```bash
docker compose up
```

Stop project:

```bash
docker compose down
```

View logs:

```bash
docker compose logs -f
```

## Related Repository

Frontend: https://github.com/EgdLav/collaborbit-frontend

[Backend](https://github.com/EgdLav/collaborbit-backend) | [Frontend](https://github.com/EgdLav/collaborbit-frontend) | [Live Demo](https://github.com/EgdLav/collaborbit-frontend)
