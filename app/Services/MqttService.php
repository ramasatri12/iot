<?php

namespace App\Services;

use App\Models\SensorReport;
use Illuminate\Support\Facades\Log;
use App\Services\CallMeBotService;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function subscribe()
    {
        $server   = '18.142.250.134';
        $port     = 1883;
        $clientId = 'php-client-' . uniqid();
        $username = 'Website';
        $password = 'website123';

        $caFile = base_path('isrgrootx1.pem');
        if (!file_exists($caFile)) {
            $error = "❌ File CA tidak ditemukan: $caFile";
            Log::error($error);

            if (app()->environment('local')) {
                echo $error . PHP_EOL;
            }

            return; 
        }


        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls(false)
            ->setTlsVerifyPeer(false)
            ->setTlsCertificateAuthorityFile($caFile);

        $mqtt = new MqttClient($server, $port, $clientId, MqttClient::MQTT_3_1);

        try {
            $mqtt->connect($connectionSettings, true);

            Log::info("✅ MQTT connected. Listening to topic...");

            $mqtt->subscribe('sensor/data', function (string $topic, string $message) {
                Log::info("📩 Received message from [$topic]: $message");

                $data = json_decode($message, true);

                if (isset($data['tinggi_air'],$data['debit'])) {
                    $status = $this->determineStatus((float) $data['tinggi_air']);

                    $report = SensorReport::create([
                        'tinggi_air' => (float) $data['tinggi_air'],
                        'debit'      => (float) $data['debit'],
                        'status'     => $status,
                    ]);

                    Log::info("💾 Data saved: ID={$report->id}");

                    if ($status !== 'normal') {
                        app(CallMeBotService::class)->sendMessage(
                            "⚠️ *Peringatan Sensor*\nTinggi Air: *{$data['tinggi_air']}cm*\nDebit: {$data['debit']}\nStatus: *{$status}*"
                        );
                    }
                }

            }, 0); // QoS 0

            $mqtt->loop(true);
        } catch (\Exception $e) {
            Log::error('❌ MQTT connection failed: ' . $e->getMessage());
             if (app()->environment('local')) {
                echo "❌ MQTT connection failed: " . $e->getMessage() . PHP_EOL;
            }
        }
    }


    protected function handleMessage(array $payload)
    {
        $report = SensorReport::create([
            'tinggi_air' => $payload['tinggi_air'],
            'debit' => $payload['debit'],
            'status' => $this->determineStatus($payload['tinggi_air']),
        ]);

        Log::info("✅ Sensor report created: ID {$report->id}");

        if ($report->status !== 'normal') {
            app(CallMeBotService::class)->sendMessage(
                "🚨 Status: {$report->status}\nTinggi Air: {$report->tinggi_air}cm\nDebit: {$report->debit}"
            );
        }
    }

    protected function determineStatus($tinggi_air): string
    {
        if ($tinggi_air > 100) return 'critical';
        if ($tinggi_air > 70) return 'warning';
        return 'normal';
    }
}