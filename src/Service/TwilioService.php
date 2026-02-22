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

    public function __construct(
        string $sid,
        string $authToken,
        string $fromPhone,
        LoggerInterface $logger
    ) {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->fromPhone = $fromPhone;
        $this->logger = $logger;
    }

    /**
     * Initialize Twilio client (lazy loading)
     */
    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client($this->sid, $this->authToken);
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
            // Validate phone numbers
            if (!$this->isValidPhone($toPhone)) {
                $this->logger->warning("Invalid recipient phone number: {$toPhone}");
                return false;
            }

            if (!$this->isValidPhone($this->fromPhone)) {
                $this->logger->error("Invalid sender phone number configured: {$this->fromPhone}");
                return false;
            }

            // Send SMS via Twilio
            $sms = $this->getClient()->messages->create(
                $toPhone,
                [
                    'from' => $this->fromPhone,
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
}
