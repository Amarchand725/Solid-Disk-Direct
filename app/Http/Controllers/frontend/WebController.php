<?php

namespace App\Http\Controllers\frontend;

use App\Models\Slider;
use App\Models\AboutUs;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Affiliate;
use App\Models\Corporate;
use App\Models\Compliance;
use App\Models\DataPolicy;
use App\Models\OurFeature;
use App\Models\ReturnPolicy;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{
    public function index(){
        $title = "Home";
        $sliders = Slider::orderBy("created_at","desc")
                    ->where('image', '!=','')
                    ->where('status', 1)
                    ->select(['title', 'sub_title', 'description', 'image'])
                    ->get();
        $popularProducts = Product::orderBy("created_at","desc")
                    ->where('image_url', '!=','')
                    // ->where('status', 1)
                    ->select(['title', 'sku', 'description', 'image_url', 'price'])
                    ->get();
        $products = $this->getProducts();

        return view('frontend.index', get_defined_vars());
    }

    public function getProducts(){
        return Product::where('image_url', '!=', '')
                    ->select(['title', 'sku', 'provider_product_id', 'description', 'image_url', 'sale_price', 'min_price', 'max_price', 'category_name'])
                    ->latest()
                    ->paginate(12);
    }

    public function singleProduct($provider_product_id){
        $title = "Single Product";
        $product = Product::where("provider_product_id", $provider_product_id)
                    ->select(['title', 'provider_product_id', 'description', 'image_url', 'sale_price', 'min_price', 'max_price', 'category_name'])
                    ->first();
        $relatedProducts = Product::where("provider_product_id", '!=', $provider_product_id)->where('category_name', $product->category_name)
                    ->select(['title', 'sku', 'provider_product_id', 'description', 'image_url', 'sale_price', 'min_price', 'max_price', 'category_name'])
                    ->get();
        return view('frontend.partials.single-product', get_defined_vars());
    }

    public function aboutUs(){
        $title = "About Us";
        $aboutUs = AboutUs::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['inspiration_behind_title', 'inspiration_behind_description', 'safer_future', 
                    'safer_future_description', 'commitment_sustainability', 'commitment_sustainability_description'])
                    ->first();
        $features = OurFeature::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['feature', 'sticky_fly_bowl', 'competitive_product'])
                    ->get();
        return view('frontend.partials.about-us', get_defined_vars());
    }

    public function shopNow(){
        $title = "Shop Now";

        $products = $this->getProducts();
        return view('frontend.partials.shop-now', get_defined_vars());
    }

    public function storeProducts($products, $cj){
        $productCategories = ProductCategory::where('status', 1)->pluck('name','id')->toArray();
        foreach ($products as $productData) {

            $priceData = $this->processProductPrice($productData['sellPrice']); // This will return the min, max, and average prices

            $product = Product::updateOrCreate([
                'provider' => 'cj',
                'provider_product_id' => $productData['pid'],
            ], [
                'title' => $productData['productNameEn'],
                'description' => $productData['remark'] ?? null,
                'image_url' => $productData['productImage'],
                'price' => $priceData['averagePrice'], // Use average or minPrice/maxPrice based on your logic
                'min_price' => $priceData['minPrice'], // You can store the min price separately if needed
                'max_price' => $priceData['maxPrice'],
                'sku' => $productData['productSku'] ?? null,
                'raw' => json_encode($productData), // Store the raw data if needed
                'category_name' => $productData['categoryName'],
                'category_id' => $productData['categoryId'],
                'delivery_time' => $productData['deliveryTime'] ?? null, // Store delivery time
            ]);

            $variants  = $cj->getVariants($productData);

            foreach ($variants as $variant) {
                $product->variants()->create([
                    'provider_variant_id' => $variant['vid'],
                    'variant_name' => $variant['variantNameEn'], // e.g., XS, L, Red
                    'variant_value' => $variant['variantKey'], // e.g., XS
                    'variant_sku' => $variant['variantSku'],
                    'variant_price' => $variant['variantSellPrice'],
                    'variant_weight' => $variant['variantWeight'],
                    'variant_properties' => json_encode([
                        'size' => $variant['variantKey'],
                        'dimensions' => $variant['variantStandard'],
                    ]),
                ]);
            }
            sleep(1);
        }
    }

    public function inquiry(){
        $title = "Inquiry";
        $inquiry = Inquiry::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['bulk_order_title', 'bulk_order_description', 'why_partner_title', 'why_partner_description',
                    'get_touch_title', 'get_touch_description'])
                    ->first();
        return view('frontend.partials.inquiry', get_defined_vars());
    }
    public function corporate(){
        $title = "Corporate";
        $corporate = Corporate::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['news_title', 'news_description', 'recent_news_title', 'recent_news_description',
                    'press_inquiry_title', 'press_inquiry_description'])
                    ->first();
        return view('frontend.partials.corporate', get_defined_vars());
    }
    public function affiliate(){
        $title = "Affiliate";
        $affiliate = Affiliate::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['become_an_affiliate_title', 'become_an_affiliate_description', 'how_it_work_title', 'how_it_work_description'])
                    ->first();
        return view('frontend.partials.affiliate', get_defined_vars());
    }
    public function compliance(){
        $title = "Compliance";
        $compliance = Compliance::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['regulations_title', 'regulations_description', 'certifications_title', 'certifications_description'])
                    ->first();
        return view('frontend.partials.compliance', get_defined_vars());
    }
    public function dataPolicy(){
        $title = "Data Policy";
        $data_policy = DataPolicy::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['how_protect_title', 'how_protect_description', 'data_we_collect_title', 
                                        'data_we_collect_description', 'commitment_security_title', 'commitment_security_description'])
                    ->first();
        return view('frontend.partials.data-policy', get_defined_vars());
    }
    public function privacyPolicy(){
        $title = "Privacy Policy";
        $privacy_policy = PrivacyPolicy::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['our_commitment_title', 'our_commitment_description', 'we_collect_title', 'we_collect_description',
                    'use_information_title', 'use_information_description'])
                    ->first();
        return view('frontend.partials.privacy-policy', get_defined_vars());
    }
    public function returnPolicy(){
        $title = "Return Policy";
        $return_policy = ReturnPolicy::orderBy("created_at","desc")
                    ->where('status', 1)
                    ->select(['hassle_free_title', 'hassle_free_description', 'return_condition_title', 
                                        'return_condition_description', 'initiate_return_title', 'initiate_return_description'])
                    ->first();
        return view('frontend.partials.return-policy', get_defined_vars());
    }
    
}
