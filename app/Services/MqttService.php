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
                    $newTinggiAir = (float) $data['tinggi_air'];
                    $newDebit     = (float) $data['debit'];
                    $newStatus    = $this->determineStatus($newTinggiAir);

                    $lastReport = SensorReport::orderBy('created_at', 'desc')->first();

                    if ($lastReport) {
                        $tinggiAirChanged = abs($newTinggiAir - (float) $lastReport->tinggi_air) > self::FLOAT_COMPARISON_TOLERANCE;
                        $debitChanged     = abs($newDebit - (float) $lastReport->debit) > self.FLOAT_COMPARISON_TOLERANCE;
                        $statusChanged    = $newStatus !== $lastReport->status;

                        if (!$tinggiAirChanged && !$debitChanged && !$statusChanged) {
                            $createRecord = false;
                            Log::info("📝 Data tidak berubah signifikan dari laporan terakhir. Skipping creation. Current: TA={$newTinggiAir}, D={$newDebit}, S={$newStatus}");
                        }
                    }

                    if ($createRecord) {
                        $report = SensorReport::create([
                            'tinggi_air' => $newTinggiAir,
                            'debit'      => $newDebit,
                            'status'     => $newStatus,
                        ]);
                        Log::info("💾 Data saved: ID={$report->id}, Tinggi Air={$report->tinggi_air}cm, Debit={$report->debit}, Status={$newStatus}");
                    }

                    $sendNotification = false;
                    if ($newStatus === 'critical') {
                        if (!$lastReport) { 
                            $sendNotification = true;
                            Log::info("🔥 Laporan pertama adalah Bahaya. Tinggi Air: {$newTinggiAir}cm.");
                        } elseif ($lastReport->status !== 'critical') { 
                            $sendNotification = true;
                            Log::info("🔥 Status berubah menjadi Bahaya. Tinggi Air: {$newTinggiAir}cm. Status sebelumnya: {$lastReport->status}");
                        } else {
                            Log::info("⚠️ Status masih Bahaya (tidak ada perubahan dari laporan terakhir). Notifikasi tidak dikirim ulang. Tinggi Air: {$newTinggiAir}cm.");
                        }
                    }
                    
                    if ($sendNotification) {
                        $pesanNotifikasi = "🔴 PERINGATAN Bahaya! Tinggi Air: {$newTinggiAir}cm, Debit: {$newDebit} L/detik. Status: {$newStatus}. Segera periksa kondisi di lokasi!";
                        
                        try {
                            app(CallMeBotService::class)->sendMessage($pesanNotifikasi);
                            Log::info("📞 Notifikasi CallMeBot terkirim untuk status CRITICAL.");
                        } catch (\Exception $e) {
                            Log::error("❌ Gagal mengirim notifikasi CallMeBot: " . $e->getMessage());
                        }
                    }

                } else {
                    Log::warning("⚠️ Data 'tinggi_air' atau 'debit' tidak ditemukan dalam pesan MQTT: $message");
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