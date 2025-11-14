# Project Setup

## Installation and Launch

### 1. Clone the project
```bash
git clone 
cd 
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
```bash
cp .env.example .env
```
Edit `.env` if needed (database, ports, etc.)

### 4. Start Laravel Sail
```bash
./vendor/bin/sail up -d
```

### 5. Generate application key
```bash
./vendor/bin/sail artisan key:generate
```

### 6. Migrate database and run seeders
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

