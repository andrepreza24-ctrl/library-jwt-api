public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained()->onDelete('restrict');
        $table->integer('quantity');
        $table->decimal('price_at_time', 10, 2);
        $table->timestamps();
    });
}