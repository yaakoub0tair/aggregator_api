<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="REST API for aggregating Moroccan news from Hespress and Hibapress",
 *     @OA\Contact(email="admin@newsaggregator.com")
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum Bearer token. Enter: Bearer {your_token}"
 * )
 *
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="admin")
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="email", type="string", format="email", example="admin@test.com"),
 *     @OA\Property(property="role_id", type="integer", example=1),
 *     @OA\Property(property="role", ref="#/components/schemas/Role")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryEmbedded",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="سياسة"),
 *     @OA\Property(property="slug", type="string", example="siasa")
 * )
 *
 * @OA\Schema(
 *     schema="SourceEmbedded",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hespress"),
 *     @OA\Property(property="base_url", type="string", example="https://www.hespress.com"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="سياسة"),
 *     @OA\Property(property="slug", type="string", example="siasa"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-22T10:00:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="SourceResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hespress"),
 *     @OA\Property(property="base_url", type="string", example="https://www.hespress.com"),
 *     @OA\Property(property="scraper_class", type="string", example="HespressScraper"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-22T10:00:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="ArticleResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="المغرب يتأهل لكأس العالم"),
 *     @OA\Property(property="slug", type="string", example="almghrb-ytahl-lkas-alalam-abc123"),
 *     @OA\Property(property="summary", type="string", nullable=true, example="ملخص الخبر باللغة العربية"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://www.hespress.com/image.jpg"),
 *     @OA\Property(property="url", type="string", example="https://www.hespress.com/article-123.html"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2026-05-22T10:00:00.000000Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-22T10:00:00.000000Z"),
 *     @OA\Property(property="category", ref="#/components/schemas/CategoryEmbedded"),
 *     @OA\Property(property="source", ref="#/components/schemas/SourceEmbedded")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     @OA\Property(property="first", type="string", example="http://localhost:8000/api/articles?page=1"),
 *     @OA\Property(property="last", type="string", example="http://localhost:8000/api/articles?page=9"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", example="http://localhost:8000/api/articles?page=2")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=9),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=123)
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedArticles",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ArticleResource")),
 *     @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
 * )
 *
 * @OA\Schema(
 *     schema="MessageResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Operation completed successfully")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="email",
 *             type="array",
 *             @OA\Items(type="string", example="The email field is required.")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 *
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Forbidden")
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="No query results for model.")
 * )
 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name","email","password","password_confirmation"},
 *     @OA\Property(property="name", type="string", example="أحمد"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email","password"},
 *     @OA\Property(property="email", type="string", format="email", example="admin@test.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="StoreArticleRequest",
 *     type="object",
 *     required={"title","url"},
 *     @OA\Property(property="title", type="string", example="عنوان المقال بالعربية"),
 *     @OA\Property(property="summary", type="string", nullable=true, example="ملخص المقال"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="url", type="string", example="https://www.hespress.com/article.html"),
 *     @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="source_id", type="integer", nullable=true, example=1)
 * )
 *
 * @OA\Schema(
 *     schema="StoreCategoryRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="رياضة")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateCategoryRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="اقتصاد")
 * )
 *
 * @OA\Schema(
 *     schema="StoreSourceRequest",
 *     type="object",
 *     required={"name","base_url","scraper_class"},
 *     @OA\Property(property="name", type="string", example="Hespress"),
 *     @OA\Property(property="base_url", type="string", example="https://www.hespress.com"),
 *     @OA\Property(property="scraper_class", type="string", example="HespressScraper"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateSourceRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Hibapress"),
 *     @OA\Property(property="base_url", type="string", example="https://ar.hibapress.com"),
 *     @OA\Property(property="scraper_class", type="string", example="HibapressScraper"),
 *     @OA\Property(property="is_active", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *     schema="ScrapingLog",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="scraping_job_id", type="integer", example=1),
 *     @OA\Property(property="articles_found", type="integer", example=83),
 *     @OA\Property(property="articles_saved", type="integer", example=45),
 *     @OA\Property(property="error_message", type="string", nullable=true, example=null)
 * )
 *
 * @OA\Schema(
 *     schema="ScrapingJob",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="source_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", enum={"pending","running","completed","failed"}, example="completed"),
 *     @OA\Property(property="started_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="finished_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="source", ref="#/components/schemas/SourceResource"),
 *     @OA\Property(property="log", ref="#/components/schemas/ScrapingLog")
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedScrapingJobs",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ScrapingJob")),
 *     @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *     schema="StatsTotals",
 *     type="object",
 *     @OA\Property(property="articles", type="integer", example=123),
 *     @OA\Property(property="sources", type="integer", example=2),
 *     @OA\Property(property="categories", type="integer", example=3),
 *     @OA\Property(property="users", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 *     schema="StatsSources",
 *     type="object",
 *     @OA\Property(property="active", type="integer", example=2),
 *     @OA\Property(property="inactive", type="integer", example=0)
 * )
 *
 * @OA\Schema(
 *     schema="StatsScraping",
 *     type="object",
 *     @OA\Property(property="total_jobs", type="integer", example=4),
 *     @OA\Property(property="completed_jobs", type="integer", example=4),
 *     @OA\Property(property="failed_jobs", type="integer", example=0),
 *     @OA\Property(property="last_run_at", type="string", format="date-time", nullable=true, example="2026-05-22T10:07:09.000000Z"),
 *     @OA\Property(property="last_run_source", type="string", nullable=true, example="Hespress"),
 *     @OA\Property(property="last_run_status", type="string", nullable=true, example="completed")
 * )
 *
 * @OA\Schema(
 *     schema="StatsArticles",
 *     type="object",
 *     @OA\Property(property="today", type="integer", example=123),
 *     @OA\Property(property="this_week", type="integer", example=123),
 *     @OA\Property(property="this_month", type="integer", example=123)
 * )
 *
 * @OA\Schema(
 *     schema="StatsResponse",
 *     type="object",
 *     @OA\Property(property="totals", ref="#/components/schemas/StatsTotals"),
 *     @OA\Property(property="sources", ref="#/components/schemas/StatsSources"),
 *     @OA\Property(property="scraping", ref="#/components/schemas/StatsScraping"),
 *     @OA\Property(property="articles", ref="#/components/schemas/StatsArticles")
 * )
 *
 * @OA\Schema(
 *     schema="ArticlesPerSourceItem",
 *     type="object",
 *     @OA\Property(property="source", type="string", example="Hespress"),
 *     @OA\Property(property="articles_count", type="integer", example=78),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="ArticlesPerCategoryItem",
 *     type="object",
 *     @OA\Property(property="category", type="string", example="سياسة"),
 *     @OA\Property(property="slug", type="string", example="siasa"),
 *     @OA\Property(property="articles_count", type="integer", example=25)
 * )
 *
 * @OA\Schema(
 *     schema="ToggleSourceResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Source Hespress is now active"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class Schemas
{
}
