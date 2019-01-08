<?php 

$relation = new Relation(RelationType::CUSTOMER, 'K100135');

// Or retrieve full data from API:
// $relation = Relation::byCode('K100135');

$relation->setEmail('info@example.com');
$relation->setLoyaltyPoints(118);
$relation->address()->setCountry('Nederland');

$updated = $relation->update();
