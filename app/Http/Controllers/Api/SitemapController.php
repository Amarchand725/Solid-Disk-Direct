<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\ProductController;

class SitemapController extends Controller
{
    public function index()
    {
        $frontendDomain = config('system.frontend_base_url');

        $sitemaps = [
            "$frontendDomain/sitemap-static.xml",
            "$frontendDomain/sitemap-categories.xml",
            "$frontendDomain/sitemap-brands.xml",
            "$frontendDomain/sitemap-blogs.xml",
        ];

        $chunkSize = 10000;
        $totalProducts = Product::where('status', 1)->count();
        $chunks = ceil($totalProducts / $chunkSize);

        for ($i = 1; $i <= $chunks; $i++) {
            $sitemaps[] = "$frontendDomain/sitemap-products-$i.xml";
        }

        return response()
            ->view('sitemaps.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    public function static()
    {
        $paths = [
            '/', '/login', '/register', '/forgot-password', '/reset-password',
            '/my-account', '/cart', '/checkout', '/track-order',
            '/products/compare', '/products/configurator',
            '/quote-request', '/faq', '/contact-us', '/site-map',
            '/blogs',
        ];

        $policySlugs = [
            'privacy-policy', 'return-policy', 'warranty-info', 'shipping',
            'tax-exempt', 'payment-methods', 'faqs', 'terms-and-conditions'
        ];

        foreach ($policySlugs as $slug) {
            $paths[] = "/policies/{$slug}";
        }

        $urls = collect($paths)->map(function ($path) {
            return $this->makeUrl($path);
        });

        return response()
            ->view('sitemaps.single', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function productChunk($chunk)
    {
        $chunk = (int) $chunk;
        $chunkSize = 10000;
        $offset = ($chunk - 1) * $chunkSize;

        $products = Product::where('status', 1)
            ->offset($offset)
            ->limit($chunkSize)
            ->get();

        $urls = $products->map(function ($product) {
            $trail = [];
            $category_url = '';
            $url = '';
            if ($product->mainCategory) {
                $trail = app(ProductController::class)->getCategoryTrailFromRelations($product->mainCategory);
                $category_url = implode('/', array_column($trail, 'slug'));
                $url = '/products/'.$category_url.'/'. $product->slug;
            }else{
                '/products/'.$product->slug;
            }
            return $this->makeUrl($url, $product->updated_at, '0.9');
        });

        return response()
            ->view('sitemaps.single', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function categories()
    {
        $categories = Category::where('status', 1)->get();

        $urls = $categories->map(function ($category) {
            return $this->makeUrl('/categories/' . $category->slug, $category->updated_at, '0.8');
        });

        return response()
            ->view('sitemaps.single', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function brands()
    {
        $brands = Brand::where('status', 1)->get();

        $urls = $brands->map(function ($brand) {
            return $this->makeUrl('/brands/' . $brand->slug, $brand->updated_at, '0.7');
        });

        return response()
            ->view('sitemaps.single', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function blogs()
    {
        $blogs = Blog::where('status', 1)->get();

        $urls = $blogs->map(function ($blog) {
            return $this->makeUrl('/blogs/' . $blog->slug, $blog->updated_at, '0.6');
        });

        return response()
            ->view('sitemaps.single', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    protected function makeUrl($path, $lastmod = null, $priority = '0.5')
    {
        return [
            'loc' => rtrim(config('system.frontend_base_url'), '/') . $path,
            'lastmod' => ($lastmod ?? now())->toAtomString(),
            'priority' => $priority,
        ];
    }
}
