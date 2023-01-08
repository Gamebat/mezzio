<?php

namespace Sync\AmoAPI;

use Sync\Unisender\ContactAction;

class WebhookProcessing
{
    /**
     * Вызываем событие (добавление, удаление или обновление)
     * в зависимости от пришедшего тела
     * @param array $parsedBodyArray
     * @return void
     */
    function process(array $parsedBodyArray): void
    {
        try
        {
            if (isset($parsedBodyArray['contacts']['update']))
            {
                (new ContactAction())->update($parsedBodyArray['contacts']['update']);
            } else if (isset($parsedBodyArray['contacts']['add'])) {
                (new ContactAction())->add($parsedBodyArray['contacts']['add']);
            } else if (isset($parsedBodyArray['contacts']['delete'])) {
                (new ContactAction())->delete($parsedBodyArray['contacts']['delete']);
            }
        } catch (\Exception $e){
            die($e->getMessage());
        }
    }
}
