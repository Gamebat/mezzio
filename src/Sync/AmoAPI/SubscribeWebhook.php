<?php

namespace Sync\AmoAPI;

use AmoCRM\Models\WebhookModel;

class SubscribeWebhook
{
    public function subscribe($apiClient)
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