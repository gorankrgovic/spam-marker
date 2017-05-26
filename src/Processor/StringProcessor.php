<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 9:38 PM
 * StringProcessor.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 */

namespace SpamMarker\Processor;

/**
 * Class StringProcessor
 * @package SpamMarker\Processor
 */
class StringProcessor implements StringProcessorInterface
{

    /**
     * @var bool
     */
    protected $asciiConversion = true;
    /**
     * @var bool
     */
    protected $aggressive = false;


    /**
     * StringProcessor constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case 'ascii_conversion':
                    $this->asciiConversion = (bool) $value;
                    break;
                case 'aggressive':
                    $this->aggressive = (bool) $value;
                    break;
            }
        }
    }

    /**
     * @param $string
     * @return mixed|string
     */
    public function prepare($string)
    {
        if ($this->asciiConversion) {
            setlocale(LC_ALL, 'en_us.UTF8');
            $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        }
        if ($this->aggressive) {
            // Convert some characters that 'MAY' be used as alias
            $string = str_replace(array("@", "$", "[dot]", "(dot)"), array("at", "s", ".", "."), $string);
            // Remove special characters
            $string = preg_replace("/[^a-zA-Z0-9-\.]/", "", $string);
            // Strip multiple dots (.) to one. eg site......com to site.com
            $string = preg_replace("/\.{2,}/", ".", $string);
        }
        $string = trim(strtolower($string));
        $string = str_replace(array("\t", "\r\n", "\r", "\n"), "", $string);
        return $string;
    }

}