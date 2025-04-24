<p align="center">
      <img src="https://github.com/Designblue-Manila/CLI-api/assets/141099415/528547d4-5d54-47ea-8da9-0ef193007fff" />
</p>
<h1 align="center"> Aboitiz Infra Capital (AIC) API</h1>

## Introduction
This project serves as backend for the website of Aboitiz Infra Capital (AIC)

## Branches
- **MAIN**: Production branch. This branch contains the stable and tested code for production.
- **DEV**: Staging branch. All development work should be done in this branch.


## Getting Started
### Prerequisites
Make sure you have the following installed on your system:

- PHP 8.1
- Composer
- Laravel 9
- MySQL



### Installation

Clone the repository :
```bash
git clone git@github.com:Designblue-Manila/aic-api.git
```
    
Install dependencies :
```bash
composer install
```

Create a copy of the .env.example file and rename it to .env. Update the database configuration and other settings as needed.

```bash
cp .env.example .env
```

Generate application and passport key:
```bash
php artisan key:generate
php artisan passport:keys
```

Dropping all tables and Creating migration table.
```bash
artisan migrate:fresh
```

Populate the database.
```bash
php artisan db:seed
```

Encrypt database table.
```bash
php artisan passport:install
```


