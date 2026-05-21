<?php

declare(strict_types=1);

namespace AccountaBuddy\Handlers\Autocomplete;

use AccountaBuddy\Discord\Types;

class Timezone
{
    // Curated city → TZ database name. Includes aliases for cities not in
    // the TZ database (e.g. Boston, Miami) mapped to their correct zone.
    private static array $cities = [
        // United States — Eastern
        'New York'          => 'America/New_York',
        'Boston'            => 'America/New_York',
        'Philadelphia'      => 'America/New_York',
        'Washington DC'     => 'America/New_York',
        'Baltimore'         => 'America/New_York',
        'Atlanta'           => 'America/New_York',
        'Miami'             => 'America/New_York',
        'Orlando'           => 'America/New_York',
        'Tampa'             => 'America/New_York',
        'Charlotte'         => 'America/New_York',
        'Pittsburgh'        => 'America/New_York',
        'Cleveland'         => 'America/New_York',
        'Columbus'          => 'America/New_York',
        'Cincinnati'        => 'America/New_York',
        'Detroit'           => 'America/Detroit',
        'Indianapolis'      => 'America/Indiana/Indianapolis',
        // United States — Central
        'Chicago'           => 'America/Chicago',
        'Dallas'            => 'America/Chicago',
        'Houston'           => 'America/Chicago',
        'Austin'            => 'America/Chicago',
        'San Antonio'       => 'America/Chicago',
        'Minneapolis'       => 'America/Chicago',
        'Kansas City'       => 'America/Chicago',
        'St. Louis'         => 'America/Chicago',
        'New Orleans'       => 'America/Chicago',
        'Milwaukee'         => 'America/Chicago',
        'Memphis'           => 'America/Chicago',
        'Nashville'         => 'America/Chicago',
        'Oklahoma City'     => 'America/Chicago',
        'Omaha'             => 'America/Chicago',
        // United States — Mountain
        'Denver'            => 'America/Denver',
        'Salt Lake City'    => 'America/Denver',
        'Albuquerque'       => 'America/Denver',
        'Boise'             => 'America/Boise',
        'Phoenix'           => 'America/Phoenix',
        'Tucson'            => 'America/Phoenix',
        // United States — Pacific
        'Los Angeles'       => 'America/Los_Angeles',
        'San Francisco'     => 'America/Los_Angeles',
        'San Diego'         => 'America/Los_Angeles',
        'Seattle'           => 'America/Los_Angeles',
        'Portland'          => 'America/Los_Angeles',
        'Las Vegas'         => 'America/Los_Angeles',
        'Sacramento'        => 'America/Los_Angeles',
        // United States — Other
        'Anchorage'         => 'America/Anchorage',
        'Honolulu'          => 'Pacific/Honolulu',
        // Canada
        'Toronto'           => 'America/Toronto',
        'Ottawa'            => 'America/Toronto',
        'Montreal'          => 'America/Toronto',
        'Halifax'           => 'America/Halifax',
        'Vancouver'         => 'America/Vancouver',
        'Calgary'           => 'America/Edmonton',
        'Edmonton'          => 'America/Edmonton',
        'Winnipeg'          => 'America/Winnipeg',
        'Quebec City'       => 'America/Toronto',
        'St. John\'s'       => 'America/St_Johns',
        // Mexico & Central America
        'Mexico City'       => 'America/Mexico_City',
        'Guadalajara'       => 'America/Mexico_City',
        'Monterrey'         => 'America/Monterrey',
        'Cancún'            => 'America/Cancun',
        'Guatemala City'    => 'America/Guatemala',
        'San José'          => 'America/Costa_Rica',
        'Panama City'       => 'America/Panama',
        // Caribbean
        'San Juan'          => 'America/Puerto_Rico',
        'Santo Domingo'     => 'America/Santo_Domingo',
        'Havana'            => 'America/Havana',
        // South America
        'Bogotá'            => 'America/Bogota',
        'Lima'              => 'America/Lima',
        'Quito'             => 'America/Guayaquil',
        'Caracas'           => 'America/Caracas',
        'Santiago'          => 'America/Santiago',
        'Buenos Aires'      => 'America/Argentina/Buenos_Aires',
        'Montevideo'        => 'America/Montevideo',
        'São Paulo'         => 'America/Sao_Paulo',
        'Rio de Janeiro'    => 'America/Sao_Paulo',
        'Brasília'          => 'America/Sao_Paulo',
        'Manaus'            => 'America/Manaus',
        'La Paz'            => 'America/La_Paz',
        'Asunción'          => 'America/Asuncion',
        // UK & Ireland
        'London'            => 'Europe/London',
        'Dublin'            => 'Europe/Dublin',
        'Edinburgh'         => 'Europe/London',
        'Cardiff'           => 'Europe/London',
        // Western Europe
        'Lisbon'            => 'Europe/Lisbon',
        'Madrid'            => 'Europe/Madrid',
        'Barcelona'         => 'Europe/Madrid',
        'Paris'             => 'Europe/Paris',
        'Lyon'              => 'Europe/Paris',
        'Brussels'          => 'Europe/Brussels',
        'Amsterdam'         => 'Europe/Amsterdam',
        'Berlin'            => 'Europe/Berlin',
        'Munich'            => 'Europe/Berlin',
        'Hamburg'           => 'Europe/Berlin',
        'Frankfurt'         => 'Europe/Berlin',
        'Zurich'            => 'Europe/Zurich',
        'Geneva'            => 'Europe/Zurich',
        'Vienna'            => 'Europe/Vienna',
        'Rome'              => 'Europe/Rome',
        'Milan'             => 'Europe/Rome',
        'Naples'            => 'Europe/Rome',
        'Copenhagen'        => 'Europe/Copenhagen',
        'Stockholm'         => 'Europe/Stockholm',
        'Oslo'              => 'Europe/Oslo',
        'Helsinki'          => 'Europe/Helsinki',
        // Eastern Europe
        'Warsaw'            => 'Europe/Warsaw',
        'Kraków'            => 'Europe/Warsaw',
        'Prague'            => 'Europe/Prague',
        'Budapest'          => 'Europe/Budapest',
        'Bratislava'        => 'Europe/Bratislava',
        'Bucharest'         => 'Europe/Bucharest',
        'Sofia'             => 'Europe/Sofia',
        'Athens'            => 'Europe/Athens',
        'Zagreb'            => 'Europe/Zagreb',
        'Belgrade'          => 'Europe/Belgrade',
        'Sarajevo'          => 'Europe/Sarajevo',
        'Kyiv'              => 'Europe/Kyiv',
        'Minsk'             => 'Europe/Minsk',
        'Vilnius'           => 'Europe/Vilnius',
        'Riga'              => 'Europe/Riga',
        'Tallinn'           => 'Europe/Tallinn',
        // Russia
        'Moscow'            => 'Europe/Moscow',
        'St. Petersburg'    => 'Europe/Moscow',
        'Yekaterinburg'     => 'Asia/Yekaterinburg',
        'Novosibirsk'       => 'Asia/Novosibirsk',
        'Vladivostok'       => 'Asia/Vladivostok',
        // Middle East
        'Istanbul'          => 'Europe/Istanbul',
        'Ankara'            => 'Europe/Istanbul',
        'Tel Aviv'          => 'Asia/Jerusalem',
        'Jerusalem'         => 'Asia/Jerusalem',
        'Beirut'            => 'Asia/Beirut',
        'Amman'             => 'Asia/Amman',
        'Damascus'          => 'Asia/Damascus',
        'Baghdad'           => 'Asia/Baghdad',
        'Riyadh'            => 'Asia/Riyadh',
        'Jeddah'            => 'Asia/Riyadh',
        'Kuwait City'       => 'Asia/Kuwait',
        'Doha'              => 'Asia/Qatar',
        'Abu Dhabi'         => 'Asia/Dubai',
        'Dubai'             => 'Asia/Dubai',
        'Muscat'            => 'Asia/Muscat',
        'Sana\'a'           => 'Asia/Aden',
        'Tehran'            => 'Asia/Tehran',
        'Kabul'             => 'Asia/Kabul',
        // Central & South Asia
        'Tashkent'          => 'Asia/Tashkent',
        'Almaty'            => 'Asia/Almaty',
        'Islamabad'         => 'Asia/Karachi',
        'Karachi'           => 'Asia/Karachi',
        'Lahore'            => 'Asia/Karachi',
        'Mumbai'            => 'Asia/Kolkata',
        'Delhi'             => 'Asia/Kolkata',
        'New Delhi'         => 'Asia/Kolkata',
        'Bangalore'         => 'Asia/Kolkata',
        'Chennai'           => 'Asia/Kolkata',
        'Kolkata'           => 'Asia/Kolkata',
        'Hyderabad'         => 'Asia/Kolkata',
        'Kathmandu'         => 'Asia/Kathmandu',
        'Colombo'           => 'Asia/Colombo',
        'Dhaka'             => 'Asia/Dhaka',
        // Southeast & East Asia
        'Rangoon'           => 'Asia/Rangoon',
        'Yangon'            => 'Asia/Rangoon',
        'Bangkok'           => 'Asia/Bangkok',
        'Hanoi'             => 'Asia/Bangkok',
        'Ho Chi Minh City'  => 'Asia/Ho_Chi_Minh',
        'Phnom Penh'        => 'Asia/Phnom_Penh',
        'Vientiane'         => 'Asia/Vientiane',
        'Jakarta'           => 'Asia/Jakarta',
        'Kuala Lumpur'      => 'Asia/Kuala_Lumpur',
        'Singapore'         => 'Asia/Singapore',
        'Manila'            => 'Asia/Manila',
        'Hong Kong'         => 'Asia/Hong_Kong',
        'Taipei'            => 'Asia/Taipei',
        'Beijing'           => 'Asia/Shanghai',
        'Shanghai'          => 'Asia/Shanghai',
        'Chongqing'         => 'Asia/Shanghai',
        'Guangzhou'         => 'Asia/Shanghai',
        'Shenzhen'          => 'Asia/Shanghai',
        'Seoul'             => 'Asia/Seoul',
        'Busan'             => 'Asia/Seoul',
        'Tokyo'             => 'Asia/Tokyo',
        'Osaka'             => 'Asia/Tokyo',
        'Sapporo'           => 'Asia/Tokyo',
        // Australia & Pacific
        'Sydney'            => 'Australia/Sydney',
        'Melbourne'         => 'Australia/Melbourne',
        'Brisbane'          => 'Australia/Brisbane',
        'Gold Coast'        => 'Australia/Brisbane',
        'Adelaide'          => 'Australia/Adelaide',
        'Perth'             => 'Australia/Perth',
        'Darwin'            => 'Australia/Darwin',
        'Hobart'            => 'Australia/Hobart',
        'Canberra'          => 'Australia/Sydney',
        'Auckland'          => 'Pacific/Auckland',
        'Wellington'        => 'Pacific/Auckland',
        'Christchurch'      => 'Pacific/Auckland',
        'Suva'              => 'Pacific/Fiji',
        'Port Moresby'      => 'Pacific/Port_Moresby',
        // Africa
        'Casablanca'        => 'Africa/Casablanca',
        'Tunis'             => 'Africa/Tunis',
        'Algiers'           => 'Africa/Algiers',
        'Tripoli'           => 'Africa/Tripoli',
        'Cairo'             => 'Africa/Cairo',
        'Alexandria'        => 'Africa/Cairo',
        'Khartoum'          => 'Africa/Khartoum',
        'Addis Ababa'       => 'Africa/Addis_Ababa',
        'Nairobi'           => 'Africa/Nairobi',
        'Dar es Salaam'     => 'Africa/Dar_es_Salaam',
        'Lagos'             => 'Africa/Lagos',
        'Abidjan'           => 'Africa/Abidjan',
        'Accra'             => 'Africa/Accra',
        'Dakar'             => 'Africa/Dakar',
        'Kinshasa'          => 'Africa/Kinshasa',
        'Luanda'            => 'Africa/Luanda',
        'Johannesburg'      => 'Africa/Johannesburg',
        'Cape Town'         => 'Africa/Johannesburg',
        'Durban'            => 'Africa/Johannesburg',
        'Harare'            => 'Africa/Harare',
        'Lusaka'            => 'Africa/Lusaka',
        'Antananarivo'      => 'Indian/Antananarivo',
    ];

