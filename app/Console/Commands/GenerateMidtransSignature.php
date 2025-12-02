<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateMidtransSignature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'midtrans:signature 
                            {order_id=TRX20251202ABC123 : Order ID}
                            {status_code=200 : Status Code}
                            {gross_amount=500000 : Gross Amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Midtrans webhook signature for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $statusCode = $this->argument('status_code');
        $grossAmount = $this->argument('gross_amount');
        $serverKey = config('services.midtrans.server_key');

        if (!$serverKey) {
            $this->error('MIDTRANS_SERVER_KEY not found in .env file!');
            $this->info('Please add: MIDTRANS_SERVER_KEY=your-server-key');
            return 1;
        }

        // Generate signature
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Display info
        $this->info('===========================================');
        $this->info('Midtrans Webhook Signature Generator');
        $this->info('===========================================');
        $this->newLine();

        $this->line('Input Data:');
        $this->line("  Order ID      : $orderId");
        $this->line("  Status Code   : $statusCode");
        $this->line("  Gross Amount  : $grossAmount");
        $this->line("  Server Key    : " . substr($serverKey, 0, 10) . "...");
        $this->newLine();

        $this->line('Generated Signature:');
        $this->warn("  $signature");
        $this->newLine();

        // Generate payload
        $payload = [
            "order_id" => $orderId,
            "transaction_status" => "settlement",
            "status_code" => $statusCode,
            "gross_amount" => $grossAmount,
            "signature_key" => $signature,
            "payment_type" => "qris",
            "transaction_time" => now()->format('Y-m-d H:i:s'),
            "fraud_status" => "accept"
        ];

        $this->info('===========================================');
        $this->info('Complete Webhook Payload (untuk Postman):');
        $this->info('===========================================');
        $this->newLine();
        $this->line(json_encode($payload, JSON_PRETTY_PRINT));
        $this->newLine();

        $this->info('===========================================');
        $this->info('Cara pakai di Postman:');
        $this->info('===========================================');
        $this->line('1. Method: POST');
        $this->line('2. URL: http://127.0.0.1:8000/api/midtrans/webhook');
        $this->line('3. Headers:');
        $this->line('   - Content-Type: application/json');
        $this->line('   - Accept: application/json');
        $this->line('4. Body (raw JSON): Copy payload di atas');
        $this->newLine();

        return 0;
    }
}
