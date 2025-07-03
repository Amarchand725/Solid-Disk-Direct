<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Brand;
use App\Models\Order;
use App\Models\State;
use App\Models\Policy;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Services\PayarcService;
use App\Mail\OrderConfirmedAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmedCustomer;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;

class DeveloperController extends Controller
{
    public function getBrandsWithProducts(){
      // $brands = Brand::where('is_top', 1)
      //     ->where('status', 1)
      //     ->orderBy('id', 'desc')
      //     ->count();

      // foreach ($brands as $brand) {
      //     $brand->limitedProducts = $brand->limitedProducts()
      //         ->where('status', 1)
      //         ->orderByDesc('id')
      //         ->limit(4)
      //         ->get();
      // }

      $models = Brand::where('is_top', 1)
        ->where('status', 1)
        ->orderByDesc('id')
        ->get()
        ->filter(function ($category) {
            // Get products first
            $products = $category->limitedProducts()
                ->where('status', 1)
                ->orderByDesc('id')
                ->get()
                ->filter(function ($product) {
                    return !empty($product->thumbnail) && Storage::disk('public')->exists($product->thumbnail);
                });
    
            // Only keep brands that have at least 1 product with valid thumbnail
            if ($products->isNotEmpty()) {
                // Set the relation with up to 4 valid products
                $category->setRelation('limitedProducts', $products->take(4)->values());
                return true;
            }
    
            return false; // Exclude brand
        })->values(); // Reindex the result

      return $models;
    }
    public function generateMissingPolicySlugs()
    {
        $policies = Policy::whereNull('slug')->orWhere('slug', '')->get();

        foreach ($policies as $policy) {
            $baseSlug = Str::slug($policy->title);
            $slug = $baseSlug;
            $count = 1;

            while (Policy::where('slug', $slug)->where('id', '!=', $policy->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $policy->slug = $slug;
            $policy->save();
        }

        return "Slugs generated for all policies.";
    }

    public function addCountryCode(){
      $countryCodes = [
        'Afghanistan' => 'AF',
        'Albania' => 'AL',
        'Algeria' => 'DZ',
        'Andorra' => 'AD',
        'Angola' => 'AO',
        'Antigua and Barbuda' => 'AG',
        'Argentina' => 'AR',
        'Armenia' => 'AM',
        'Australia' => 'AU',
        'Austria' => 'AT',
        'Azerbaijan' => 'AZ',
        'Bahamas' => 'BS',
        'Bahrain' => 'BH',
        'Bangladesh' => 'BD',
        'Barbados' => 'BB',
        'Belarus' => 'BY',
        'Belgium' => 'BE',
        'Belize' => 'BZ',
        'Benin' => 'BJ',
        'Bhutan' => 'BT',
        'Bolivia' => 'BO',
        'Bosnia and Herzegovina' => 'BA',
        'Botswana' => 'BW',
        'Brazil' => 'BR',
        'Brunei' => 'BN',
        'Bulgaria' => 'BG',
        'Burkina Faso' => 'BF',
        'Burundi' => 'BI',
        'Cabo Verde' => 'CV',
        'Cambodia' => 'KH',
        'Cameroon' => 'CM',
        'Canada' => 'CA',
        'Central African Republic' => 'CF',
        'Chad' => 'TD',
        'Chile' => 'CL',
        'China' => 'CN',
        'Colombia' => 'CO',
        'Comoros' => 'KM',
        'Congo (Congo-Brazzaville)' => 'CG',
        'Costa Rica' => 'CR',
        'Croatia' => 'HR',
        'Cuba' => 'CU',
        'Cyprus' => 'CY',
        'Czech Republic' => 'CZ',
        'Democratic Republic of the Congo' => 'CD',
        'Denmark' => 'DK',
        'Djibouti' => 'DJ',
        'Dominica' => 'DM',
        'Dominican Republic' => 'DO',
        'Ecuador' => 'EC',
        'Egypt' => 'EG',
        'El Salvador' => 'SV',
        'Equatorial Guinea' => 'GQ',
        'Eritrea' => 'ER',
        'Estonia' => 'EE',
        'Eswatini' => 'SZ',
        'Ethiopia' => 'ET',
        'Fiji' => 'FJ',
        'Finland' => 'FI',
        'France' => 'FR',
        'Gabon' => 'GA',
        'Gambia' => 'GM',
        'Georgia' => 'GE',
        'Germany' => 'DE',
        'Ghana' => 'GH',
        'Greece' => 'GR',
        'Grenada' => 'GD',
        'Guatemala' => 'GT',
        'Guinea' => 'GN',
        'Guinea-Bissau' => 'GW',
        'Guyana' => 'GY',
        'Haiti' => 'HT',
        'Honduras' => 'HN',
        'Hungary' => 'HU',
        'Iceland' => 'IS',
        'India' => 'IN',
        'Indonesia' => 'ID',
        'Iran' => 'IR',
        'Iraq' => 'IQ',
        'Ireland' => 'IE',
        'Israel' => 'IL',
        'Italy' => 'IT',
        'Jamaica' => 'JM',
        'Japan' => 'JP',
        'Jordan' => 'JO',
        'Kazakhstan' => 'KZ',
        'Kenya' => 'KE',
        'Kiribati' => 'KI',
        'Kuwait' => 'KW',
        'Kyrgyzstan' => 'KG',
        'Laos' => 'LA',
        'Latvia' => 'LV',
        'Lebanon' => 'LB',
        'Lesotho' => 'LS',
        'Liberia' => 'LR',
        'Libya' => 'LY',
        'Liechtenstein' => 'LI',
        'Lithuania' => 'LT',
        'Luxembourg' => 'LU',
        'Madagascar' => 'MG',
        'Malawi' => 'MW',
        'Malaysia' => 'MY',
        'Maldives' => 'MV',
        'Mali' => 'ML',
        'Malta' => 'MT',
        'Marshall Islands' => 'MH',
        'Mauritania' => 'MR',
        'Mauritius' => 'MU',
        'Mexico' => 'MX',
        'Micronesia' => 'FM',
        'Moldova' => 'MD',
        'Monaco' => 'MC',
        'Mongolia' => 'MN',
        'Montenegro' => 'ME',
        'Morocco' => 'MA',
        'Mozambique' => 'MZ',
        'Myanmar (Burma)' => 'MM',
        'Namibia' => 'NA',
        'Nauru' => 'NR',
        'Nepal' => 'NP',
        'Netherlands' => 'NL',
        'New Zealand' => 'NZ',
        'Nicaragua' => 'NI',
        'Niger' => 'NE',
        'Nigeria' => 'NG',
        'North Korea' => 'KP',
        'North Macedonia' => 'MK',
        'Norway' => 'NO',
        'Oman' => 'OM',
        'Pakistan' => 'PK',
        'Palau' => 'PW',
        'Palestine' => 'PS',
        'Panama' => 'PA',
        'Papua New Guinea' => 'PG',
        'Paraguay' => 'PY',
        'Peru' => 'PE',
        'Philippines' => 'PH',
        'Poland' => 'PL',
        'Portugal' => 'PT',
        'Qatar' => 'QA',
        'Romania' => 'RO',
        'Russia' => 'RU',
        'Rwanda' => 'RW',
        'Saint Kitts and Nevis' => 'KN',
        'Saint Lucia' => 'LC',
        'Saint Vincent and the Grenadines' => 'VC',
        'Samoa' => 'WS',
        'San Marino' => 'SM',
        'Sao Tome and Principe' => 'ST',
        'Saudi Arabia' => 'SA',
        'Senegal' => 'SN',
        'Serbia' => 'RS',
        'Seychelles' => 'SC',
        'Sierra Leone' => 'SL',
        'Singapore' => 'SG',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Solomon Islands' => 'SB',
        'Somalia' => 'SO',
        'South Africa' => 'ZA',
        'South Korea' => 'KR',
        'South Sudan' => 'SS',
        'Spain' => 'ES',
        'Sri Lanka' => 'LK',
        'Sudan' => 'SD',
        'Suriname' => 'SR',
        'Sweden' => 'SE',
        'Switzerland' => 'CH',
        'Syria' => 'SY',
        'Taiwan' => 'TW',
        'Tajikistan' => 'TJ',
        'Tanzania' => 'TZ',
        'Thailand' => 'TH',
        'Timor-Leste' => 'TL',
        'Togo' => 'TG',
        'Tonga' => 'TO',
        'Trinidad and Tobago' => 'TT',
        'Tunisia' => 'TN',
        'Turkey' => 'TR',
        'Turkmenistan' => 'TM',
        'Tuvalu' => 'TV',
        'Uganda' => 'UG',
        'Ukraine' => 'UA',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States' => 'US',
        'Uruguay' => 'UY',
        'Uzbekistan' => 'UZ',
        'Vanuatu' => 'VU',
        'Vatican City' => 'VA',
        'Venezuela' => 'VE',
        'Vietnam' => 'VN',
        'Yemen' => 'YE',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
        "Aland Islands" => "AX",
        "American Samoa" => "AS",
        "Anguilla" => "AI",
        "Antarctica" => "AQ",
        "Aruba" => "AW",
        "Bermuda" => "BM",
        "Bonaire, Sint Eustatius and Saba" => "BQ",
        "Bouvet Island" => "BV",
        "British Indian Ocean Territory" => "IO",
        "Cape Verde" => "CV", // Also known as Cabo Verde
        "Cayman Islands" => "KY",
        "Christmas Island" => "CX",
        "Cocos (Keeling) Islands" => "CC",
        "Congo" => "CG", // Republic of the Congo
        "Cook Islands" => "CK",
        "Cote D'Ivoire (Ivory Coast)" => "CI",
        "Curaçao" => "CW",
        "Falkland Islands" => "FK",
        "Faroe Islands" => "FO",
        "Fiji Islands" => "FJ", // Same as Fiji
        "French Guiana" => "GF",
        "French Polynesia" => "PF",
        "French Southern Territories" => "TF",
        "Gibraltar" => "GI",
        "Greenland" => "GL",
        "Guadeloupe" => "GP",
        "Guam" => "GU",
        "Guernsey" => "GG",
        "Heard Island and McDonald Islands" => "HM",
        "Hong Kong S.A.R." => "HK",
        "Jersey" => "JE",
        "Kosovo" => "XK", // Not ISO officially, but widely used
        "Macau S.A.R." => "MO",
        "Man (Isle of)" => "IM",
        "Martinique" => "MQ",
        "Mayotte" => "YT",
        "Montserrat" => "MS",
        "Myanmar" => "MM",
        "New Caledonia" => "NC",
        "Niue" => "NU",
        "Norfolk Island" => "NF",
        "Northern Mariana Islands" => "MP",
        "Palestinian Territory Occupied" => "PS",
        "Pitcairn Island" => "PN",
        "Puerto Rico" => "PR",
        "Reunion" => "RE",
        "Saint Helena" => "SH",
        "Saint Pierre and Miquelon" => "PM",
        "Saint-Barthelemy" => "BL",
        "Saint-Martin (French part)" => "MF",
        "Sint Maarten (Dutch part)" => "SX",
        "South Georgia" => "GS", // South Georgia and the South Sandwich Islands
        "Svalbard and Jan Mayen Islands" => "SJ",
        "The Bahamas" => "BS",
        "The Gambia " => "GM",
        "Tokelau" => "TK",
        "Turks and Caicos Islands" => "TC",
        "United States Minor Outlying Islands" => "UM",
        "Vatican City State (Holy See)" => "VA",
        "Virgin Islands (British)" => "VG",
        "Virgin Islands (US)" => "VI",
        "Wallis and Futuna Islands" => "WF",
        "Western Sahara" => "EH",
      ];
      
      foreach($countryCodes as $key=>$countryCode){
        Country::where('name', $key)->update([
          'code' => $countryCode
        ]);
      }

      return 'Added country code total: '.count($countryCodes);
    }

    public function storeBrands(){
      $brands = [
        'HP',
        'Samsung',
        'IBM',
        'Western Digital',
        'Hitachi',
        'Fujitsu',
        'Compaq',
        'Sun',
        'Dell',
        'Lexmark',
        'Lenovo',
        'Cisco',
        'Supermicro',
        'EMC',
        'Digital Equipment Corp (DEC)',
        'Gateway',
        'LSI Logic',
        'Intel',
        'ASUS',
        'Nortel',
        'Panasonic',
        'Eaton Corporation',
        'Fluke Networks',
        'HPE',
        'Belkin',
        'Axis',
        'SIIG',
        'StarTech',
        'Tripp Lite',
        'Matrox',
        'APC',
        'Avocent',
        'Adtran',
        'Ubiquiti Networks',
        'Cables To Go',
        'NVIDIA',
        'Linksys',
        'NetApp',
        'Mellanox',
        'Zebra',
        'Netgear',
        'Juniper',
        'Avaya',
        '3Com',
        'D-Link',
        'Planet Technology',
        'Allied Telesis',
        'Force 10 Networks',
        'Brocade',
        'QLogic',
        'TP-LINK',
        'ELO',
        'Extreme Networks',
        'Aten Technology, Inc.',
        'Iogear',
        'Lantronix',
        'Logitech',
        'TRENDnet',
        'Delta Electronics',
        'Aruba',
        'Teltonika',
        'Fortinet',
        'SonicWall',
        'Advantech',
        'Polycom',
        'QNAP',
        'Epson',
        'Intermec',
        'Canon',
        'LG',
        'CRESTON',
        'StorageTek',
        'Jabra',
        'Philips',
        'Dell EMC',
        'Astec',
        'Enterasys',
        'Siemens',
        'Dialogic',
        'Arista',
        'Broadcom',
        'Fuji Electric',
        'NCR',
        'Transition Networks',
        'Alcatel-Lucent',
        'Huawei',
        'Edge Memory',
        'Foundry Networks',
        'Buffalo',
        'ZyXEL',
        'Ruckus',
        'CABLETRON',
        'EnGenius',
        'Digi international',
        'EDGECORE',
        'Quanta',
        'Black Box',
        'F5 Networks',
        'Comtrol',
        'Motorola',
        'Infortrend',
        'Ciena',
        'H3C',
        'Aten Technology',
        'Marconi',
        'Raritan',
        'Omnitron Systems',
        'ShoreTel',
        'Allied Telesyn',
        'Tenda',
        'Palo Alto Networks',
        'Mikrotik',
        'Moxa',
        'Honeywell',
        'ComNet',
        'Check Point',
        'Sophos',
        'Aerohive',
        'SMC',
        'Mitel Networks',
        'Bay Networks',
        'Citrix',
        'Adic',
        'Infinera',
        'Apex',
        'Milan',
        'Symbol',
        'Riverstone',
        'IMC',
        'Perle',
        'Vertiv',
        'Altronix',
        'Humminbird',
        'Stride',
        'Narpult',
        'CentroPower',
        'EtherWAN',
        'Equinox',
        'Barracuda',
        'WyreStorm',
        'SmartAVI',
        'iPGARD',
        'MCDATA',
        'Silicom',
        'Madge',
        'LevelOne',
        'Audiocodes',
        'Adva Optical',
        'Gigamon',
        'Voltaire',
        'Riverbed',
        'Renesas Electronics',
        'Paradyne',
        'Radisys',
        'Ruijie',
        'MDS',
        'MRD',
        'Hillstone',
        'Hikvision',
        'Dahua',
      ];

      foreach($brands as $brand){
        Brand::create([
          'name' => $brand,
          'description' => $brand,
        ]);
      }

      return 'Brand added successfully.';
    }
    public function addCountryTaxRates(){
      $countriesJson =  '{
                          "US": {
                            "rate": 0.0,
                            "percent": "0%",
                            "type": "Sales Tax"
                          },
                          "CA": {
                            "rate": 0.05,
                            "percent": "5%",
                            "type": "GST"
                          },
                          "GB": {
                            "rate": 0.2,
                            "percent": "20%",
                            "type": "VAT"
                          },
                          "DE": {
                            "rate": 0.19,
                            "percent": "19%",
                            "type": "VAT"
                          },
                          "FR": {
                            "rate": 0.2,
                            "percent": "20%",
                            "type": "VAT"
                          },
                          "IT": {
                            "rate": 0.22,
                            "percent": "22%",
                            "type": "VAT"
                          },
                          "ES": {
                            "rate": 0.21,
                            "percent": "21%",
                            "type": "VAT"
                          },
                          "AU": {
                            "rate": 0.1,
                            "percent": "10%",
                            "type": "GST"
                          },
                          "NZ": {
                            "rate": 0.15,
                            "percent": "15%",
                            "type": "GST"
                          },
                          "JP": {
                            "rate": 0.1,
                            "percent": "10%",
                            "type": "Consumption Tax"
                          },
                          "CN": {
                            "rate": 0.13,
                            "percent": "13%",
                            "type": "VAT"
                          },
                          "IN": {
                            "rate": 0.18,
                            "percent": "18%",
                            "type": "GST"
                          },
                          "RU": {
                            "rate": 0.2,
                            "percent": "20%",
                            "type": "VAT"
                          },
                          "BR": {
                            "rate": 0.17,
                            "percent": "17%",
                            "type": "VAT"
                          },
                          "ZA": {
                            "rate": 0.15,
                            "percent": "15%",
                            "type": "VAT"
                          },
                          "MX": {
                            "rate": 0.16,
                            "percent": "16%",
                            "type": "VAT"
                          },
                          "AR": {
                            "rate": 0.21,
                            "percent": "21%",
                            "type": "VAT"
                          },
                          "SE": {
                            "rate": 0.25,
                            "percent": "25%",
                            "type": "VAT"
                          },
                          "NO": {
                            "rate": 0.25,
                            "percent": "25%",
                            "type": "VAT"
                          },
                          "FI": {
                            "rate": 0.24,
                            "percent": "24%",
                            "type": "VAT"
                          },
                          "NL": {
                            "rate": 0.21,
                            "percent": "21%",
                            "type": "VAT"
                          },
                          "BE": {
                            "rate": 0.21,
                            "percent": "21%",
                            "type": "VAT"
                          },
                          "CH": {
                            "rate": 0.077,
                            "percent": "7.7%",
                            "type": "VAT"
                          },
                          "DK": {
                            "rate": 0.25,
                            "percent": "25%",
                            "type": "VAT"
                          },
                          "PL": {
                            "rate": 0.23,
                            "percent": "23%",
                            "type": "VAT"
                          },
                          "IE": {
                            "rate": 0.23,
                            "percent": "23%",
                            "type": "VAT"
                          },
                          "PT": {
                            "rate": 0.23,
                            "percent": "23%",
                            "type": "VAT"
                          },
                          "AT": {
                            "rate": 0.2,
                            "percent": "20%",
                            "type": "VAT"
                          },
                          "GR": {
                            "rate": 0.24,
                            "percent": "24%",
                            "type": "VAT"
                          },
                          "CZ": {
                            "rate": 0.21,
                            "percent": "21%",
                            "type": "VAT"
                          },
                          "SK": {
                            "rate": 0.2,
                            "percent": "20%",
                            "type": "VAT"
                          },
                          "HU": {
                            "rate": 0.27,
                            "percent": "27%",
                            "type": "VAT"
                          },
                          "TR": {
                            "rate": 0.18,
                            "percent": "18%",
                            "type": "VAT"
                          },
                          "KR": {
                            "rate": 0.1,
                            "percent": "10%",
                            "type": "VAT"
                          },
                          "HK": {
                            "rate": 0.0,
                            "percent": "0%",
                            "type": "None"
                          },
                          "SG": {
                            "rate": 0.09,
                            "percent": "9%",
                            "type": "GST"
                          },
                          "MY": {
                            "rate": 0.06,
                            "percent": "6%",
                            "type": "SST"
                          },
                          "TH": {
                            "rate": 0.07,
                            "percent": "7%",
                            "type": "VAT"
                          },
                          "ID": {
                            "rate": 0.11,
                            "percent": "11%",
                            "type": "VAT"
                          },
                          "PH": {
                            "rate": 0.12,
                            "percent": "12%",
                            "type": "VAT"
                          },
                          "VN": {
                            "rate": 0.1,
                            "percent": "10%",
                            "type": "VAT"
                          },
                          "SA": {
                            "rate": 0.15,
                            "percent": "15%",
                            "type": "VAT"
                          },
                          "AE": {
                            "rate": 0.05,
                            "percent": "5%",
                            "type": "VAT"
                          },
                          "EG": {
                            "rate": 0.14,
                            "percent": "14%",
                            "type": "VAT"
                          }
                        }';

          $countryTaxRates = json_decode($countriesJson, true);
          
          foreach($countryTaxRates as $key=>$countryTaxRate){
            $country = Country::where('code', $key)->first();
            $country->update([
              'rate' => $countryTaxRate['rate'],
              'percent' => $countryTaxRate['percent'],
              'type' => $countryTaxRate['type'],
            ]);
          }

          return 'Updated tax in '. count($countryTaxRates). ' countries successfully';
    }

    public function addStateTaxRates(){
      $us_state_tax_rates_by_name = '{
                                    "California": {"iso2": "US", "rate": 0.0725, "percent": "7.25%", "type": "Sales Tax"},
                                    "New York": {"iso2": "US", "rate": 0.04, "percent": "4%", "type": "Sales Tax"},
                                    "Texas": {"iso2": "US", "rate": 0.0625, "percent": "6.25%", "type": "Sales Tax"},
                                    "Florida": {"iso2": "US", "rate": 0.06, "percent": "6%", "type": "Sales Tax"},
                                    "Illinois": {"iso2": "US", "rate": 0.0625, "percent": "6.25%", "type": "Sales Tax"},
                                    "Washington": {"iso2": "US", "rate": 0.065, "percent": "6.5%", "type": "Sales Tax"},
                                    "Pennsylvania": {"iso2": "US", "rate": 0.06, "percent": "6%", "type": "Sales Tax"}
                                  }';
                    
      $stateTaxRates = json_decode($us_state_tax_rates_by_name, true);
      
      foreach($stateTaxRates as $key=>$stateTaxRate){
            $state = State::where('name', $key)->first();
            $state->update([
              'rate' => $stateTaxRate['rate'],
              'percent' => $stateTaxRate['percent'],
              'type' => $stateTaxRate['type'],
            ]);
          }

          return 'Updated tax in '. count($stateTaxRates). ' states successfully';
    }

