<?php

namespace App\Services;

use App\Models\SensorReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\CallMeBotService;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    protected const FLOAT_COMPARISON_TOLERANCE = 0.1; 
    protected const NOTIFICATION_COOLDOWN_MINUTES = 15;

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

                if (!isset($data['tinggi_air'], $data['debit'])) {
                    Log::warning("⚠️ Data tidak lengkap: $message");
                    return;
                }

                $tinggiAir = (float) $data['tinggi_air'];
                $debit     = (float) $data['debit'];
                $status    = $this->determineStatus($tinggiAir);

                $last = SensorReport::latest()->first();

                $significantChange = true;

                if ($last) {
                    $taChanged      = abs($last->tinggi_air - $tinggiAir) > self::FLOAT_COMPARISON_TOLERANCE;
                    $dChanged       = abs($last->debit - $debit) > self::FLOAT_COMPARISON_TOLERANCE;
                    $statusChanged  = $last->status !== $status;

                    $significantChange = $taChanged || $dChanged || $statusChanged;

                    if (!$significantChange) {
                        Log::info("📝 Tidak ada perubahan signifikan. Data tidak disimpan.");
                        return;
                    }
                }

                $report = SensorReport::create([
                    'tinggi_air' => $tinggiAir,
                    'debit'      => $debit,
                    'status'     => $status,
                ]);

                Log::info("💾 Data saved: ID={$report->id}, TA={$tinggiAir}, D={$debit}, Status={$status}");

                if ($status === 'critical') {
                    $cacheKey = 'last_critical_notification_sent_at';
                    $lastNotifTime = Cache::get($cacheKey);
                    $now = now();

                    if (!$lastNotifTime || $now->diffInMinutes($lastNotifTime) >= self::NOTIFICATION_COOLDOWN_MINUTES) {
                        try {
                            app(CallMeBotService::class)->sendMessage(
                                "⚠️ PERINGATAN: Status Bahaya. TA={$tinggiAir}cm, Debit={$debit} L/detik."
                            );
                            Log::info("📞 Notifikasi CallMeBot terkirim untuk status: {$status}");

                            Cache::put($cacheKey, $now, now()->addMinutes(self::NOTIFICATION_COOLDOWN_MINUTES));
                        } catch (\Exception $e) {
                            Log::error("❌ Gagal mengirim notifikasi: " . $e->getMessage());
                        }
                    } else {
                        Log::info("⏳ Notifikasi tidak dikirim (dalam masa jeda). Terakhir kirim: $lastNotifTime");
                    }
                }

            }, 0);

            $mqtt->loop(true);
        } catch (\Exception $e) {
            Log::error('❌ MQTT connection failed: ' . $e->getMessage());
        }
    }

    protected function determineStatus($tinggi_air): string
    {
        if ($tinggi_air > 80) {
            return 'critical';
        } elseif ($tinggi_air > 20) {
            return 'warning';
        }
        return 'normal';
    }
}
