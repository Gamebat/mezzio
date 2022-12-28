<?php

namespace Sync\AmoAPI;

use Hopex\Simplog\Logger;
use Sync\Unisender\AddContact;
use Sync\Unisender\DeleteContact;
use Sync\Unisender\UpdateContact;

class WebhookProcessing
{
    function process(array $parsedBodyArray)
    {
        try {
            if (isset($parsedBodyArray['contacts']['update']))
            {
                (new Logger())
                    ->setLevel('webhooks')
                    ->putData($parsedBodyArray, 'update');

                (new UpdateContact())->update($parsedBodyArray['contacts']['update']);
            } else if (isset($parsedBodyArray['contacts']['add'])) {
                (new AddContact())->add($parsedBodyArray['contacts']['add']);
            } else if (isset($parsedBodyArray['contacts']['delete'])) {
                (new DeleteContact())->delete($parsedBodyArray['contacts']['delete']);
            }
        } catch (\Exception $e){
            (new Logger())
                ->setLevel('errors')
                ->putData($e->getMessage(), 'exception');
        }

        (new Logger())
            ->setLevel('requests')
            ->putData($parsedBodyArray, 'webhooks');
        return $parsedBodyArray;
    }
}