    public function linkProductsToAttributes() {
      // 1. Mappings
      $groupAttributes = DB::table('attribute_group_values')
          ->get()
          ->groupBy('attribute_group_id')
          ->map(fn($items) => $items->pluck('attribute_id')->unique()->toArray());

      $attributeValues = DB::table('attribute_values')
          ->get()
          ->groupBy('attribute_id')
          ->map(fn($items) => $items->pluck('id')->toArray());

      $groupNameToId = DB::table('attribute_groups')->pluck('id', 'name'); // 'Memory' => 1
      $categories = DB::table('categories')->get()->keyBy('id'); // id => object
      $categoryRelations = DB::table('category_relations')->get(); // parent_id, child_id

      // 2. Build child → parent map
      $childToParent = [];
      foreach ($categoryRelations as $rel) {
          $childToParent[$rel->child_id] = $rel->parent_id;
      }

      // 3. Find root ancestor name for each category_id
      $categoryIdToRootName = [];

      foreach ($categories as $catId => $category) {
          $currentId = $catId;

          // Walk up to the root
          while (isset($childToParent[$currentId])) {
              $currentId = $childToParent[$currentId];
          }

          $categoryIdToRootName[$catId] = $categories[$currentId]->name ?? null;
      }

      // 4. Category ID → attribute value IDs
      $categoryToAttributeValueIds = [];

      foreach ($categoryIdToRootName as $categoryId => $rootName) {
          if (!$rootName || !isset($groupNameToId[$rootName])) continue;

          $groupId = $groupNameToId[$rootName];
          $attributeIds = $groupAttributes[$groupId] ?? [];

          foreach ($attributeIds as $attributeId) {
              $valueIds = $attributeValues[$attributeId] ?? [];
              foreach ($valueIds as $valId) {
                  $categoryToAttributeValueIds[$categoryId][] = $valId;
              }
          }
      }

      // 5. Link products
      $productCategories = DB::table('category_product')->get(); // product_id, category_id
      $toInsert = [];

      foreach ($productCategories as $pc) {
          $productId = $pc->product_id;
          $categoryId = $pc->category_id;

          $valueIds = $categoryToAttributeValueIds[$categoryId] ?? [];

          foreach ($valueIds as $valueId) {
              $toInsert[] = [
                  'product_id' => $productId,
                  'attribute_value_id' => $valueId,
              ];

              if (count($toInsert) >= 1000) {
                  DB::table('product_attributes')->insertOrIgnore($toInsert);
                  $toInsert = [];
              }
          }
      }

      if (count($toInsert)) {
          DB::table('product_attributes')->insertOrIgnore($toInsert);
      }

      return $toInsert;
  }

