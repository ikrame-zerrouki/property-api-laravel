<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== 1. Create Users ====================
        $this->command->info('🏗️ Creating users...');

        // Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Agent users (3 agents)
        $agent1 = User::create([
            'name' => 'Ahmed Benali',
            'email' => 'ahmed@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        $agent2 = User::create([
            'name' => 'Fatima Zohra',
            'email' => 'fatima@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        $agent3 = User::create([
            'name' => 'Karim Mansouri',
            'email' => 'karim@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        // Visitor user
        $visitor = User::create([
            'name' => 'Visitor User',
            'email' => 'visitor@example.com',
            'password' => Hash::make('password'),
            'role' => 'visiteur'
        ]);

        $this->command->info('✅ Users created: 1 Admin, 3 Agents, 1 Visitor');

        // ==================== 2. Create Properties ====================
        $this->command->info('🏗️ Creating properties...');

        $agents = [$agent1, $agent2, $agent3];
        $cities = ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida', 'Sétif', 'Tizi Ouzou'];
        $types = ['appartement', 'villa', 'terrain', 'magasin', 'bureau'];
        $statuses = ['disponible', 'vendu', 'location'];

        $properties = [];

        // Create 20 properties
        for ($i = 1; $i <= 20; $i++) {
            $agent = $agents[array_rand($agents)];
            $type = $types[array_rand($types)];
            $city = $cities[array_rand($cities)];
            $pieces = rand(1, 6);
            $surface = rand(50, 300);
            $prix = rand(800000, 5000000);
            $status = $statuses[array_rand($statuses)];
            $isPublished = (bool) rand(0, 1);

            // Generate title based on type and details
            $typeNames = [
                'appartement' => 'Appartement',
                'villa' => 'Villa',
                'terrain' => 'Terrain',
                'magasin' => 'Magasin',
                'bureau' => 'Bureau'
            ];
            $typeName = $typeNames[$type];
            $title = "{$typeName} {$pieces} pièces à {$city}";

            $property = Property::create([
                'user_id' => $agent->id,
                'type' => $type,
                'pieces' => $pieces,
                'surface' => $surface,
                'prix' => $prix,
                'ville' => $city,
                'description' => $this->generateDescription($type, $city, $pieces, $surface),
                'statut' => $status,
                'is_published' => $isPublished,
                'title' => $title
            ]);

            $properties[] = $property;
        }

        $this->command->info("✅ " . count($properties) . " properties created");

        // ==================== 3. Create Images ====================
        $this->command->info('🏗️ Creating images...');

        foreach ($properties as $property) {
            // Each property gets 2-4 images
            $numImages = rand(2, 4);

            for ($j = 1; $j <= $numImages; $j++) {
                // Create image record without size field
                Image::create([
                    'property_id' => $property->id,
                    'path' => "properties/{$property->id}/image-{$j}.jpg"
                ]);
            }
        }

        $this->command->info("✅ Images created for all properties");

        // ==================== 4. Create Statistics ====================
        $this->command->info('📊 Summary:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info("Users: " . User::count());
        $this->command->info("Properties: " . Property::count());
        $this->command->info("Images: " . Image::count());
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('   Admin: admin@example.com / password');
        $this->command->info('   Agent: ahmed@example.com / password');
        $this->command->info('   Visitor: visitor@example.com / password');
    }

    /**
     * Generate realistic property description
     */
    private function generateDescription(string $type, string $city, int $pieces, float $surface): string
    {
        $descriptions = [
            'appartement' => [
                "Superbe appartement situé dans le quartier résidentiel de {$city}. Lumineux, spacieux et bien agencé.",
                "Bel appartement avec vue dégagée à {$city}. Proche des commodités et des transports.",
                "Appartement moderne avec finitions de qualité au cœur de {$city}. Idéal pour famille.",
            ],
            'villa' => [
                "Magnifique villa avec jardin à {$city}. Prestige et confort absolu.",
                "Villa de luxe avec piscine à {$city}. Prestations haut de gamme.",
                "Superbe villa contemporaine à {$city}, calme et verdoyante.",
            ],
            'terrain' => [
                "Terrain constructible à {$city} avec vue imprenable. Viabilisé.",
                "Grand terrain à bâtir à {$city}, idéal pour projet immobilier.",
                "Terrain plat et constructible à {$city}, permis de construire obtenu.",
            ],
            'magasin' => [
                "Local commercial à {$city} en plein centre. Très passant.",
                "Magasin avec vitrine à {$city}, prêt à l'emploi.",
                "Local commercial bien situé à {$city}, clientèle assurée.",
            ],
            'bureau' => [
                "Espace bureau moderne à {$city}, open space et salles de réunion.",
                "Bureau lumineux à {$city}, idéal pour profession libérale.",
                "Local professionnel à {$city} avec parking et accès facile.",
            ]
        ];

        $typeDescriptions = $descriptions[$type] ?? $descriptions['appartement'];
        $description = $typeDescriptions[array_rand($typeDescriptions)];

        // Add details
        $description .= " {$pieces} pièces, {$surface}m² habitables. ";

        // Add random feature
        $features = [
            "Chauffage central",
            "Climatisation réversible",
            "Cuisine équipée",
            "Parking inclus",
            "Ascenseur",
            "Balcon",
            "Terrasse",
            "Jardin privatif",
            "Cave",
            "Garde-meubles",
        ];

        $description .= $features[array_rand($features)] . ".";

        return $description;
    }
}
