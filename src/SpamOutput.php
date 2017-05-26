<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 8:25 PM
 * SpamOutput.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 */
namespace SpamMarker;

/**
 * Class SpamOutput
 * @package SpamMarker
 */
class SpamOutput
{

    /**
     * @var bool
     */
    protected $is_spam = false;


    /**
     * @var array
     */
    protected $messages = array();


    /**
     * SpamOutput constructor.
     * @param $is_spam
     * @param array $messages
     */
    public function __construct($is_spam, array $messages = array() )
    {
        $this->is_spam = $is_spam;
        $this->messages = $messages;
    }

    /**
     * Alias of SpamOutput::failed();
     *
     * @return bool
     */
    public function isSpam()
    {
        return $this->failed();
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return !$this->passed();
    }

    /**
     * @return bool
     */
    public function passed()
    {
        return $this->is_spam == false;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

}