<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 8:15 PM
 * TooManyLinks.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 */
namespace SpamMarker\Filter;


/**
 * Class TooManyLinks
 * @package SpamMarker\Filter
 */
class TooManyLinks implements FilterInterface
{

    /**
     * Const URI REGEX
     *
     * matches URI
     */
    const URI_REGEX = "!((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)!";


    /**
     * How many links we are allowing in text
     *
     * @var int
     */
    protected $linksAllowed = 0;


    /**
     * This is the ratio, in perc.
     * ie how many percentage of links relative
     * to the number of words in a string we allow
     * @var int
     *   Default 10%
     */
    protected $linksRatio = 10;


    /**
     * Message to return
     * @var string
     */
    protected $message = 'Text contains the disallowed number of links';


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['linksAllowed']))
        {
            $this->setOption('linksAllowed', $config['linksAllowed']);
        }

        if (isset($config['linksRatio']))
        {
            $this->setOption('linksRatio', $config['linksRatio']);
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
            if ( $key == 'linksAllowed' || $key == 'linksRatio' || $key == 'message')
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return int
     */
    public function getLinksRatio()
    {
        return $this->linksRatio;
    }

    /**
     * @return int
     */
    public function getLinksAllowed()
    {
        return $this->linksAllowed;
    }


    /**
     * {@inheritDocs}
     */
    public function filter($data)
    {
        // We only need the text, not the original text
        $text = $data['text'];

        // Match the url pattern in text
        preg_match_all(self::URI_REGEX, $text, $matches);

        // How many links we have inside?
        $linkCount = count($matches[0]);

        // How many words we have?
        $wordCount = str_word_count($text, null, 'http: //');

        // Get the match
        if ( $linkCount > 0 && $linkCount >= $this->linksAllowed) {
            // If the link count is more than the maximum allowed
            // Mark as spam
            return array(
                'founded' => true,
                'message' => $this->message
            );
        }

        // Get the ratio of words to link
        $ratio = floor(($linkCount / $wordCount) * 100);

        $is = $ratio >= $this->linksRatio;
        // mark as spam if ratio
        return array(
            'founded' => $is,
            'message' => $this->message
        );
    }
}