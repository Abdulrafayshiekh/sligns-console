<?php

namespace App\Support;

use DateTimeZone;

class Options
{
    public const RTL_LANGUAGES = ['ar', 'he', 'ur'];

    public static function languages(): array
    {
        return [
            'en' => 'English',
            'es' => 'Spanish',
            'pt' => 'Portuguese',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'ko' => 'Korean',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'tr' => 'Turkish',
            'ru' => 'Russian',
            'id' => 'Indonesian',
            'vi' => 'Vietnamese',
            'th' => 'Thai',
            'el' => 'Greek',
            'sv' => 'Swedish',
            'he' => 'Hebrew',
            'fil' => 'Filipino',
            'uk' => 'Ukrainian',
            'ro' => 'Romanian',
            'ur' => 'Urdu',
        ];
    }

    public static function timeZones(): array
    {
        return DateTimeZone::listIdentifiers();
    }

    public static function titles(): array
    {
        return ['Mr.', 'Ms.', 'Mx.', 'Prefer not to say'];
    }

    public static function maritalStatuses(): array
    {
        return ['Single', 'Married', 'In a partnership', 'Separated', 'Divorced', 'Widowed', 'Prefer not to say'];
    }

    public static function industries(): array
    {
        return [
            'Agriculture', 'Construction', 'Consulting', 'Education', 'Energy', 'Finance', 'Government',
            'Healthcare', 'Hospitality', 'Information Technology', 'Legal', 'Manufacturing', 'Media',
            'Non-profit', 'Real Estate', 'Retail', 'Sales', 'Transportation', 'Other',
        ];
    }

    public static function countries(): array
    {
        return [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia',
            'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium',
            'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei',
            'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada',
            'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
            'Côte d\'Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czechia', 'Democratic Republic of the Congo', 'Denmark',
            'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea',
            'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon', 'Gambia', 'Georgia',
            'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
            'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel',
            'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Laos',
            'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
            'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania',
            'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco',
            'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger',
            'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama',
            'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar',
            'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines',
            'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles',
            'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa',
            'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria',
            'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago',
            'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
            'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
            'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
        ];
    }

    public static function ageBand(?int $age): string
    {
        return match (true) {
            $age === null => 'Not given',
            $age < 25 => 'Under 25',
            $age < 35 => '25–34',
            $age < 45 => '35–44',
            $age < 55 => '45–54',
            $age < 65 => '55–64',
            default => '65 and over',
        };
    }
}
