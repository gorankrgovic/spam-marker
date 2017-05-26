<?php
/**
 * Created by PhpStorm.
 * Date: 5/26/17
 * Time: 2:17 AM
 * SpamMarkerTest.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

namespace SpamMarker;


use SpamMarker\Filter\BlackListed;


class SpamMarkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SpamMarker
     */
    protected $spam;


    public function setUp()
    {
        $blackList = new BlackListed();
        $this->spam = new SpamMarker();
        $this->spam->registerFilter($blackList);
    }

    public function testRegisteredFilters()
    {
        $filters = $this->spam->getFilters();
        $this->assertContainsOnlyInstancesOf('SpamMarker\Filter\FilterInterface', $filters);
        $this->assertArrayHasKey("BlackListed", $filters);
        $this->assertFalse($this->spam->getFilter('Dummy'));
        $this->assertInstanceOf('SpamMarker\Filter\FilterInterface', $this->spam->getFilter('BlackListed'));
    }
    public function tearDown()
    {
        $this->spam = null;
    }
}