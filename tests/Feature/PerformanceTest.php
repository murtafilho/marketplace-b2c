<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Category::factory(10)->create();
        $sellers = User::factory(5)->create();
        
        foreach ($sellers as $seller) {
            SellerProfile::factory()->create([
                'user_id' => $seller->id,
                'status' => 'approved'
            ]);
        }
        
        Product::factory(100)->create();
    }

    public function test_homepage_loads_within_acceptable_time()
    {
        $startTime = microtime(true);
        
        $response = $this->get('/');
        
        $loadTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(2.0, $loadTime, "Homepage should load within 2 seconds, took {$loadTime}s");
    }

    public function test_product_search_performance()
    {
        $startTime = microtime(true);
        
        $response = $this->get('/api/search?q=produto');
        
        $loadTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(1.0, $loadTime, "Search should complete within 1 second, took {$loadTime}s");
    }

    public function test_category_listing_with_products()
    {
        $category = Category::first();
        
        $startTime = microtime(true);
        
        $response = $this->get("/categories/{$category->id}");
        
        $loadTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(1.5, $loadTime, "Category listing should load within 1.5 seconds, took {$loadTime}s");
    }

    public function test_database_query_efficiency()
    {
        DB::enableQueryLog();
        
        $this->get('/');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        $this->assertLessThan(20, $queryCount, "Homepage should execute fewer than 20 queries, executed {$queryCount}");
        
        foreach ($queries as $query) {
            $this->assertLessThan(0.1, $query['time'] / 1000, "Each query should complete within 100ms");
        }
    }

    public function test_n_plus_one_query_prevention()
    {
        DB::enableQueryLog();
        
        $response = $this->get('/api/categories');
        
        $queries = DB::getQueryLog();
        $categoryQueries = array_filter($queries, function($query) {
            return strpos($query['query'], 'categories') !== false;
        });
        
        $this->assertLessThan(5, count($categoryQueries), "Should avoid N+1 queries when loading categories");
    }

    public function test_cache_effectiveness()
    {
        Cache::flush();
        
        $startTime = microtime(true);
        $this->get('/');
        $firstLoadTime = microtime(true) - $startTime;
        
        $startTime = microtime(true);
        $this->get('/');
        $cachedLoadTime = microtime(true) - $startTime;
        
        $this->assertLessThan($firstLoadTime * 0.8, $cachedLoadTime, "Cached requests should be at least 20% faster");
    }

    public function test_large_dataset_pagination_performance()
    {
        Product::factory(500)->create();
        
        $startTime = microtime(true);
        
        $response = $this->get('/api/products?page=1&per_page=20');
        
        $loadTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(0.5, $loadTime, "Paginated product listing should load within 0.5 seconds, took {$loadTime}s");
    }

    public function test_concurrent_user_simulation()
    {
        $users = User::factory(10)->create();
        $loadTimes = [];
        
        foreach ($users as $user) {
            $startTime = microtime(true);
            
            $this->actingAs($user)->get('/dashboard');
            
            $loadTimes[] = microtime(true) - $startTime;
        }
        
        $averageLoadTime = array_sum($loadTimes) / count($loadTimes);
        $maxLoadTime = max($loadTimes);
        
        $this->assertLessThan(1.0, $averageLoadTime, "Average load time should be under 1 second");
        $this->assertLessThan(2.0, $maxLoadTime, "Maximum load time should be under 2 seconds");
    }

    public function test_image_optimization_headers()
    {
        $response = $this->get('/storage/products/sample.jpg');
        
        if ($response->status() === 200) {
            $response->assertHeader('Cache-Control');
            $response->assertHeader('ETag');
        }
    }

    public function test_api_response_compression()
    {
        $response = $this->withHeaders([
            'Accept-Encoding' => 'gzip, deflate'
        ])->get('/api/products');
        
        $response->assertStatus(200);
        
        $contentLength = strlen($response->getContent());
        $this->assertLessThan(100000, $contentLength, "API response should be reasonably sized");
    }

    public function test_database_connection_pooling()
    {
        $connections = [];
        
        for ($i = 0; $i < 10; $i++) {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $connections[] = microtime(true) - $startTime;
        }
        
        $averageConnectionTime = array_sum($connections) / count($connections);
        $this->assertLessThan(0.01, $averageConnectionTime, "Database connections should be fast");
    }

    public function test_memory_usage_within_limits()
    {
        $initialMemory = memory_get_usage(true);
        
        $this->get('/');
        
        $peakMemory = memory_get_peak_usage(true);
        $memoryIncrease = $peakMemory - $initialMemory;
        
        $memoryLimitMB = 64;
        $this->assertLessThan($memoryLimitMB * 1024 * 1024, $memoryIncrease, 
            "Memory usage should not exceed {$memoryLimitMB}MB");
    }

    public function test_session_performance()
    {
        $user = User::first();
        
        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $sessionTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(0.5, $sessionTime, "Session-based requests should be fast");
    }

    public function test_asset_compilation_size()
    {
        $cssPath = public_path('css/app.css');
        $jsPath = public_path('js/app.js');
        
        if (file_exists($cssPath)) {
            $cssSize = filesize($cssPath);
            $this->assertLessThan(500000, $cssSize, "CSS bundle should be under 500KB");
        }
        
        if (file_exists($jsPath)) {
            $jsSize = filesize($jsPath);
            $this->assertLessThan(1000000, $jsSize, "JavaScript bundle should be under 1MB");
        }
    }

    public function test_eager_loading_efficiency()
    {
        DB::enableQueryLog();
        
        $products = Product::with(['category', 'user.sellerProfile'])
            ->take(10)
            ->get();
        
        $queries = DB::getQueryLog();
        
        $this->assertLessThan(4, count($queries), "Eager loading should minimize queries");
        
        foreach ($products as $product) {
            $this->assertNotNull($product->category);
            $this->assertNotNull($product->user);
        }
    }

    public function test_queue_processing_performance()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        
        $startTime = microtime(true);
        
        event(new \App\Events\UserRegistered($user));
        
        $eventTime = microtime(true) - $startTime;
        
        $this->assertLessThan(0.1, $eventTime, "Event dispatching should be fast");
    }
}