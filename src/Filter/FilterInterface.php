<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 8:10 PM
 * SpamMarkerInterface.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */
namespace SpamMarker\Filter;


interface FilterInterface {

    /**
     * @param array $data
     * @return mixed
     */
    public function filter($data);


}