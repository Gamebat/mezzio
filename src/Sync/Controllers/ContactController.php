<?php

namespace Sync\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
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

}
