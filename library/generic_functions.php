<?php

class Encryption {

    var $skey = "Ankit<+91-8010215453>"; // you can change it

    public function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($value) {

        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value) {

        if (!$value) {
            return false;
        }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

}

/**
 *
 * @param string $action
 * The name of the Action in the current Controller if $controller is not mentioned
 * @param string $text
 * The text that you want to show instead of a link
 * @param string $controller
 * The name of the controller 
 * @return string 
 * A link with url and title .
 */
function anchor($action, $text, $module=NULL) {

    // Eg. In http://www.example.com/admin/controller_name/controller_action_name
    // action can be 
    if ($module == NULL)
        $link = (substr(CUR_URL, -1) == '/' ? (CUR_URL . $action) : (CUR_URL . '/' . $action));
    else {
        $link = DOMAIN . '/' . $module . '/' . $action;
    }
    return '<a href="' . $link . '">' . $text . '</a>';
}

function image($attr) {
    if (is_array($attr)) {
        return '<img src="' . $attr['src'] . '" width="' . $attr['width'] . '" height="' . $attr['height'] . '" alt="' . $attr['alt'] . '" title="' . $attr['title'] . '">';
    } else {
        return '<img src="' . $attr . '">';
    }
}

function isValidPhone($areaCode, $prefix, $lineNumber) {
    /**
     * Validates a U.S. phone number for proper format
     * @param integer $areaCode the phone number's area code
     * @param integer $prefix the phone number's prefix
     * @param integer $lineNumber the phone number's line number
     * @return boolean
     */
    $validAreaCode = (strlen((int) $areaCode) == 3);
    $validPrefix = (strlen((int) $prefix) == 3);
    $validLineNumber = (strlen((int) $lineNumber) == 4);

    if ($validAreaCode && $validPrefix && $validLineNumber) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function isValidDate($date, $separator='/') {
    /**
     * Determines whether a value is a valid date as defined
     * by PHP's checkdate() function
     * @param string $date date to validate
     * @return boolean
     */
    list($month, $day, $year) = explode($separator, $input);
    return checkdate($month, $day, $year);
}

function isValidEmail($email) {
    /**
     * Determines whether an e-mail address is valid
     * @param string $input email to validate
     * @return boolean
     */
    if (preg_match('/^([_a-z0-9-]+)(\+)*(\.[_a-z0-9-]+)*@([a-z0-9-]+)\.[a-z0-9-]+)*(\.[a-z]{2,6})$/', $email)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function echo_pre($items) {
    echo '<pre>';
    print_r($items);
    // exit;
}

function dateFormat($datestring, $type=NULL, $break=NULL, $time='NO') {
    switch ($type) {
        case 1:
            return date('M j, Y', $datestring) . $break . (($time == 'YES') ? date('g:i:s A', $datestring) : '');

            break;
        case 2:
            break;
        case 3:
            break;
        default:
            return date('F d ,Y', strtotime($datestring));
    }
}

//======================= Function Library=====================================//

function addRange($first, $last) {
    return array_sum(range($first, $last));
}

if (!function_exists('singular')) {

    function singular($str) {
        $str = trim($str);
        $end = substr($str, -3);

        $str = preg_replace('/(.*)?([s|c]h)es/i', '$1$2', $str);

        if (strtolower($end) == 'ies') {
            $str = substr($str, 0, strlen($str) - 3) . (preg_match('/[a-z]/', $end) ? 'y' : 'Y');
        } elseif (strtolower($end) == 'ses') {
            $str = substr($str, 0, strlen($str) - 2);
        } else {
            $end = strtolower(substr($str, -1));

            if ($end == 's') {
                $str = substr($str, 0, strlen($str) - 1);
            }
        }

        return $str;
    }

}

// --------------------------------------------------------------------

/**
 * Plural
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	str
 */
if (!function_exists('plural')) {

    function plural($str, $force = FALSE) {
        $str = trim($str);
        $end = substr($str, -1);

        if (preg_match('/y/i', $end)) {
            // Y preceded by vowel => regular plural
            $vowels = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U');
            $str = in_array(substr($str, -2, 1), $vowels) ? $str . 's' : substr($str, 0, -1) . 'ies';
        } elseif (preg_match('/h/i', $end)) {
            if (preg_match('/^[c|s]h$/i', substr($str, -2))) {
                $str .= 'es';
            } else {
                $str .= 's';
            }
        } elseif (preg_match('/s/i', $end)) {
            if ($force == TRUE) {
                $str .= 'es';
            }
        } else {
            $str .= 's';
        }

        return $str;
    }

}

// --------------------------------------------------------------------

/**
 * Camelize
 *
 * Takes multiple words separated by spaces or hyphen and camelizes them
 *
 * @access	public
 * @param	string
 * @return	str
 */
if (!function_exists('camelize')) {

    function camelize($str) {
        $str = 'x' . strtolower(trim($str));
        $str = ucwords(preg_replace('/[\s-]+/', ' ', $str));
        return substr(str_replace(' ', '', $str), 1);
    }

}

// --------------------------------------------------------------------

/**
 * Underscore
 *
 * Takes multiple words separated by spaces and underscores them
 *
 * @access	public
 * @param	string
 * @return	str
 */
if (!function_exists('underscore')) {

    function underscore($str) {
        return preg_replace('/[\s]+/', '_', strtolower(trim($str)));
    }

}

// --------------------------------------------------------------------

/**
 * hyphenize
 *
 * Takes multiple words separated by spaces and hyphenizes them
 *
 * @access	public
 * @param	string
 * @return	str
 */
if (!function_exists('hyphenize')) {

    function hyphenize($str) {
        return preg_replace('/[\s]+/', '-', strtolower(trim($str)));
    }

}

// --------------------------------------------------------------------

/**
 * Humanize
 *
 * Takes multiple words separated by underscores and changes them to spaces
 *
 * @access	public
 * @param	string
 * @return	str
 */
if (!function_exists('humanize')) {

    function humanize($str) {
        return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($str))));
    }

}

function arrayrange($start, $end, $startIndex=NULL) {
    $arr = range($start, $end);
    if ($startIndex != NULL) {
        $newArray = array();
        for ($i = 0; $i < count($arr); $i++) {
            $newArray[$startIndex] = $arr[$i];
            $startIndex++;
        }
        unset($arr);
        $arr = $newArray;
    }
    return $arr;
}

function createArrayFromUrl($frm) {
    $bricks = explode('&', $frm);

    foreach ($bricks as $key => $value) {
        $walls = preg_split('/=/', $value);
        //$built[$walls[0]] =$walls[1];
        $built[urldecode($walls[0])] = urldecode($walls[1]);
    }

    return $built;
}

if (!function_exists('dropDown')) {

    function dropDown($name = '', $options = array(), $selected = array(), $extra = '') {
        if (!is_array($selected)) {
            $selected = array($selected);
        }


        if (count($selected) === 0) {

            if (isset($_POST[$name])) {
                $selected = array($_POST[$name]);
            }
        }

        if ($extra != '')
            $extra = ' ' . $extra;

        $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

        $form = '<select name="' . $name . '"' . $extra . $multiple . ">\n";

        foreach ($options as $key => $val) {
            $key = (string) $key;

            if (is_array($val) && !empty($val)) {
                $form .= '<optgroup label="' . $key . '">' . "\n";

                foreach ($val as $optgroup_key => $optgroup_val) {
                    $sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

                    $form .= '<option value="' . $optgroup_key . '"' . $sel . '>' . (string) $optgroup_val . "</option>\n";
                }

                $form .= '</optgroup>' . "\n";
            } else {
                $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

                $form .= '<option value="' . $key . '"' . $sel . '>' . (string) $val . "</option>\n";
            }
        }

        $form .= '</select>';

        return $form;
    }

}

function encryptLink($data) {
    $encrypt = new Encryption();
    return $encrypt->encode($data);
}

function decryptLink($data) {
    $encrypt = new Encryption();
    return $encrypt->decode($data);
}

function removeSpace($string) {
    return $str = str_replace(" ", "", preg_replace('/\s\s+/', ' ', $string));
}

function word_trim($string, $count, $ellipsis = FALSE) {
    $words = explode(' ', $string);
    if (count($words) > $count) {
        array_splice($words, $count);
        $string = implode(' ', $words);
        if (is_string($ellipsis)) {
            $string .= $ellipsis;
        } elseif ($ellipsis) {
            $string .= '&hellip;';
        }
    }
    return $string;
}

function addDaysToDate($date, $day) {
    $sum = strtotime(date("d-m-Y", "$date + $day day"));
    $dateTo = date('d-m-Y', $sum);
    return $dateTo;
}

function getPlaceHolder($str) {
    $stringLength = strlen($str);

    switch ($stringLength) {

        case 3:
            $placeholder = ' Hundred ';

            break;
        case 4:
        case 5:
            $placeholder = ' Thousand ';

            break;

        case 6:
        case 7:
            $placeholder = ' Lakh ';

            break;
        case 8:
        case 9:
            $placeholder = ' Crore ';



            break;
        case 10:
        case 11:
            $placeholder = ' Arab ';
            break;

        case 12:
        case 13:
            $placeholder = ' Kharab ';
            break;
    }
    return $placeholder;
}

function convertNumberToWords($str, $recursion=0) {
//    if ($str > 999999999) {
//        return '';
//    }
    if ($str == '0' && $recursion != 0)
        return 'Zero';


    if ($str == '')
        return '';

    $stringLength = strlen($str);
    $placeholder = '';
    $aboveHundred = false;
    $leftMost;
    switch ($stringLength) {
        case 2:
            $divisor = 10;
            $leftMost = substr($str, 0, 1);
            break;
        case 3:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $twoTens = ($recursion == 1) ? true : false;
            $divisor = 100;
            $divisor1 = 10;
            $leftMost = substr($str, 0, 1);
            break;
        case 4:
        case 5:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $twoTens = ($recursion == 1) ? true : false;
            $divisor = 1000;
            $leftMost = ($str <= 9999) ? substr($str, 0, 1) : substr($str, 0, 2);
            break;

        case 6:
        case 7:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $divisor = 100000;
            $leftMost = ($str <= 999999) ? substr($str, 0, 1) : substr($str, 0, 2);
            break;
        case 8:
        case 9:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $divisor = 10000000;
            $leftMost = ($str <= 99999999) ? substr($str, 0, 1) : substr($str, 0, 2);
            break;
        case 10:
        case 11:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $divisor = 1000000000;
            $leftMost = ($str <= 9999999999) ? substr($str, 0, 1) : substr($str, 0, 2);
            break;
        case 12:
        case 13:
            $placeholder = getPlaceHolder($str);
            $aboveHundred = true;
            $divisor = 100000000000;
            $leftMost = ($str <= 999999999999) ? substr($str, 0, 1) : substr($str, 0, 2);
            break;
    }

    if ($str <= 19 || $aboveHundred && $leftMost <= 19) {
        if (!$twoTens) {
            if (!$aboveHundred)
                $str = substr($str, -1, 1); // get last digit
            else {

                $remaining = $str % $divisor;
                $tens = (int) ($str / $divisor);
                $stringLength = strlen($tens);
                $str = substr($tens, -1, 1); // get last digit
            }
        } else {

            $placeholder = getPlaceHolder($str);
            $tens = (int) ($str / $divisor);
            $remaining = (int) ($str % $divisor);
            $str = substr($tens, -1, 1); // get last digit
            $stringLength = strlen($tens);
            $recursion = 1;
        }


        switch ($str) {
            case 0:
                  if ($stringLength == 1) {
                      return 'Zero';
                }
                return 'Ten ' . $placeholder . convertNumberToWords($remaining, $recursion);
                
                break;
            case 1:

                if ($stringLength == 1) {

                    return 'One ' . $placeholder . convertNumberToWords($remaining, $recursion);
                }
                if ($stringLength == 2) {

                    return 'Eleven ' . $placeholder . convertNumberToWords($remaining, $recursion);
                }
                break;
            case 2:
                if ($stringLength == 1)
                    return 'Two ' . $placeholder . convertNumberToWords($remaining, $recursion);
                if ($stringLength == 2)
                    return 'Twelve ' . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
            case 3:
                if ($stringLength == 1)
                    return 'Three ' . $placeholder . convertNumberToWords($remaining, $recursion);
                if ($stringLength == 2)
                    return 'Thirteen ' . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
            case 4:
                $char = 'Four';
                if ($stringLength == 2)
                    $char.='teen ';
                return $char . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
            case 5 :
                if ($stringLength == 1)
                    return 'Five ' . $placeholder . convertNumberToWords($remaining, $recursion);
                if ($stringLength == 2)
                    return 'Fifteen ' . $placeholder . convertNumberToWords($remaining, $recursion);
                break;


            case 6:
                $char = 'Six';
                if ($stringLength == 2)
                    $char.='teen ';
                return $char . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
            case 7 :
                $char = 'Seven';
                if ($stringLength == 2)
                    $char.='teen ';
                return $char . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
            case 8:
                $char = 'Eight';
                if ($stringLength == 2)
                    $char.='teen ';
                return $char . $placeholder . convertNumberToWords($remaining, $recursion);
            case 9:
                $char = 'Nine';
                if ($stringLength == 2)
                    $char.='teen ';
                return $char . $placeholder . convertNumberToWords($remaining, $recursion);
                break;
        }
    }
    else {

        $tens = (int) ($str / $divisor);
        $ones = ($twoTens) ? ((int) ($str % $divisor)) : substr($str, -1, 1); // get last digit
        if (strlen($tens) == 2) {
            $tens = substr($str, 0, 1);
            $firstRemaining = substr($str, 1, 1);
            $ones = substr($str, 2, strlen($str));
        }




        switch ($tens) {
            case 1:
                $char = 'Ten ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 2:
                $char = 'Twenty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 3:
                $char = 'Thirty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 4:
                $char = 'Fourty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 5:
                $char = 'Fifty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 6:
                $char = 'Sixty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 7:
                $char = 'Seventy ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 8:
                $char = 'Eighty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
                break;
            case 9:
                $char = 'Ninty ' . convertNumberToWords($firstRemaining) . $placeholder;
                if ($ones != 0)
                    $char.=convertNumberToWords($ones);
                return $char;
        }
    }
}

//If you only want integer values like 23 or 0155, or form/string integer values like "23" or "0155" to be valid, this should work just fine.
function int($int) {

    // First check if it's a numeric value as either a string or number
    if (is_numeric($int) === TRUE) {

        // It's a number, but it has to be an integer
        if ((int) $int == $int) {

            return TRUE;

            // It's a number, but not an integer, so we fail
        } else {

            return FALSE;
        }

        // Not a number
    } else {

        return FALSE;
    }
}

/**
 * @param type string $string
 * @param type boolean $case
 * @return type string
 * @uses     get only the first letters of the string passed
 * @example passed value Ankit Vishwakarma ; returned value AV
 */
function str_getFirstLetters($string,$num=2, $case=1) {
    $words = explode(" ", $string);
    $letters = "";
    if(count($words)>1){
    foreach ($words as $value) {
        $letters .= substr($value, 0, 1);
    }
    }else{
        $letters = substr($string,0,$num);
    }
    return ($case == 0) ? $letters : strtoupper($letters);
}

function validatePanCardNumber($number,$casesensitive=false) {
    
    if (preg_match('/[A-Z]{3}(A|B|C|F|G|H|J|L|P|T){1}[A-Z]{1}\d{4}[A-Z]{1}/'.(($casesensitive)?'i':''), strtoupper($number)))
        return true;
    else
        return false;
}


?>
