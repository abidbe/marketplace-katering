<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceSeeder extends Seeder
{

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@market.test'],
            ['name' => 'Admin Marketplace', 'password' => Hash::make('password'), 'role' => 'admin', 'is_active' => true]
        );

        $dewa = User::updateOrCreate(
            ['email' => 'dewa@catering.test'],
            [
                'name' => 'Dewa', 'password' => Hash::make('password'),
                'role' => 'merchant', 'is_active' => true,
                'company_name' => 'Dewa Catering', 'city' => 'Jakarta',
                'address' => 'Jl. Merdeka Raya No. 1, Jakarta Selatan',
                'phone' => '0812-3456-7890',
                'description' => 'Katering harian kantor dengan menu rumahan, halal, dan tepat waktu.',
            ]
        );

        $sari = User::updateOrCreate(
            ['email' => 'sari@catering.test'],
            [
                'name' => 'Sari', 'password' => Hash::make('password'),
                'role' => 'merchant', 'is_active' => true,
                'company_name' => 'Sari Rasa Bandung', 'city' => 'Bandung',
                'address' => 'Jl. Braga No. 21, Bandung',
                'phone' => '0821-9876-5432',
                'description' => 'Prasmanan dan nasi liwet khas Sunda untuk acara kantor.',
            ]
        );

        $menuDewa = [
            ['name' => 'Nasi Box Ayam Bakar', 'category' => 'Nasi Kotak', 'price' => 25000, 'description' => 'Nasi putih, ayam bakar, lalapan, sambal, dan teh botol.'],
            ['name' => 'Nasi Box Rendang', 'category' => 'Nasi Kotak', 'price' => 28000, 'description' => 'Nasi putih, rendang sapi pedas manis, sayur nangka.'],
            ['name' => 'Mie Goreng Spesial', 'category' => 'Mie', 'price' => 18000, 'description' => 'Mie goreng dengan telur, ayam suwir, dan pangsit goreng.'],
            ['name' => 'Snack Box Premium', 'category' => 'Snack Box', 'price' => 15000, 'description' => 'Risoles, dadar gulung, pastel, dan air mineral 330ml.'],
        ];

        $menuSari = [
            ['name' => 'Nasi Liwet Komplit', 'category' => 'Nasi Kotak', 'price' => 30000, 'description' => 'Nasi liwet, ayam goreng, tahu tempe, sambal dadak, dan lalapan.'],
            ['name' => 'Paket Prasmanan Sunda', 'category' => 'Prasmanan', 'price' => 45000, 'description' => 'Prasmanan 7 lauk khas Sunda untuk minimal 20 orang.'],
        ];

        foreach ($menuDewa as $m) {
            Menu::updateOrCreate(['user_id' => $dewa->id, 'name' => $m['name']], $m + ['is_active' => true]);
        }
        foreach ($menuSari as $m) {
            Menu::updateOrCreate(['user_id' => $sari->id, 'name' => $m['name']], $m + ['is_active' => true]);
        }

        $kantor1 = User::updateOrCreate(
            ['email' => 'kantor@office.test'],
            [
                'name' => 'PT Maju Bersama', 'password' => Hash::make('password'),
                'role' => 'customer', 'is_active' => true,
                'address' => 'Jl. Sudirman Kav. 5, Jakarta Pusat', 'city' => 'Jakarta',
                'phone' => '021-555-0101',
            ]
        );

        $kantor2 = User::updateOrCreate(
            ['email' => 'hrd@company.test'],
            [
                'name' => 'CV Karya Mandiri', 'password' => Hash::make('password'),
                'role' => 'customer', 'is_active' => true,
                'address' => 'Jl. Dipatiukur No. 10, Bandung', 'city' => 'Bandung',
                'phone' => '022-555-0202',
            ]
        );

        $this->makeOrder($kantor1, $dewa, now()->addDays(3)->toDateString(), 'Tolong kirim sebelum jam 11.', [
            ['menu' => $this->menu($dewa, 'Nasi Box Ayam Bakar'), 'portions' => 20],
            ['menu' => $this->menu($dewa, 'Mie Goreng Spesial'), 'portions' => 10],
        ], 'accepted');

        $this->makeOrder($kantor2, $sari, now()->addDays(5)->toDateString(), 'Untuk rapat direksi, toopak 25 orang.', [
            ['menu' => $this->menu($sari, 'Nasi Liwet Komplit'), 'portions' => 25],
        ], 'pending');

        $this->makeOrder($kantor2, $dewa, now()->subDays(2)->toDateString(), null, [
            ['menu' => $this->menu($dewa, 'Snack Box Premium'), 'portions' => 40],
        ], 'completed');

        unset($admin);
    }

    private function menu(User $merchant, string $name): Menu
    {
        return Menu::where('user_id', $merchant->id)->where('name', $name)->firstOrFail();
    }

    /**
     * @param  array<int, array{menu: Menu, portions: int}>  $lines
     */
    private function makeOrder(User $customer, User $merchant, string $date, ?string $note, array $lines, string $status): Order
    {
        $invoice = Order::nextInvoiceNumber($date);

        $order = Order::firstOrCreate(
            ['invoice_number' => $invoice],
            [
                'customer_id' => $customer->id,
                'merchant_id' => $merchant->id,
                'delivery_date' => $date,
                'note' => $note,
                'total_price' => collect($lines)->sum(fn ($l) => $l['menu']->price * $l['portions']),
                'status' => $status,
            ]
        );

        if ($order->wasRecentlyCreated) {
            foreach ($lines as $line) {
                $order->items()->create([
                    'menu_id' => $line['menu']->id,
                    'menu_name' => $line['menu']->name,
                    'price' => $line['menu']->price,
                    'portions' => $line['portions'],
                    'subtotal' => $line['menu']->price * $line['portions'],
                ]);
            }
        }

        return $order;
    }
}