    // Shown when the user hasn't typed anything yet
    private static array $defaults = [
        'New York', 'Chicago', 'Denver', 'Los Angeles', 'Honolulu', 'Anchorage',
        'Toronto', 'Vancouver', 'London', 'Paris', 'Berlin', 'Moscow',
        'Dubai', 'Mumbai', 'Singapore', 'Tokyo', 'Sydney', 'Auckland',
    ];

    public static function handle(array $interaction): array
    {
        // Options are nested: data.options[0] is the subcommand, its options contain the focused field
        $query = '';
        $subOptions = $interaction['data']['options'][0]['options'] ?? [];
        foreach ($subOptions as $opt) {
            if (($opt['focused'] ?? false) && $opt['name'] === 'timezone') {
                $query = trim($opt['value'] ?? '');
                break;
            }
        }

        $choices = $query === ''
            ? self::defaultChoices()
            : self::search($query);

        return [
            'type' => Types::APPLICATION_COMMAND_AUTOCOMPLETE_RESULT,
            'data' => ['choices' => $choices],
        ];
    }

    private static function defaultChoices(): array
    {
        $choices = [];
        foreach (self::$defaults as $city) {
            $choices[] = ['name' => $city, 'value' => self::$cities[$city]];
        }
        return $choices;
    }

    private static function search(string $query): array
    {
        $q = mb_strtolower($query);

        $startsWith  = [];
        $contains    = [];

        foreach (self::$cities as $city => $tz) {
            $cityLower = mb_strtolower($city);
            $tzLower   = mb_strtolower(str_replace('_', ' ', $tz));

            if (str_starts_with($cityLower, $q)) {
                $startsWith[] = ['name' => $city, 'value' => $tz];
            } elseif (str_contains($cityLower, $q) || str_contains($tzLower, $q)) {
                $contains[] = ['name' => $city, 'value' => $tz];
            }
        }

        return array_slice(array_merge($startsWith, $contains), 0, 25);
    }
}
