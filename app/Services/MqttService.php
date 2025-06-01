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

                    Log::info("💾 Data saved: ID={$report->id}, Tinggi Air={$report->tinggi_air}cm, Debit={$report->debit}, Status={$report->status}");


                    if ($status == 'critical') {
                        $pesanNotifikasi = 'Status normal, cek fitur aja';

                        app(CallMeBotService::class)->sendMessage($pesanNotifikasi);
                        Log::info("📞 Notifikasi CallMeBot terkirim untuk status: {$status}");
                    }
                }

            }, 0); 

            $mqtt->loop(true);
        } catch (\Exception $e) {
            Log::error('❌ MQTT connection failed: ' . $e->getMessage());
             if (app()->environment('local')) {
                echo "❌ MQTT connection failed: " . $e->getMessage() . PHP_EOL;
            }
        }
    }

    protected function determineStatus($tinggi_air): string
    {
        $tinggi_air_float = (float) $tinggi_air;

        if ($tinggi_air_float > 80) {
            return 'critical';
        } elseif ($tinggi_air_float > 20) {
            return 'warning';
        } else {
            return 'normal';
        }
    }
}