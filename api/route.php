<?php
require_once './prepare.php';

API::init();
API::define('AUTH', '[0-9a-f]{20}');
API::define('ID', '\d+');
API::post('session/', function($a_Data) {
    if (!isset( $a_Data['email'],  $a_Data['password'])) {
        return API::make_error(400, 'Missing POST parameters.');
    }
    $email = $a_Data['email'];
    $password = $a_Data['password'];
    // check for isertions...
    // quick and dirty implementation
    try {
        $session = new Session($email, $password);
    } catch(UserError $u) {
        API::make_error($u->getCode(), $u->getMessage());
    }

    echo '{"authToken": "'.$session->get_token().'"}';
});
API::get('session/{AUTH}/', function($a_Data) {
    // check whether input is token
    $session = $a_Data['session'];
    $session->get_all_sessions();
    var_dump($session->get_user());
});
API::put('session/', function($a_Data) {

});
API::delete('session/{AUTH}/', function($a_Data) {
    $session = $a_Data['session'];

    $session->destroy();
});
API::finalize();
?>