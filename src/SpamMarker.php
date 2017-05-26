<?php
/**
 * Created by PhpStorm.
 * Date: 5/25/17
 * Time: 8:08 PM
 * SpamMarker.php
 * @author Goran Krgovic <goran@dashlocal.com>
 * @author Laju Morrison <morrelinko@gmail.com>
 *
 * Thanks to the original author Laju Morrison for the idea and his library
 * This library is an adaptation of his SpamDetector library
 * Located at:
 * https://github.com/morrelinko/spam-detector
 *
 * This library offers some bug fixes and improvements, and more filters (detectors)
 */

namespace SpamMarker;

use SpamMarker\Filter\FilterInterface;
use SpamMarker\Processor\StringProcessorInterface;


/**
 * Class SpamMarker
 * @package SpamMarker
 */
class SpamMarker
{


    /**
     * Holder for all registered filters
     *
     * @var Filter\FilterInterface[]
     */
    protected $filters = array();


    /**
     * Options
     * @var array
     */
    protected $options = array();


    /**
     * @var StringProcessorInterface
     */
    protected $stringProcessor;


    /**
     * @param StringProcessorInterface $stringProcessor
     */
    public function setStringProcessor( StringProcessorInterface $stringProcessor )
    {
        $this->stringProcessor = $stringProcessor;
    }


    /**
     * Checks if the string contains spam
     *
     * @param $data
     * @return SpamOutput
     */
    public function check($data)
    {
        $failure = 0;
        $messages = array();

        if (is_string($data))
        {
            $data = array('text' => $data);
        }

        // Prepare the text
        $data = $this->prepareData($data);

        foreach ($this->filters as $filter)
        {
            $passed = $filter->filter($data);

            if ( $passed['founded'] )
            {
                $failure++;
                $messages[] = $passed['message'];
            }
        }
        return new SpamOutput($failure > 0, $messages);
    }


    /**
     * Registers a spam filter
     *
     * @param FilterInterface $spamFilter
     * @return $this
     */
    public function registerFilter( FilterInterface $spamFilter )
    {
        $filterId = $this->classFilterName($spamFilter);

        if (isset($this->filters[$filterId])) {
            throw new \RuntimeException(
                'Spam Filter [%s] already registered',
                $filterId
            );
        }
        $this->filters[$filterId] = $spamFilter;
        return $this;
    }


    /**
     * Gets a filter using its ID (class name)
     *
     * @param $filterId
     * @return bool|FilterInterface
     */
    public function getFilter($filterId)
    {
        if (!isset($this->filters[$filterId])) {
            return false;
        }
        return $this->filters[$filterId];
    }


    /**
     * Get the list of all filters
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }


    /**
     * Prepare the data before passing to filters
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $data['original_text'] = $data['text'];
        $data['text'] = $this->stringProcessor ? $this->stringProcessor->prepare($data['text']) : $data['text'];
        return $data;
    }



    /**
     * Gets the name of a class
     *
     * @param $class
     * @return string
     */
    protected function classFilterName($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return substr($class, strrpos($class, '\\') + 1);
    }
}

