<?php

namespace diversen;

/**
 *
 */
class prg {
    
    /**
     * Generate redirect for use in prg
     * @return string $path path and query
     */
    private static function getRedirect(){

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ary = array();
        foreach($_GET as $key => $val) {
            if ($key == 'q') {
                continue;
            }
            
            if ($key =='prg' OR $key == 'uniqid') {
                continue;
            }
            $ary[$key] = $val;
        }
        $query = http_build_query($ary);
        $ret = $path . '?' . $query;
        if (!empty($ary)) {
            $ret.='&';
        }
        return $ret;
    }
    
    /**
     * Simple pattern for creating PRG. 
     * (Keep state when reloading browser and resends forms etc.) 
     * @param int $last
     */
    public static function prg ($max_time = 0){

        // genrate a session var holding the _POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $uniqid = uniqid();
            $_SESSION['post'][$uniqid] = $_POST;
            $_SESSION['post'][$uniqid]['prg_time'] = time();            
            $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];

            header("HTTP/1.1 303 See Other");
            
            $location = self::getRedirect() . 'prg=1&uniqid=' . $uniqid;
            self::locationHeader($location);
        }

        
        if (!isset($_SESSION['REQUEST_URI'])){
            $_SESSION['post'] = null;
        } else {
            if (isset($_GET['prg'])){
                $uniqid = $_GET['uniqid'];
                
                if (isset($_SESSION['post'][$uniqid])) {
                    if ( $max_time && ($_SESSION['post'][$uniqid]['prg_time'] + $max_time) < time() ) {
                        unset($_SESSION['post'][$uniqid]);
                    } else {           
                        $_POST = $_SESSION['post'][$uniqid];
                    }
                }
            } else {
                @$_SESSION['REQUEST_URI'] = null;
            }
        }
    }
    
    /**
     * Simple function for creating prg pattern. 
     * (Keep state when reloading browser and resends forms etc.) 
     */
    public static function prgSinglePost (){
        
        // POST
        // genrate a session var holding the _POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $uniqid = uniqid();
            $_SESSION['post'][$uniqid] = $_POST;
            $_SESSION['post'][$uniqid]['prg_time'] = time();            
            $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];

            header("HTTP/1.1 303 See Other");
            $location = self::getRedirect() . 'prg=1&uniqid=' . $uniqid;
            self::locationHeader($location);
        }
        
        if (!isset($_SESSION['REQUEST_URI'])){
            $_SESSION['post'] = null;
        } else {
            if (isset($_GET['prg'])){
                $uniqid = $_GET['uniqid'];
                
                if (isset($_SESSION['post'][$uniqid])) {        
                    $_POST = $_SESSION['post'][$uniqid];
                    unset($_SESSION['post'][$uniqid]);
                }
                
            } else {
                @$_SESSION['REQUEST_URI'] = null;
            }
        }
    }

    /**
     * Send a location header
     * @param type $location the location, e.g. /content/view/article/3
     */
    public static function locationHeader ($location) {        
        $header = "Location: $location";
        header($header);
        die();    
    }
}
