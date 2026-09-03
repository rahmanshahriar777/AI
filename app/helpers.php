<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Enquiry;

if (! function_exists('generateEnquiryName')) {
    function generateEnquiryName()
    {
        $latest = Enquiry::latest('id')->first();
        $number = $latest ? ($latest->id + 1) : 1;
        $formatted = str_pad($number, 5, '0', STR_PAD_LEFT);
        return 'ENQ-' . $formatted;
    }
}

if (!function_exists('randomenquiryGenerator')) {
    function randomenquiryGenerator()
    {
        do {
            // Generate a random 5-digit number (from 10000–99999)
            $randomNumber = 3 . rand(10000, 99999);

            // Check if this number already exists in the 'enquiry_name' column
            $exists = DB::table('enquiries')->where('slug', $randomNumber)->exists();
        } while ($exists); // Repeat if the number already exists

        return (string) $randomNumber;
    }
}


if (!function_exists('lookup_postcode')) {
    function lookup_postcode(string $postcode)
    {
        $apiKey = config('services.newpostcode.api_key'); // API key from config
        $postcode = strtoupper(str_replace(' ', '', $postcode)); // Normalize postcode

        $url = "https://api.easypostcodes.com/addresses/{$postcode}";

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
                'Accept' => 'application/json',
            ])->get($url);

            $data = [
                'addresses' => [],
                'post_town' => '',
            ];

            if ($response->successful()) {
                $results = $response->json();

                foreach ($results as $result) {
                    if (isset($result['envelopeAddress']['summaryLine'])) {
                        $data['addresses'][] = $result['envelopeAddress']['summaryLine'];
                    }

                    if (empty($data['post_town']) && isset($result['postTown'])) {
                        $data['post_town'] = $result['postTown'];
                    }
                }

                return $data;
            } else {
                Log::error('Postcode API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Postcode lookup exception: ' . $e->getMessage());
            return null;
        }
    }
}


if (! function_exists('lookup_places')) {
    function lookup_places(string $region)
    {
        $apiKey = config('services.postcode.api_key'); // Get from config/services.php
        $region = strtoupper(str_replace(' ', '', $region)); // Clean and format input

        $url = "https://api.os.uk/search/names/v1/find?maxresults=1&query={$region}&key={$apiKey}";

        $response = Http::get($url);

        $data = [
            'county' => '',
            'region' => '',
        ];

        if ($response->successful()) {
            $check = json_decode($response->getBody(), true); // Decode as array

            if (isset($check['error'])) {
                Log::error('Postcode lookup error: ' . $check['error']['message']);
                return null;
            }

            if (!empty($check['results'][0]['GAZETTEER_ENTRY'])) {
                $entry = $check['results'][0]['GAZETTEER_ENTRY'];
                $data['county'] = $entry['COUNTY_UNITARY'] ?? '';
                $data['region'] = $entry['REGION'] ?? '';
                $data['country'] = $entry['COUNTRY'] ?? '';
            }
            return $data;
        } else {
            throw new Exception('Failed to fetch postcode: ' . $response->body());
        }
    }
}

if (!function_exists('formatPoundNumber')) {
    function formatPoundNumber($number, $precision = 0)
    {
        if (!is_numeric($number)) return '£0';

        if ($number < 1000) {
            return '£' . number_format($number, $precision);
        }

        $units = ['', 'K', 'M', 'B', 'T'];
        $power = floor(log($number, 1000));
        $formatted = round($number / pow(1000, $power), $precision);

        return '£' . $formatted . $units[$power];
    }
}

if (!function_exists('beautify_status')) {
    function beautify_status(string $status): string
    {
        // Replace underscores or dashes with spaces and capitalize words
        return ucwords(str_replace(['_', '-'], ' ', $status));
    }
}

if (!function_exists('simple_format_phone_pattern')) {
    function simple_format_phone_pattern(string $countryCode='UK', $is_international=false): string {
        //country code config here

        $pattern = '';
        
        switch($countryCode){
            case 'UK':
                if($is_international){
                    $pattern = '^(?:\+44|0)(?:\s?\(?0?\)?\s?)?(?:\d[\s-]?){9,10}$';
                }
                else{
                    $pattern = '^((?0?\)?\s?)?(?:\d[\s-]?){9,10}$';
                }
                break;
            default:
                $pattern = '';
                break;
        }

        return $pattern;
    }
}

if (!function_exists('simple_format_phone')) {
    function simple_format_phone(string $phone='', string $countryCode='UK', $is_international=false): string {
        //if(empty($phone)) return '';
        //country code config here

        $formated_phone = '';
        
        switch($countryCode){
            case 'UK':
                if(empty(trim($phone))) $phone = '07123456789';
                $formated_phone = simple_format_uk_phone($phone, $is_international);
                break;
            default:
                $formated_phone = '';
                break;
        }

        return $formated_phone;
    }
}

if (!function_exists('simple_format_uk_phone')) {
    function simple_format_uk_phone(string $phone='', $is_international=false): string {
        if(empty($phone)) return '';
        $internation_prefix = '0';
        if($is_international) $internation_prefix = '+44 ';

        // 1. Strip all non-numeric characters (except leading '+')
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // 2. Convert common international prefixes (00 or 44) to '+44'
        if (strpos($phone, '00') === 0) {
            $phone = '+' . substr($phone, 2);
        } elseif (strpos($phone, '44') === 0 && strpos($phone, '+') !== 0) {
            $phone = '+' . $phone;
        }

        // 3. Ensure it starts with +44, if not, assume national and add +44 (removing leading 0)
        if (strpos($phone, '+44') !== 0) {
            if (strpos($phone, '0') === 0) {
                if($is_international){
                    $phone = '+44' . substr($phone, 1);
                }
            } else {
                // Assume invalid or non-UK number if it doesn't match common patterns
                return "";
                //return "Invalid Format";
            }
        }

        // 4. Re-format for display (example: mobile +44 7xxx xxx xxx)
        $pattern = '/^(\d{10,11})$/';
        if($is_international){
            $pattern = '/^\+44(7\d{9})$/';
        }
        if (preg_match($pattern, $phone, $matches)) {
            //dd($matches);
            $phone_formated = $internation_prefix;
            if($is_international){
                $phone_formated .= "{$matches[1][0]}";
            }
            $phone_formated .= "{$matches[1][1]}{$matches[1][2]}{$matches[1][3]} {$matches[1][4]}{$matches[1][5]}{$matches[1][6]} {$matches[1][7]}{$matches[1][8]}{$matches[1][9]}";
            if(isset($matches[1][10])){
                $phone_formated .= "{$matches[1][10]}";
            }
            
            return $phone_formated;

            //return "+44 {$matches[1][0]}{$matches[1][1]}{$matches[1][2]}{$matches[1][3]} {$matches[1][4]}{$matches[1][5]}{$matches[1][6]} {$matches[1][7]}{$matches[1][8]}{$matches[1][9]}";
        }
        // More regex patterns would be needed for landlines (e.g., London 020 numbers)

        return $phone; // Fallback
    }

    // Example usage:
    //echo simple_format_uk_phone('07700900000'); // Output: +44 7700 900 000
}


