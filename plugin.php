<?php

return array(
    'id' =>             'auth:pyork', # notrans
    'version' =>        '0.1',
    'name' =>           /* trans */ 'PYork Authentication',
    'author' =>         'Tuan Nguyen',
    'description' =>    /* trans */ 'Allows for the Apache server to perform
    the authentication of the user. osTicket will match the username from the
    server authentication to a username defined internally',
    'url' =>            'http://www.osticket.com/plugins/auth/pyork',
    'plugin' =>         'authenticate.php:PYorkAuthPlugin'
);

?>
