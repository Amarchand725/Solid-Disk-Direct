<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeveloperController extends Controller
{
    public function storeCategories(){
        $parentCats = [
            "Storage Devices",
            "Memory",
            "Network & Accessories",
            "PC & Servers",
            "Softwares",
            "Motherboards",
            "Printers & Scanners",
            "Power Equipment",
            "Processors",
            "Video Components",
            "Audio Components",
            "Input Devices",
            "Cameras",
            "Cables",
            "System Cooling Parts",
            "Monitors",
            "Computer Accessories",
        ];

        $storageDeviceCategories = [
            "Hard Drives",
            "Storage Accessories",
            "Hard Drive Enclosure",
            "Solid State Drives",
            "Storage Array",
            "Controllers",
            "Tape Drives",
            "Tape Media",
            "Tape Libraries & Autoloaders",
            "USB Flash Drives",
            "Memory Cards",
            "I/O Cards/Panel",
            "Floppy Drives",
            "Network Storage Devices",
            "Host Bus Adapter",
            "Host Channel Adapter",
            "Optical Drives & Burners",
            "Optical Media"
          ];

          $hardDrivesSubCategories = [
            "Server Hard Drives",
            "Desktop Hard Drives",
            "Laptop Hard Drives",
            "Printer Hard Drives",
            "External Hard Drives"
          ];

          $storageAccessoriesSubCategories = [
            "Tray/Caddy",
            "Other Storage Accessories"
          ];

          $controllersSubCategories = [
            "SATA/SAS Controllers",
            "SCSI Controllers",
            "Fibre Channel Controllers",
            "Storage Controllers",
            "Raid Controllers"
          ];
          
          $opticalDrivesAndBurnersSubCategories = [
            "CD & DVD Burners",
            "CD Drives",
            "DVD Drives",
            "Blu-ray Drives",
            "CD DVD & Blu-ray Accessories",
            "Blu-ray Burners",
            "External CD DVD & Blu-ray Drives"
          ];

          $opticalMediaSubCategories = [
            "CD Disk",
            "DVD Disk",
            "Blu-ray Disk"
          ];          
          
          //
          $memoryCategories = [
            "Server Memory",
            "Desktop Memory",
            "Laptop Memory",
            "Printer Memory",
            "Network Memory",
            "Gaming Memory",
            "Cache Memory",
            "Flash Memory",
            "Memory Boards"
          ];

          $networkAndAccessoriesCategories = [
            "Wireless Networking",
            "Switches",
            "Routers",
            "VoIP Gateways",
            "Media Converter",
            "Network Adapters",
            "Network Accessories",
            "Transceivers",
            "Switch Module",
            "Modems",
            "Powerline Network Adapters",
            "Power Over Ethernet Adapters",
            "Network Security & Firewall Devices",
            "IP Phones",
            "Print Servers",
            "Router/Switch Chassis",
            "Terminal Servers",
            "Networking Devices",
            "PABX System"
          ];
          $wirelessNetworkingSubcategories = [
            "Wireless Access Points",
            "Wireless Routers",
            "Antennas",
            "Wireless LAN Controller"
          ];
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
      // $countries = DB::table('countries')
      // ->whereNull('code')
      // ->pluck('name')
      // ->toArray();

      // return $countries;
      
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
}
