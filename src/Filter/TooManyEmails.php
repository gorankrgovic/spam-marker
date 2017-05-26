<?php
/**
 * Created by PhpStorm.
 * Date: 5/26/17
 * Time: 1:16 AM
 * TooManyEmails.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */
namespace SpamMarker\Filter;


/**
 * Class TooManyEmails
 * @package SpamMarker\Filter
 */
class TooManyEmails
{

    /**
     * Const URI REGEX
     *
     * matches URI
     */
    const EMAIL_REGEX = '/\b(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))\b/iD';


    /**
     * How many links we are allowing in text
     *
     * @var int
     */
    protected $emailsAllowed = 0;


    /**
     * Message to return
     * @var string
     */
    protected $message = 'Text contains the disallowed number of emails';


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['emailsAllowed']))
        {
            $this->setOption('emailsAllowed', $config['emailsAllowed']);
        }

        if (isset($config['message']))
        {
            $this->setOption('message', $config['message']);
        }

    }

    /**
     * Sets an option for configuration
     *
     *
     * @param $key
     * @param $value
     */
    public function setOption($key = false, $value = false)
    {
        if ( $key && $key != null )
        {
            if ( $key == 'emailsAllowed' || $key == 'message')
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * {@inheritDocs}
     */
    public function filter($data)
    {
        // We only need the text, not the original text
        $text = $data['text'];

        // Match the url pattern in text
        preg_match_all(self::EMAIL_REGEX, $text, $matches);

        // How many links we have inside?
        $emailsCount = count($matches[0]);

        // Get the match
        if ( $emailsCount > 0 && $emailsCount >= $this->emailsAllowed) {
            // If the link count is more than the maximum allowed
            // Mark as spam
            return array(
                'founded' => true,
                'message' => $this->message
            );
        }
        else
        {
            return array(
                'founded' => false,
                'message' => $this->message
            );
        }
    }

}