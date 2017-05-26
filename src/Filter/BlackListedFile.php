<?php
/**
 * Created by PhpStorm.
 * Date: 5/26/17
 * Time: 1:42 AM
 * BlackListedFile.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */
namespace SpamMarker\Filter;


/**
 * Class BlackListedFile
 * @package SpamMarker\Filter
 */
class BlackListedFile implements FilterInterface
{


    /**
     * Holds the message being returned - it can be passed via setOption or even config
     * @var string
     */
    protected $message = 'Text contains spam keyword(s) or matches spam expressions';

    /**
     * @var
     */
    protected $dir = null;


    /**
     * @var array|bool
     */
    protected $index = false;


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset( $config['message']))
        {
            $this->setOption('message', $config['message']);
        }

        if ( isset($config['dir']) )
        {
            $this->setOption('dir', $config['dir']);
        }
        else
        {
            $dir = self::defaultBlacklistDirectory();

            $this->dir = $dir;
        }

        $this->index = $this->getBlacklistsFromDir();
    }


    /**
     * Sets an option for configuration
     *
     * @param $key
     * @param $value
     */
    public function setOption($key = false, $value = false)
    {
        if ( $key && $key != null )
        {
            if ( $key == 'message' || $key == 'dir' )
            {
                if ( $key == 'dir' )
                {
                    if ( !file_exists($value . DIRECTORY_SEPARATOR . 'index' ) ) {
                        throw new \RuntimeException(sprintf(
                            "Could not find index file in folder [%s]",
                            $value
                        ));
                    }
                    $this->dir = $value;
                }
                else
                {
                    $this->$key = $value;
                }
            }
        }
    }


    /**
     * @return string
     */
    public static function defaultBlacklistDirectory()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'blacklists'; // absolute path
    }


    /**
     * @return array
     */
    private function getBlacklistsFromDir()
    {

        $blacklistIndex = $this->dir . DIRECTORY_SEPARATOR . 'index';

        if (!file_exists($blacklistIndex))
        {
            throw new \RuntimeException(sprintf(
                "Could not find blacklist file [%s]",
                 $blacklistIndex
            ));
        }
        else
        {
            $index = $this->dir . DIRECTORY_SEPARATOR . 'index';

            return $this->getListFromFile($index);
        }
    }

    /**
     * @param $file_path
     * @return array
     */
    private function getListFromFile($file_path)
    {
        $file_contents = file_get_contents($file_path);
        return preg_split("/((\r?\n)|(\r\n?))/", $file_contents, NULL, PREG_SPLIT_NO_EMPTY);
    }


    /**
     * @param $text
     * @param $blacklist
     * @return bool|mixed
     */
    private function regexMatchFromFile($text, $blacklist)
    {
        $keywords = file($blacklist);
        $current_line = 0;
        $regex_match = array();

        foreach($keywords as $regex)
        {
            $current_line++;
            // Remove comments and whitespace before and after a keyword
            $regex = preg_replace('/(^\s+|\s+$|\s*#.*$)/i', "", $regex);

            if (empty($regex)) continue;

            $match = @preg_match("/$regex/i", $text, $regex_match);
            if ($match)
            {
                // Spam found. Return the text that was matched
                return $regex_match[0];
            }
            else if ($match === false)
            {
                throw new \RuntimeException(sprintf(
                    "Invalid regular expression in [%s]",
                    $blacklist
                ));
                continue;
            }
        }
        // No spam found
        return false;
    }

    /**
     * {@inheritDocs}
     */
    public function filter($data)
    {

        // We only need the text from the data
        $text = $data['text'];

        $matched = false;

        // Loop from each index file
        foreach ($this->index as $blacklist_filename)
        {

            $match = $this->regexMatchFromFile($text, $this->dir . DIRECTORY_SEPARATOR . $blacklist_filename);
            if ($match) {
                // Match at least one... then break
                $matched = true;
                break;
            }
        }

        return array(
            'founded' => $matched,
            'message' => $this->message
        );

    }

}