    public function getGroupAttribute(){
      $attributeSlug = '128GB';
      $attrValue = AttributeValue::with(['attribute', 'attributeGroup'])->where('value', $attributeSlug)->first();
      $category = Category::where('name', $attrValue->attributeGroup->name)->first();
      return new CategoryResource($category);
      // return $attrValue->attributeGroup;
    }

    public function mailTest(){
      $bool = false;
      $order = Order::first();

      if(!empty($order)){
        //order confirm email
        Mail::to('amarchand.mmc@gmail.com')->send(new OrderConfirmedAdmin($order));
        Mail::to('amarchand.mmc@gmail.com')->send(new OrderConfirmedCustomer($order));
        return 'mail sent successfully';
      }
    }
    public function testSupportEmail(){
      // $data = [
      //   'contact_name' => 'Sales Team',
      //   'product_name' => 'WAP4410N-A' ?? '',
      //   'quantity' => '5' ?? '',
      //   'company_name' => 'ABC' ?? '',
      //   'contact_person' => 'XYZ',
      //   'phone' => '34543543545',
      //   'email' => 'test@gmail.com',
      // ];

      // $emailFrom = 'quote';

      $data = [
        'name' => 'Amar' ?? '',
        'email' => 'test@gmail.com' ?? '',
        'phone' => '234324324' ?? '',
        'subject' => 'Testing subject' ?? '',
        'message' => 'Testing message' ?? '',
      ];
      
      $emailFrom = 'contact-support';
      
      //sending email to support
      sendSupportOrContactEmail($emailFrom, $data);

      return 'support email sent successfully';
    }

