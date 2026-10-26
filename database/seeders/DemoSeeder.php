<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\SuiteApp;
use App\Models\AppActivity;
use App\Models\Company;
use App\Models\User;
use App\Support\Options;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    private const SUBJECTS = ['Confidence', 'Gratitude', 'Focus', 'Health', 'Relationships', 'Prosperity', 'Purpose'];

    private const GOALS = ['Run a 10K', 'Read 12 books', 'Save 5,000', 'Launch a side business', 'Meditate daily', 'Learn Spanish'];

    private const AREAS = ['Career', 'Wealth', 'Health', 'Relationships', 'Mindset'];

    private const PROFILES = ['active', 'active', 'active', 'idle', 'inactive', 'new'];

    public function run(): void
    {
        $companies = [
            ['Proctor Gallagher', '#1d4ed8', 'proctorgallagher.test', 'United States', 'Phoenix', 'America/Phoenix', 'Jordan Lee', 'en'],
            ['Johnstone and Murphy Consulting', '#0f766e', 'johnstonemurphy.test', 'United Kingdom', 'London', 'Europe/London', 'Sam Murphy', 'en'],
        ];

        foreach ($companies as [$name, $color, $domain, $country, $city, $timeZone, $contact, $language]) {
            $company = Company::create([
                'name' => $name,
                'slug' => Company::uniqueSlug($name),
                'console_language' => $language,
                'interface_color' => $color,
                'contact_person' => $contact,
                'preferred_language' => $language,
                'about' => fake()->paragraph(),
                'addresses' => [fake()->streetAddress().', '.$city],
                'phones' => [fake()->numerify('+1 (###) ###-####')],
                'emails' => ['hello@'.$domain],
                'city' => $city,
                'country' => $country,
                'time_zone' => $timeZone,
                'text_opt_in' => true,
                'status' => Company::ACTIVE,
            ]);

            $company->users()->create([
                'role' => Role::Company,
                'name' => $contact,
                'email' => 'admin@'.$domain,
                'password' => 'password',
            ]);

            foreach (range(1, 3) as $number) {
                $coach = $company->users()->create([
                    'role' => Role::Coach,
                    'name' => fake()->firstName().' '.fake()->lastName(),
                    'email' => 'coach'.$number.'@'.$domain,
                    'password' => 'password',
                    'phone' => fake()->numerify('+1 (###) ###-####'),
                    'address' => fake()->streetAddress().', '.$city,
                    'country' => fake()->randomElement([$country, 'Canada', 'Australia']),
                ]);

                foreach (self::PROFILES as $index => $profile) {
                    $this->seedClient($company, $coach, $profile, $index === 0 && $number < 3);
                }

                $this->seedMessages($coach);
            }
        }
    }

    private function seedClient(Company $company, User $coach, string $profile, bool $birthdaySoon): void
    {
        $birthday = $birthdaySoon
            ? today()->addDays(fake()->numberBetween(0, 5))->subYears(fake()->numberBetween(28, 55))
            : Carbon::instance(fake()->dateTimeBetween('-65 years', '-22 years'));

        $client = $company->users()->create([
            'role' => Role::Client,
            'coach_id' => $coach->id,
            'name' => fake()->firstName().' '.fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+1 (###) ###-####'),
            'birthday' => $birthday,
            'title' => fake()->randomElement(Options::titles()),
            'marital_status' => fake()->randomElement(Options::maritalStatuses()),
            'age' => $birthday->age,
            'occupation' => fake()->jobTitle(),
            'industry' => fake()->randomElement(Options::industries()),
            'country' => fake()->randomElement(['United States', 'Canada', 'United Kingdom', 'Australia', 'Pakistan', 'Germany', 'Mexico']),
            'language' => fake()->randomElement(['en', 'en', 'es', 'fr', 'de', 'ur']),
            'coach_viewed_at' => fake()->randomElement([null, now()->subDays(1), now()->subDays(3), now()->subDays(9), now()->subDays(20)]),
        ]);

        $lastDay = match ($profile) {
            'active' => fake()->numberBetween(0, 3),
            'idle' => fake()->numberBetween(8, 12),
            'inactive' => fake()->numberBetween(16, 40),
            default => null,
        };

        if ($lastDay !== null) {
            $this->seedActivity($client, fake()->numberBetween(150, 330), $lastDay);
        }

        foreach (range(1, fake()->numberBetween(1, 3)) as $i) {
            $coach->authoredNotes()->create([
                'client_id' => $client->id,
                'noted_on' => today()->subDays(fake()->numberBetween(1, 60)),
                'body' => fake()->randomElement([
                    'Talked about morning routine. Wants to journal before work.',
                    'Feeling stuck on the savings goal. Suggested smaller weekly targets.',
                    'Great energy this week. Celebrated finishing the first script.',
                    'Asked about Lotus sessions for sleep. Recommended evening sessions.',
                    'Needs encouragement. Follow up on Friday.',
                ]),
            ]);
        }
    }

    private function seedActivity(User $client, int $firstDay, int $lastDay): void
    {
        $goals = collect(fake()->randomElements(self::GOALS, 3))
            ->mapWithKeys(fn ($goal) => [$goal => [fake()->numberBetween(0, 40), fake()->randomFloat(2, 0.15, 0.6)]]);
        $target = fake()->randomFloat(1, 5, 8);
        $mood = fake()->randomFloat(1, 3, 6);
        $rows = [];
        $now = now();

        for ($day = $firstDay; $day >= $lastDay; $day--) {
            $date = today()->subDays($day)->toDateString();
            $used = false;

            if (fake()->boolean(55)) {
                $mood = max(1, min(10, $mood + 0.25 * ($target - $mood) + fake()->randomFloat(1, -1.2, 1.2)));
                $rows[] = $this->row($client, SuiteApp::Momentum, $date, value: round($mood, 1), now: $now);
                $used = true;
            }

            if (fake()->boolean(40)) {
                $rows[] = $this->row($client, SuiteApp::Scintilla, $date, label: fake()->randomElement(self::SUBJECTS), quantity: fake()->numberBetween(1, 4), now: $now);
                $used = true;
            }

            if (fake()->boolean(35)) {
                $sessions = fake()->numberBetween(1, 2);
                $rows[] = $this->row($client, SuiteApp::Lotus, $date, quantity: $sessions, value: $sessions * fake()->numberBetween(10, 35), now: $now);
                $used = true;
            }

            if (fake()->boolean(20)) {
                $goal = $goals->keys()->random();
                [$start, $speed] = $goals[$goal];
                $progress = min(100, $start + ($firstDay - $day) * $speed + fake()->numberBetween(-3, 3));
                $rows[] = $this->row($client, SuiteApp::TargetPro, $date, label: $goal, value: max(0, round($progress)), now: $now);
                $used = true;
            }

            if (fake()->boolean(12)) {
                $shared = fake()->boolean(35);
                $rows[] = $this->row(
                    $client,
                    SuiteApp::ScriptBuilder,
                    $date,
                    label: fake()->randomElement(self::AREAS),
                    quantity: 1,
                    content: $shared ? 'I am so happy and grateful now that '.lcfirst(fake()->sentence(12)) : null,
                    shared: $shared,
                    now: $now,
                );
                $used = true;
            }

            if ($day === $lastDay && ! $used) {
                $rows[] = $this->row($client, SuiteApp::Momentum, $date, value: round($mood, 1), now: $now);
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            AppActivity::insert($chunk);
        }
    }

    private function row(
        User $client,
        SuiteApp $app,
        string $date,
        ?string $label = null,
        int $quantity = 0,
        ?float $value = null,
        ?string $content = null,
        bool $shared = false,
        ?Carbon $now = null,
    ): array {
        return [
            'client_id' => $client->id,
            'app' => $app->value,
            'activity_date' => $date,
            'label' => $label,
            'quantity' => $quantity,
            'value' => $value,
            'content' => $content,
            'shared' => $shared,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedMessages(User $coach): void
    {
        foreach ($coach->clients()->inRandomOrder()->take(2)->get() as $client) {
            $coach->sentMessages()->create([
                'client_id' => $client->id,
                'channel' => 'email',
                'subject' => 'Checking in',
                'body' => 'Hi '.$client->name.', how are your sessions going this week?',
            ]);
        }
    }
}
