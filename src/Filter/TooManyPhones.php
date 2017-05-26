<?php
/**
 * Created by PhpStorm.
 * Date: 5/26/17
 * Time: 12:12 AM
 * TooManyPhones.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */
namespace SpamMarker\Filter;


/**
 * Class TooManyPhones
 * @package SpamMarker\Filter
 */
class TooManyPhones implements FilterInterface
{

    /**
     * Phone regex
     */
    const PHONE_REGEX = "/\b
        (?:                                 # Area Code
            (?:                            
                \(                          # Open Parentheses
                (?=\d{3}\))                 # Lookahead.  Only if we have 3 digits and a closing parentheses
            )?
            (\d{3})                         # 3 Digit area code
            (?:
                (?<=\(\d{3})                # Closing Parentheses.  Lookbehind.
                \)                          # Only if we have an open parentheses and 3 digits
            )?
            [\s.\/-]?                       # Optional Space Delimeter
        )?
        (\d{3})                             # 3 Digits
        [\s\.\/-]?                          # Optional Space Delimeter
        (\d{4})\s?                          # 4 Digits and an Optional following Space
        (?:                                 # Extension
            (?:                             # Lets look for some variation of 'extension'
                (?:
                    (?:e|x|ex|ext)\.?       # First, abbreviations, with an optional following period
                |
                    extension               # Now just the whole word
                )
                \s?                         # Optionsal Following Space
            )
            (?=\d+)                         # This is the Lookahead.  Only accept that previous section IF it's followed by some digits.
            (\d+)                           # Now grab the actual digits (the lookahead doesn't grab them)
        )?                                  # The Extension is Optional
        \b/x";                               // /x modifier allows the expanded and commented regex

    /**
     * How many phone numbers we allow
     * @var int
     */
    protected $phonesAllowed = 0;


    /**
     * Message
     * @var string
     */
    protected $message = 'Text contains the disallowed number of phone numbers';


    /**
     * TooManyPhones constructor.
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['phonesAllowed']))
        {
            $this->setOption('phonesAllowed', $config['phonesAllowed']);
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
            if ( $key == 'phonesAllowed' || $key == 'message')
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
        preg_match_all(self::PHONE_REGEX, $text, $matches);

        // How many phones we have inside?
        $phonesCount = count($matches[0]);

        // Get the match
        if ( $phonesCount > 0 && $phonesCount >= $this->phonesAllowed) {
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