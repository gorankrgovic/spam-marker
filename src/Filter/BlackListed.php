<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 10:12 PM
 * BlackListed.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 */

namespace SpamMarker\Filter;


class BlackListed implements FilterInterface
{


    /**
     * @var string
     */
    protected $regex;

    /**
     * @var bool
     */
    protected $rebuild = false;

    /**
     * Holds blacklisted words
     *
     * @var array
     */
    protected $blackLists = array();

    /**
     * Holds the file that stores blacklisted words
     *
     * @var null
     */
    protected $listFile = null;


    /**
     * Holds the message being returned - it can be passed via setOption or even config
     * @var string
     */
    protected $message = 'Text contains blacklisted keyword(s)';


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['blackLists']))
        {
            $this->setOption('blacklists', $config['blacklists'] );
        }

        if (isset($config['listFile']))
        {
            $this->setOption('listFile', $config['listFile']);
        }

        if (isset( $config['message']))
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
            if ( $key == 'blackLists' || $key == 'listFile' || $key == 'message' )
            {
                if ( $key == 'listFile' )
                {
                    if ( !file_exists($value) ) {
                        throw new \RuntimeException(sprintf(
                            "Could not find blacklist file [%s]",
                            $value
                        ));
                    }

                    $this->listFile = $value;
                }
                else
                {
                    $this->$key = $value;
                }
            }
        }
    }


    /**
     * Adds a word/pattern to the black list.
     * Set the second argument to true to treat
     * the added word as a regular expression.
     *
     * @param string $vars List of blacklisted words
     * @param bool $regex Flags word as regex pattern
     * @return BlackListed
     */
    public function add($vars, $regex = false)
    {
        if (!is_array($vars))
        {
            $vars = array($vars);
        }

        foreach ($vars as $var)
        {
            $this->blackLists[] = $regex ? '[' . $var . ']' : $var;
        }

        return $this;
    }


    /**
     * @param $flag
     */
    protected function rebuildRegex($flag)
    {
        $this->rebuild = $flag;
    }

    /**
     * {@inheritDocs}
     */
    public function filter($data)
    {
        // We only need the text from the data
        $text = $data['text'];

        $is = false;

        if ( !$this->regex || $this->rebuild )
        {
            $fileList = array();

            if ($this->listFile)
            {
                $fileList = array_map('trim', explode("\n", file_get_contents($this->listFile)));
            }

            $blackLists = array_merge($this->blackLists, $fileList);

            $this->regex = sprintf('~%s~', implode('|', array_map(function ($value) {

                if (isset($value[0]) && $value[0] == '[')
                {
                    $value = substr($value, 1, -1);
                }
                else
                {
                    $value = preg_quote($value);
                }

                return '(?:' . $value . ')';

            }, $blackLists)));
        }

        /*
         * return
         */
        $is =  (bool) preg_match($this->regex, $text);

        return array(
            'founded' => $is,
            'message' => $this->message
        );
    }
}