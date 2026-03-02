<?php

namespace App\Service;

use Twilio\Rest\Client;
use Psr\Log\LoggerInterface;

class TwilioService
{
    private string $sid;
    private string $authToken;
    private string $fromPhone;
    private ?Client $client = null;
    private LoggerInterface $logger;
    private string $defaultCountry;

    public function __construct(
        string $sid,
        string $authToken,
        string $fromPhone,
        LoggerInterface $logger,
        string $defaultCountry = '+216'
    ) {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->fromPhone = $fromPhone;
        $this->defaultCountry = $defaultCountry;
        $this->logger = $logger;
    }

    /**
     * Initialize Twilio client (lazy loading)
     */
    private function getClient(): Client
    {
        if (!$this->client) {
            // Configure SSL certificate for Windows/PHP
            if (file_exists('C:\php\cacert.pem')) {
                ini_set('openssl.cafile', 'C:\php\cacert.pem');
            }
            
            // Fix for SSL certificate issues (like hostname mismatch from corporate proxies)
            $curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ];
            
            $httpClient = new \Twilio\Http\CurlClient($curlOptions);
            $this->client = new Client($this->sid, $this->authToken, null, null, $httpClient);
        }
        return $this->client;
    }

    /**
     * Send an SMS message
     *
     * @param string $toPhone   Recipient phone number in E.164 format (e.g., +216XXXXXXXX)
     * @param string $message   Message content (max 160 characters for SMS)
     * @return bool            True if sent successfully, false otherwise
     */
    public function sendSms(string $toPhone, string $message): bool
    {
        try {
            // Normalize and validate phone numbers
            $normalizedTo = $this->normalizePhone($toPhone);
            $normalizedFrom = $this->normalizePhone($this->fromPhone);

            if (!$this->isValidPhone($normalizedTo)) {
                $this->logger->warning("Invalid recipient phone number after normalization: {$toPhone} -> {$normalizedTo}");
                return false;
            }

            if (!$this->isValidPhone($normalizedFrom)) {
                $this->logger->error("Invalid sender phone number configured: {$this->fromPhone} -> {$normalizedFrom}");
                return false;
            }

            // Send SMS via Twilio
            $sms = $this->getClient()->messages->create(
                $normalizedTo,
                [
                    'from' => $normalizedFrom,
                    'body' => $message,
                ]
            );

            $this->logger->info("SMS sent successfully to {$toPhone}", [
                'sid' => $sms->sid,
                'status' => $sms->status,
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error("Failed to send SMS to {$toPhone}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            return false;
        }
    }

    /**
     * Send SMS to multiple recipients
     *
     * @param array  $phones     Array of phone numbers
     * @param string $message    Message content
     * @return array            Array of [phone => success (bool)]
     */
    public function sendSmsBulk(array $phones, string $message): array
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->sendSms($phone, $message);
        }
        return $results;
    }

    /**
     * Validate phone number format (E.164)
     *
     * @param string $phone Phone number to validate
     * @return bool         True if valid, false otherwise
     */
    private function isValidPhone(string $phone): bool
    {
        // E.164 format: + followed by 1-15 digits
        return preg_match('/^\+[1-9]\d{1,14}$/', $phone) === 1;
    }

    /**
     * Try to normalize a phone number to E.164 using sensible defaults.
     * - If already starts with + and digits, keep it (clean non-digits)
     * - If it's local (e.g. 8 digits for Tunisia), prepend default country code
     */
    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        if ($phone === '') {
            return '';
        }

        // Keep leading + if present, remove other non-digits
        if (strpos($phone, '+') === 0) {
            $digits = preg_replace('/\D+/', '', $phone);
            return '+' . $digits;
        }

        // Remove non-digits
        $digits = preg_replace('/\D+/', '', $phone);

        // Common local format: 8 digits (Tunisia) -> prepend default country
        if (strlen($digits) === 8) {
            return $this->defaultCountry . $digits;
        }

        // If starts with 0 and then 8 digits, strip leading 0 and prepend default
        if (preg_match('/^0(\d{8})$/', $digits, $m)) {
            return $this->defaultCountry . $m[1];
        }

        // Fallback: if looks like 9-15 digits, try to add +
        if (preg_match('/^\d{9,15}$/', $digits)) {
            return '+' . $digits;
        }

        return '';
    }
}
