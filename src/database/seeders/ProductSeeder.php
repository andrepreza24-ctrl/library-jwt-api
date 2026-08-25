public function run(): void
{
    \App\Models\Product::create([
        'name' => 'Camiseta Developer',
        'description' => 'Camiseta de algodón 100% para programadores',
        'price' => 25.00,
        'stock' => 50
    ]);

    \App\Models\Product::create([
        'name' => 'Taza de Café Backend',
        'description' => 'Taza cerámica de 11oz',
        'price' => 12.50,
        'stock' => 100
    ]);
}