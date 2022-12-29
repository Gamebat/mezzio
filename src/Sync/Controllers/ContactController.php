<?php

namespace Sync\Controllers;

use Hopex\Simplog\Logger;
use Illuminate\Database\Capsule\Manager as Capsule;
use Sync\models\Account;
use Sync\Models\Contact;


class ContactController
{
    /**
     * @var Contact
     */
    private Contact $contactModel;
    public function __construct()
    {
        $this->contactModel = new Contact();

        $capsule = new Capsule;

        $capsule->addConnection(include "./config/autoload/database.global.php");
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    public function saveContact(array $contactArray): Contact
    {
        return $this->contactModel->create($contactArray);
    }

    public function updateContact(array $updateArray)
    {
        $contact = Contact::where('name', $updateArray['name'])->first();
        $contact->name = 'Paris to London';
        $contact->email = 'Paris to London';
        $contact->save();
    }

    public function deleteContactDB(array $emails)
    {
        foreach ($emails as $email)
        {
            $deleted = Contact::where('email', $email)->delete();
        }
    }

    public function getContact($id)
    {
        (new Logger())
            ->setLevel('users')
            ->putData($id, 'ids');

        $this->accountModel = Contact::where('contact_id', (int)$id)->get()->toArray();

        (new Logger())
            ->setLevel('users')
            ->putData($this->accountModel, 'contacts');

        foreach ($this->accountModel as $account)
        {
            $result[]= $account['email'];
        }
        (new Logger())
            ->setLevel('users')
            ->putData($result, 'emails');

        return $result;
    }
}
