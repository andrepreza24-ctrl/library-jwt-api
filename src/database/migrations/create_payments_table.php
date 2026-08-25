public function up(): void
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->string('stripe_charge_id');
        $table->decimal('amount', 10, 2);
        $table->string('status'); // succeeded, failed
        $table->timestamps();
    });
}