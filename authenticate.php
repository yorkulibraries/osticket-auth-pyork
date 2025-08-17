<?php

require_once(INCLUDE_DIR.'class.auth.php');
class PYorkAuthentication extends StaffAuthenticationBackend {
    static $name = "PYork Authentication";
    static $id = "pyork";

    function supportsInteractiveAuthentication() {
        return false;
    }

    public static function signOut($staff) {
        session_unset();
        osTicketSession::destroyCookie();
        session_destroy();
        Lock::removeStaffLocks($staff->getId());
        Http::redirect('https://passportyork.yorku.ca/ppylogin/ppylogout');
    }

    function signOn() {
        if (isset($_SERVER['HTTP_PYORK_USER']) && !empty($_SERVER['HTTP_PYORK_USER']))
            $username = $_SERVER['HTTP_PYORK_USER'];

        if ($username) {
            if (($user = StaffSession::lookup($username)) && $user->getId()) {
                if (!$user instanceof StaffSession) {
                    // osTicket <= v1.9.7 or so
                    $user = new StaffSession($user->getId());
                }
                return $user;
            }
        }
    }
}

class UserPYorkAuthentication extends UserAuthenticationBackend {
    static $name = "PYork Authentication";
    static $id = "pyork.client";

    function supportsInteractiveAuthentication() {
        return false;
    }

    public static function signOut($user) {
        session_unset();
        osTicketSession::destroyCookie();
        session_destroy();
        Http::redirect('https://passportyork.yorku.ca/ppylogin/ppylogout');
    }

    function signOn() {
        if (isset($_SERVER['HTTP_PYORK_EMAIL']) && !empty($_SERVER['HTTP_PYORK_EMAIL']))
            $username = $_SERVER['HTTP_PYORK_EMAIL'];

        if ($username) {
            if ($acct = ClientAccount::lookupByUsername($username)) {
                if (($client = new ClientSession(new EndUser($acct->getUser()))) && $client->getId()) {
                    return $client;
		}
            }
	    $data = array('email'=>$username, 'name'=>$_SERVER['HTTP_PYORK_FIRSTNAME'] . ' ' . $_SERVER['HTTP_PYORK_SURNAME']);
	    $user = User::fromVars($data, true, false);
	    if ($user && $acct = ClientAccount::createForUser($user)) {
	        $client = new ClientSession(new EndUser($user));
                $acct->confirm();
                if ($user = $this->login($client, $this)) {
                    Http::redirect('tickets.php');
                }
            }
        }
    }
}

require_once(INCLUDE_DIR.'class.plugin.php');
require_once('config.php');
class PYorkAuthPlugin extends Plugin {
    var $config_class = 'PYorkAuthConfig';

    function bootstrap() {
        $config = $this->getConfig();
        if ($config->get('auth-staff'))
            StaffAuthenticationBackend::register('PYorkAuthentication');
        if ($config->get('auth-client'))
            UserAuthenticationBackend::register('UserPYorkAuthentication');
    }
}
