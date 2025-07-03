<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Fixing duplicate slugs...");

        // Fix duplicates
        DB::table('products')
            ->select('slug')
            ->whereNotNull('slug')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('slug')
            ->each(function ($slug) {
                Product::where('slug', $slug)
                    ->orderBy('id')
                    ->skip(1) // keep first one
                    ->chunkById(100, function ($products) {
                        foreach ($products as $product) {
                            $product->slug = $this->generateUniqueSlug($product->title, $product->id);
                            $product->save();
                        }
                    });
            });

        $this->info("Fixing null slugs...");

        Product::whereNull('slug')->chunkById(100, function ($products) {
            foreach ($products as $product) {
                $product->slug = $this->generateUniqueSlug($product->title, $product->id);
                $product->save();
            }
        });

        $this->info("Slug update completed.");
    }

    protected function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;

        while (
            Product::where('slug', $slug)
                ->when($id, fn($q) => $q->where('id', '!=', $id))
                ->exists()
        ) {
            $slug = "{$originalSlug}-" . Str::lower(Str::random(3));
        }

        return $slug;
    }
}
