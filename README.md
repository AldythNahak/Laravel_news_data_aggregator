# 📘 `Laravel_news_data_aggregator` Simple Backend Project Documentation

This is the backend implementation for a **news aggregator** application built with **PHP** and **Laravel**. The system is responsible for fetching articles from multiple third-party news sources (**NewsAPI**, **New York Times**, **The Guardian**), storing them in a local database (**MySQL**), and serving them via a clean, filterable API endpoint.

User authentication has been explicitly removed, making all API endpoints publicly accessible.

---

## 🧩 Features
- Data Aggregation: Fetches and normalizes articles from multiple external APIs (e.g., NewsAPI, New York Times, The Guardian).
- Scheduled Updates: Uses Laravel's scheduler to regularly update the local article database.
- Filterable API: Provides a single, powerful API endpoint to search and filter articles by keyword, date, category, and source.

---

## 🚀 Requirements

- PHP (8.1+)
- Composer
- A Relational Database (MySQL)
- API key: [NewsAPI](https://newsapi.org/) | [New York Times](https://developer.nytimes.com/apis) | [The Guardian](https://open-platform.theguardian.com/)
---

## ⚙️ Configuration
1. Clone the Repository:

```bash
# Clone the repo
git clone https://github.com/AldythNahak/Laravel_news_data_aggregator.git
cd Laravel_news_data_aggregator
```

2. Install Dependencies:
```bash
composer install
```

3. Configure Environment:
- Copy the example environment file: ``cp .env.example .env``
- Set your database credentials (``DB_*`` variables).
- Set your API keys for all configured news sources (e.g., ``NEWSAPI_KEY, NYTAPI_KEY, THEGUARDIANAPI_KEY``).

4. Generate Application Key:
```bash
php artisan key:generate
```

5. Run Migrations:
```bash
php artisan migrate
```

## 📅 Data Aggregation and Scheduling
The application uses an Artisan command to fetch data from all integrated news sources and update the local database.

1. Manual Execution:
```bash
php artisan app:aggregate-news
```

2. Automated Execution:
The command is scheduled to run periodically using Laravel's task scheduler (configured in app/Console/Kernel.php). For a production environment, ensure your system's cron job is set up to call the scheduler every 30 minutes:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📄 API Documentation
The news aggregator provides one primary endpoint for retrieving and filtering articles.
Endpoint: ``GET /api/articles``

Retrieves a paginated list of articles from the database, allowing for multiple filtering options.

| Parameter | Type | Optional? | Description | Example Value |
|:-----------|:------:|:----------:|:-------------|:---------------|
| `search` | `string` | ✅ | Keywords to search against the article title and content | `"Indonesia"` |
| `sources` | `string` | ✅ | source names of the news (source_name in DB) | `NewsAPI`, `NewYorkTimesAPI`, `TheGuardianAPI` |
| `category` | `string` | ✅ | category/sections of the news | `business`, `entertainment`, `general`, `health`, `science`, `sports`, `technology` |
| `date` | `string` | ✅ | Filters articles published on a specific date (YYYY-MM-DD) | `2025-10-14` |
| `page` | `integer` | ✅ | pecifies the page number for pagination. (Default: 1) | `2` |


### Example Request (with all filters)
Retrieves the second page of articles published today from "NewsAPI" and "NYT", searching for "AI" within the "Technology" category. 
```bash
api/articles?search=president&sources=NewsAPI,NewYorkTimesAPI&date=2025-10-14
```
Example Success Response (JSON)
```bash
{
    "current_page": 1,
    "data": [
        {
            "id": 199,
            "title": "North Carolina Republicans heed Trump’s call to redraw congressional map - The Washington Post",
            "content": "North Carolinas Republican-led legislature said Monday that it will soon begin work on a new congressional map that could yield another Republican-leaning district in the state.\r\nPresident Trump earn… [+280 chars]",
            "author": "Alec Dent",
            "source_name": "NewsAPI",
            "category": "General",
            "url": "https://www.washingtonpost.com/politics/2025/10/13/north-carolina-redistricting-map-trump/",
            "image_url": "https://www.washingtonpost.com/wp-apps/imrs.php?src=https://arc-anglerfish-washpost-prod-washpost.s3.amazonaws.com/public/ZJUF46DFTV7RYWCH7Y32QIALDY_size-normalized.jpg&w=1440",
            "published_at": "2025-10-14T04:16:00.000000Z",
            "created_at": "2025-10-15T08:52:22.000000Z",
            "updated_at": "2025-10-15T08:52:22.000000Z"
        },
        // ... more article objects
    ],
    "first_page_url": "http://localhost:8000/api/articles?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/articles?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "page": null,
            "active": false
        },
        {
            "url": "http://localhost:8000/api/articles?page=1",
            "label": "1",
            "page": 1,
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "page": null,
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://localhost:8000/api/articles",
    "per_page": 20,
    "prev_page_url": null,
    "to": 9,
    "total": 9
}
```

## 🧑‍💻 Author

**Aldyth Nahak**  
[LinkedIn](https://linkedin.com/in/aldythnahak) | [GitHub](https://github.com/AldythNahak)

---

## ⭐️ Contribute or Follow

Feel free to fork, clone, or star this repo if you find it helpful!
