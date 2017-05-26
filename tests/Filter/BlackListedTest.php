<?php
/**
 * Created by PhpStorm.
 * Date: 5/26/17
 * Time: 2:22 AM
 * BlackListedTest.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

namespace SpamMarker\Filter;


class BlackListedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlackListed
     */
    protected $blackList;


    public function setUp()
    {
        $this->blackList = new BlackListed();
        // Adding black lists manually
        $this->blackList->add('example.com');
        $this->blackList->add('127.0.0.1');
        $this->blackList->add('[site|some]dump\.[com|org|net|info]', true);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSpamFreeString()
    {
        $data = array('text' => 'This is a very clean text with no blacklisted data.');
        $return = $this->blackList->filter($data);
        $this->assertFalse($return['founded']);
    }


    public function testSimpleBlacklistedString()
    {
        $data = array('text' => 'this is a string with example.com and should fail.');
        $return = $this->blackList->filter($data);

        $this->assertTrue($return['founded']);
        $data = array('text' => 'this is a string with 127.0.0.1 and should fail.');

        $return = $this->blackList->filter($data);
        $this->assertTrue($return['founded']);
    }


    public function testRegexPatternBlacklistedString()
    {
        $data = array('text' => 'this is a string with somedump.com should fail spam check');
        $return = $this->blackList->filter($data);
        $this->assertTrue($return['founded']);
        $data = array('text' => 'this is a string with sitedump.net should fail spam check');
        $return = $this->blackList->filter($data);
        $this->assertTrue($return['founded']);
    }

    public function tearDown()
    {
        $this->blackList = null;
    }
}