    public function pay(PayarcService $payarc)
    {
        $response = $payarc->createPaymentIntent([
            'amount' => 1000, // in cents
            'currency' => 'USD',
            'payment_method' => 'card',
            // add other required fields per Payarc API docs
        ]);

        return response()->json($response);
    }

    public function updateThumbnail()
    {
        // Array of updates: table => [column]
        $updates = [
            'products' => 'thumbnail',
            'sliders' => 'image',
            'banners' => 'banner',
        ];

        // Loop through each table and apply replacements
        foreach ($updates as $table => $column) {
            DB::table($table)
                ->where(function ($query) use ($column) {
                    $query->where($column, 'like', '%.jpg')
                          ->orWhere($column, 'like', '%.jpeg')
                          ->orWhere($column, 'like', '%.png');
                })
                ->update([
                    $column => DB::raw(
                        "REPLACE(REPLACE(REPLACE($column, '.jpeg', '.webp'), '.jpg', '.webp'), '.png', '.webp')"
                    )
                ]);
        }

        return 'All thumbnails/images updated to .webp';
    }

    public function countImageExtensions()
    {
        $folderPath = 'public/uploads/products'; // storage/app/public/uploads/products
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $folderCounts = array_fill_keys($extensions, 0);

        // Get files (non-recursive)
        $files = Storage::files($folderPath);
        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $folderCounts[$ext]++;
            }
        }

        $folderCounts['total_images'] = array_sum($folderCounts);

        // ---------- 2. DB-based thumbnail extension count ----------
        $dbCounts = array_fill_keys($extensions, 0);

        $thumbnails = Product::whereNotNull('thumbnail')->pluck('thumbnail');

        foreach ($thumbnails as $thumb) {
            $ext = strtolower(pathinfo($thumb, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $dbCounts[$ext]++;
            }
        }

        $dbCounts['total_thumbnails'] = array_sum($dbCounts);
        $dbCounts['total_products'] = Product::count();
        $dbCounts['total_products_thumbnail_not_exist'] = $dbCounts['total_products']-$folderCounts['total_images'];

        // ---------- 3. Final structured response ----------
        return response()->json([
            'folder_images' => $folderCounts,
            'database_thumbnails' => $dbCounts,
        ]);
    }

    public function fixDuplicateSlugs($offset = 0, $limit = 50)
    {
        // Step 1: Get all duplicate slugs
        $duplicateSlugs = DB::table('products')
            ->select('slug')
            ->whereNotNull('slug')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('slug')
            ->toArray();
    
        // Step 2: Get only a limited batch of products
        $products = Product::whereIn('slug', $duplicateSlugs)
            ->orderBy('id')
            ->offset($offset)
            ->limit($limit)
            ->select('id', 'title', 'slug')
            ->get();
    
        $updated = 0;
        $updatedProducts = [];
    
        foreach ($products as $product) {
            if (!empty($product)) {
                // $productSlug = Str::slug($product->title).'-'.$product->mpn;
                
                $newSlug = $this->generateUniqueSlug($product->title, $product->id);
                
                Product::where('id', $product->id)->update([
                    'slug' => $newSlug,
                ]);
                
                // $product->refresh();
                // return $product;
                $updated++;
                
                $updatedProducts[] = $product;
            }
        }
        return $updatedProducts;
        // return "Updated {$updated} slugs from offset {$offset}. ";
    }
    
    // Slug generator function
    public function generateUniqueSlug($title, $productId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
    
        while (
            Product::where('slug', $slug)
                ->where('id', '!=', $productId)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }
    
        return $slug;
    }

    public function getProductsMpn(){
      // $offset = 4973; //my system 
      // $limit = 5000;

      // $offset = 9973; //bilal system
      // $limit = 5000;
      
      // $offset = 14973; //hamza system
      // $limit = 5000;

      // $offset = 19973; //hamza system
      // $limit = 3000;
      
      // $offset = 22973; //hamza system
      // $limit = 3000;
      
      // $offset = 25973; //hamza system
      // $limit = 3000;
      
      // $offset = 28973; //hamza system
      // $limit = 3000;
      
      // $offset = 31973; //hamza system
      // $limit = 3000;

      // $offset = 34973; //hamza system
      // $limit = 3000;
      
      // $offset = 37973; //hamza system
      // $limit = 3000;

      // $offset = 40973; //hamza system
      // $limit = 2000;
      
      // $offset = 42973; //hamza system
      // $limit = 3000;
      
      // $offset = 45973; //hamza system
      // $limit = 3000;
      
      // $offset = 48973; //hamza system
      // $limit = 3000;

      // $offset = 51973; //hamza system
      // $limit = 2000;
      
      // $offset = 53973; //hamza system
      // $limit = 3000;
      
      // $offset = 56973; //hamza system
      // $limit = 3000;
      
      // $offset = 59973; //hamza system
      // $limit = 3000;
      
      // $offset = 62973; //hamza system
      // $limit = 3000;
      
      // $offset = 65973; //hamza system
      // $limit = 3000;
      
      // $offset = 68973; //hamza system
      // $limit = 3000;
      
      // $offset = 71973; //hamza system
      // $limit = 3000;
      
      // $offset = 74973; //hamza system
      // $limit = 3000;
      
      // $offset = 77973; //hamza system
      // $limit = 3000;
      
      // $offset = 80973; //hamza system
      // $limit = 3000;
      
      // $offset = 83973; //hamza system
      // $limit = 3000;
      
      // $offset = 86973; //hamza system
      // $limit = 3000;
      
      // $offset = 89973; //hamza system
      // $limit = 3000;
      
      $offset = 92973; //hamza system
      $limit = 3000;

      $moreMpns = Product::offset($offset)->limit($limit)->pluck('mpn')->toArray();
      return $moreMpns;
    }
}
