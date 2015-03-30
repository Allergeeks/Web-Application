<?php
class API {
    public static $s_Path = null;
    public static $a_Declarations = array();
    
    /**
     * Definieren eines neuen Komponenten
     * @param $s_Method Server Request Method
     * @param $s_Comp der API Pfad des Componenten
     * @param $f_Func die auszuf�hrende Funktion
     *          function(array of Strings)
     *          Im Array werden Variablen aus dem Pfad zur Verf�gung gestellt
     */
    public static function component($s_Method, $s_Comp, $f_Func) {
        // Methode checken
        if ($_SERVER['REQUEST_METHOD'] != $s_Method OR $s_Comp === null) {
            return false;
        }
        $s_RegPath = $s_Comp;
        $a_Params = array();
        // Nach Variablen im Pfad checken
        if (preg_match_all('~\{[A-Z]+\}~', $s_RegPath, $a_Vars)) {
            foreach($a_Vars[0] as $i_Key => $s_Var) {
                $s_Var = substr($s_Var, 1, -1);
                // Gefundene Variablen auf Deklarationen �berpr�fen
                if (!array_key_exists($s_Var, self::$a_Declarations)) {
                    self::make_error(500, "Undeclared Variable '" + $s_Var + "'.");
                }
                $s_RegPath = preg_replace('~\{'.$s_Var.'\}~',
                                '('.self::$a_Declarations[$s_Var].')', $s_RegPath);
                // Variable Position im Pfad zuordnen
                $a_Params[$s_Var] = $i_Key;
            }
        }
        // Checken ob Pfad mit Url matched
        if (preg_match('~^'.$s_RegPath.'$~', self::$s_Path, $a_Matches)) {
            // Bei Post mit den Variablen f�llen 
            if ($s_Method === 'POST') {
                $a_Req = $_POST;
            } else {
                $a_Req = array();
            }
            // Pfadvariablen sammeln und �bergeben
            foreach($a_Params as $s_Var => $i_Position) {
                $a_Req[strtolower($s_Var)] = $a_Matches[$i_Position + 1];
            }
            $f_Func($a_Req);
            die();
        }
    }

    public static function finalize() {
        $s_Message = 'Cannot '.strtolower($_SERVER['REQUEST_METHOD']).' '.self::$s_Path;
        self::make_error(400, $s_Message);
    }

    private static function make_error($s_ResponseCode, $s_Message) {
        http_response_code($s_ResponseCode);
        die($s_Message);
    }

    /**
     * Definiere eine neuen POST Pfad
     * @param $s_Comp der API Pfad des Componenten
     * @param $f_Func die auszuf�hrende Funktion
     *          function(array of Strings)
     *          Im Array werden Variablen aus dem Pfad zur Verf�gung gestellt
     */
    public static function post($s_Comp, $f_Func) {
        self::component('POST', $s_Comp, $f_Func);
    }


    /**
     * Definiere eine neuen GET Pfad
     * @param $s_Comp der API Pfad des Componenten
     * @param $f_Func die auszuf�hrende Funktion
     *          function(array of Strings)
     *          Im Array werden Variablen aus dem Pfad zur Verf�gung gestellt
     */
    public static function get($s_Comp, $f_Func) {
        self::component('GET', $s_Comp, $f_Func);
    }

    /**
     * Definiert eine neue Variable mit Hilfe eines regul�ren Ausdrucks
     * @param $s_Name Name der Variable bitte Gro�buchstaben
     * @param $s_RegEx Regul�rer Ausdruck, auf den die Variable matchen soll
     */
    public static function define($s_Name, $s_RegEx) {
        self::$a_Declarations[$s_Name] = $s_RegEx;
    }
    
    /**
     * Damit wird die API initialisiert. Muss gleich zu Beginn aufgerufen werden
     */
    public static function init() {
        if(!isset($_GET['p'])) {
            self::make_error(404);
        } else {
            self::$s_Path = $_GET['p'];
        }
    }

    /**
     * Wird f�r die besondere Variable {AUTH} verwendet
     * Tritt die Variable auf, wird versucht den User anzumelden
     */
    private static function auth($s_SessId) {
        return SessionLogin::get_logged_user($s_SessId);
    }
}

API::init();
API::define('AUTH', '[0-9a-zA-Z]{20}');
API::define('NUMBER', '\d+');
API::define('ID', '\d+');
API::get('blog/like/{ID}/{AUTH}/', function($a_Data) {
    echo $a_Data['id'].' '.$a_Data['auth'];
});
API::finalize();
?>