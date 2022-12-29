<?php

namespace Sync\Unisender;

use Hopex\Simplog\Logger;
use PHPUnit\Util\Exception;
use Sync\Controllers\AccountController;
use Sync\Controllers\ContactController;
use Unisender\ApiWrapper\UnisenderApi;

class ImportOnAction
{

    /**
     * @var UnisenderApi
     */
    private UnisenderApi $client;

    /**
     * @var string
     */
    private string $token;

    /**
     * @var array
     */
    private array $usersKommo;

    /**
     * @var array
     */
    private array $contacts;

    /**
     * @var array
     */
    private array $blocks;

    /**
     * @var array
     */
    private array $toDatabase;

    public function __construct($name,$contacts)
    {
        try
        {
            $this->token = (new AccountController())->takeUniToken($name);
            $this->client = new UnisenderApi($this->token);
            $this->usersKommo = $contacts;

        } catch (Exception $e){
            die($e->getMessage());
        }
    }

    /**
     * Добавление контактов Kommo в Unisender
     * @return array
     */
    public function importContacts(): array
    {

        $header = [
            'field_names[0]' => 'email',
            'field_names[1]' => 'Name'
        ];
        $id = 0;
        foreach ($this->usersKommo as $user)
        {
            $this->toDatabase[$user['email']] = [
                'name' => $user['name'],
                'contact_id' => $user['contact_id']
            ];
            $this->contacts["data[{$id}][1]"] = $user['name'];
            $this->contacts["data[{$id}][0]"] = $user['email'];
            $id++;

        }
        $result = [];
        $this->blocks = array_chunk($this->contacts, 500, true);

        foreach ($this->blocks as $block){
            $block  = array_merge($block, $header);
            $inserted = json_decode($this->client->importContacts($block),true);

            if ((count($inserted['result']['log']))!=0)
            {
                foreach ($inserted['result']['log'] as $value)
                {
                    $index = $value['index'];
                    $unseated = $block["data[{$index}][0]"];
                    $unseatedName = $block["data[{$index}][1]"];

                    (new Logger())
                        ->setLevel('errors')
                        ->putData([$unseatedName => $unseated], 'not_added_emails');

                    unset($this->toDatabase[$unseated]);
                }
            }

            foreach ($this->toDatabase as $key => $value)
            {
                (new ContactController())->saveContact([
                    'contact_id' => $value['contact_id'],
                    'name' => $value['name'],
                    'email' => $key
                ]);
            }

            $result[]= $inserted;
        }

        return $result;
    }

    /**
     * Удаление контакта
     * @return void
     */
    public function deleteContact(): void
    {
        try
        {
            $emails = (new ContactController())->getContact($this->usersKommo[0]);
            $header = [
                'field_names[0]' => 'email',
                'field_names[1]' => 'delete'
            ];
            $id = 0;
            foreach ($emails as $email)
            {
                $this->contacts["data[{$id}][1]"] = 1;
                $this->contacts["data[{$id}][0]"] = $email;
                $id++;
            }
            $this->blocks = array_chunk($this->contacts, 500, true);

            foreach ($this->blocks as $block)
            {
                $block  = array_merge($block, $header);
                $this->client->importContacts($block);
            }
        } catch (\Exception $e){
            (new Logger())
                ->setLevel('errors')
                ->putData($e->getMessage(), 'webhook_processing');
        }
        (new ContactController())->deleteContactDB($emails);
    }
}
