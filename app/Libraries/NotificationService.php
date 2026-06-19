<?php

namespace App\Libraries;

class NotificationService
{
    /**
     * @param array<string, mixed> $customer
     */
    public function send(array $customer, string $type): void
    {
        // Plug real email or WhatsApp provider delivery here. The API records attempts in communication_logs.
    }
}
