# News Aggregator API

A robust Laravel-based REST API for aggregating news articles from multiple Moroccan news sources with automated scraping capabilities.

## Features

- **Multi-source News Aggregation**: Scrape articles from multiple news sources (Hespress, Hibapress)
- **Automated Daily Scraping**: Scheduled jobs to automatically fetch new articles daily
- **RESTful API**: Clean, well-documented API endpoints for all operations
- **Authentication**: Secure API authentication using Laravel Sanctum
- **Admin Dashboard**: Protected endpoints for content management and analytics
- **Rate Limiting**: Built-in rate limiting for public endpoints (60 requests/minute)
- **Search Functionality**: Full-text search across articles
- **Category Management**: Organize articles by categories
- **Source Management**: Enable/disable news sources dynamically
- **Queue-based Processing**: Asynchronous scraping jobs for better performance
-Duplicate Detection**: Automatic duplicate article prevention
- **Pagination**: Efficient data retrieval with pagination
- **API Documentation**: OpenAPI/Swagger documentation support

## Tech Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **Queue**: Redis/Database
- **HTTP Client**: Guzzle
- **Web Scraping**: Symfony DomCrawler
- **API Documentation**: L5-Swagger

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or PostgreSQL
- Redis (optional, for queue)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yaakoub0tair/aggregator_api.git
   cd aggregator_api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (authenticated)
- `GET /api/me` - Get current user (authenticated)

### Public Endpoints (Rate Limited: 60/min)

- `GET /api/articles` - List all articles (paginated)
- `GET /api/articles/{slug}` - Get single article by slug
- `GET /api/search` - Search articles
- `GET /api/categories` - List all categories
- `GET /api/sources` - List all news sources

### Admin Endpoints (Authenticated + Admin Role)

#### Articles
- `POST /api/articles` - Create article
- `DELETE /api/articles/{article}` - Delete article

#### Categories
- `POST /api/categories` - Create category
- `PUT /api/categories/{category}` - Update category
- `DELETE /api/categories/{category}` - Delete category

#### Sources
- `POST /api/sources` - Create source
- `PUT /api/sources/{source}` - Update source
- `DELETE /api/sources/{source}` - Delete source

#### Scraping
- `POST /api/scrape/{source}` - Trigger manual scraping for a source
- `GET /api/scrape/logs` - Get scraping job logs

#### Dashboard
- `GET /api/admin/stats` - Get overall statistics
- `GET /api/admin/logs` - Get system logs
- `GET /api/admin/articles-per-source` - Articles count per source
- `GET /api/admin/articles-per-category` - Articles count per category
- `GET /api/admin/recent-articles` - Get recent articles
- `PUT /api/admin/sources/{source}/toggle` - Toggle source active status

## Scraping System

### Manual Scraping

Trigger scraping for a specific source:
```bash
php artisan scrape:all
```

### Automated Daily Scraping

The API includes a built-in scheduler that runs daily at midnight to scrape all active news sources.

To enable the scheduler, add this cron job to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Adding New Sources

1. Create a new scraper class in `app/Services/Scrapers/` extending `BaseScraper`
2. Implement the `scrape()` method
3. Add the source to the database via admin API or seeder
4. The scraper will be automatically picked up by the scraping system

## Authentication

The API uses Laravel Sanctum for authentication. To access protected endpoints:

1. Register or login to get an API token
2. Include the token in the Authorization header:
   ```
   Authorization: Bearer {your_token}
   ```

## Rate Limiting

Public endpoints are rate limited to 60 requests per minute per IP address to prevent abuse.

## Testing

Run the test suite:

```bash
php artisan test
```

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── ScrapeAllSourcesCommand.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── Admin/
│   │   │   │   └── DashboardController.php
│   │   │   └── AuthController.php
│   │   ├── ArticleController.php
│   │   ├── CategoryController.php
│   │   ├── ScrapingController.php
│   │   └── SourceController.php
│   └── Middleware/
├── Jobs/
│   └── ScrapeSourceJob.php
├── Models/
│   ├── Article.php
│   ├── Category.php
│   ├── ScrapingJob.php
│   └── Source.php
└── Services/
    └── Scrapers/
        ├── BaseScraper.php
        ├── HespressScraper.php
        └── HibapressScraper.php
```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or contributions, please open an issue on GitHub.
