<?php

return [

    /*
     * Please contact SWRetail to obtain the correct endpoint for your installation
     * i.e.: https://yourservername.cloud.swretail.nl/swcloud/SWWService/
     * The path should end with a slash (/).
     */
    'endpoint' => env('SWRETAIL_ENDPOINT', 'https://yourservername.cloud.swretail.nl/swcloud/SWWService/'),

    /*
     * Please contact SWRetail to obtain an username
     */
    'username' => env('SWRETAIL_USERNAME', 'username'),

    /*
     * Please contact SWRetail to obtain a password
     */
    'password' => env('SWRETAIL_PASSWORD', 'password'),

];
