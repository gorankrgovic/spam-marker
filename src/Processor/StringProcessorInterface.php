<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 8:45 PM
 * StringProcessor.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 */
namespace SpamMarker\Processor;


/**
 * Interface StringProcessorInterface
 * @package SpamMarker\Processor
 */
interface StringProcessorInterface
{
    /**
     * @param $string
     * @return mixed
     */
    public function prepare($string);
}

