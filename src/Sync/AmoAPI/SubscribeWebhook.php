<?php

namespace Sync\AmoAPI;

use AmoCRM\Models\WebhookModel;

class SubscribeWebhook
{
    /**
     * Подпись аккаунта на события Webhooks
     * @param $apiClient
     * @return array
     */
    public function subscribe($apiClient): array
    {
        $webHookModel = (new WebhookModel())
            ->setSettings([
                'add_contact',
                'update_contact',
                'delete_contact'
            ])
            ->setDestination('https://d029-173-233-147-68.eu.ngrok.io/webhooks');

        $response = $apiClient
            ->webhooks()
            ->subscribe($webHookModel)
            ->toArray();

        return $response;
    }
}