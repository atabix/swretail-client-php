<?php

// CREATE RELATION

$relation = new Relation(Type::CUSTOMER);

$relation->setExternalId('WEB701');
$relation->setGroup('Klanten');
$relation->setEmail('info@example.com');

$relation->setName($lastname, $firstname);
$relation->address()
    ->setStreet('Hoofdweg', '12') // or separate: ->setHousenumber('12')
    ->setZipcode('7908HB')
    ->setCity('Hoogeveen', 'NL'); // or separate: ->setCountry('NL')

$relation->setPhone('0509998887', '0644443333'); // or separate: ->setPhone2('06123')

$relation->setBirthdate('26 Apr 1986'); // also accepts a Carbon object.
$relation->setTitle(Title::FAMILY);

$relation->addOrder($order); // Order object.

// Create:
$relation->create();
