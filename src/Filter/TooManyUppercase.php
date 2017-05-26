<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 11:51 PM
 * TooManyUppercase.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */
namespace SpamMarker\Filter;


/**
 * Class TooManyUppercase
 * @package SpamMarker\Filter
 */
class TooManyUppercase implements FilterInterface
{

    /**
     * This is the ratio, in perc.
     * ie how many percentage of uppercase relative
     * to the number of words in a string we allow
     * @var int
     *   Default 30%
     */
    protected $wordsRatio = 30;


    /**
     * Message to return
     * @var string
     */
    protected $message = 'Text contains too many uppercase words';


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['wordsRatio']))
        {
            $this->setOption('wordsRatio', $config['wordsRatio']);
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
            if ( $key == 'wordsRatio' || $key == 'message')
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

        // Just count letters
        $text = preg_replace("/[^A-Za-z0-9 ]/", '', $text);

        // Get the words in array
        $words = str_word_count($text, 1);

        $wordCount = count($words);

        $founded = 0;

        foreach ($words as $word)
        {
            if (strtoupper($word) === $word)
            {
                $founded++;
            }
        }

        $ratio = floor(($founded / $wordCount) * 100);

        $is = $ratio >= $this->wordsRatio;

        // mark as spam if ratio
        return array(
            'founded' => $is,
            'message' => $this->message
        );

    }
}