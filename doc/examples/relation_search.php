<?php

use SWRetail\Models\Relation;

// RETRIEVE BY SW CODE
$search = 'K100135';
$relation = Relation::byCode($search);

// RETRIEVE BY EXTERNAL CODE
$search = 'C24';
$relation = Relation::byExternalId($search);

// SEARCH BY UNIFIED ZIPCODE
$search = '7908HB12';
$relations = Relation::searchByUnifiedZip($search);

// SEARCH BY EMAIL ADDRESS
$search = 'info@example.com';
$relations = Relation::searchByEmail($search);

// SEARCH BY MOFIFIED IN THE LAST N MINUTES
$search = 60;
$relations = Relation::searchChanged($search);
