SpamMarker PHP library
----------------------

SpamMarker is a simple library for detecting spam in provided strings. It follows the open closed principle by introducing
Spam Filters which are just separate classes used to extend the SpamMarker detecting capabilities. 

The original idea, and actually a slightly different library, is from a lib called Spam Filter by Laju Morrison, which you can find [here](https://github.com/morrelinko/spam-detector).

I was just tired of all sorts of spam which comes from the user input on my projects, so I finally created this.

## Instalation

This library can be loaded into your projects using [Composer](http://getcomposer.org) or by re-inventing the wheel and creating your own autoloader.

Instalation via composer:

```text
 composer require gorankrgovic/spam-marker
```

## Setup and basic usage

This should be done once throughout the app.

```php

use SpamMarker\SpamMarker;

// 1.
// Use some filters
use SpamMarker\Filter\BlackListed;

// 2.
// Initalize the blackisted filter
$blackListed = new BlackListed();

// Add some spammy keywords
$blackListed->add('hacker');
$blackListed->add('attacker');

// 3.
// Initialize the spam marker
$spamMarker = new SpamMarker();

// 4.
// Register the filters
$spamMarker->registerFilter($blackListed);

// 5.
// Check your string against filters
$spamMarkerResult = $spamMarker->check("
	Hello, this is some text containing hacker and attacker 
	and should fail as it has a word that is black-listed
");

// var_dump ( $spamMarkerResult );
// It gives you two objects - is_spam (bool) and messages array

if ( $spamMarkerResult->passed() )
{
    // Do some stuff, because the spam marker is passed
    echo 'Your string doesn\'t contain the spam';
}
else
{
    echo 'String containts the spam!';
}

// If you only want to see if it's failed then just...
if ( $spamMarkerResult->failed() ) {
    // Do stuff
} 

```


Each time you call the ``check()`` method on a string, it returns a ``SpamOutput``
Object which holds the spam check result and messages.

You can change various stuff inside such as messages for each filter. with two methods: 

Calling setOption function:

```php

$loadedFilter->setOption('message', 'My own returning error message');

```

Or by initializing with different config

```php

$config = array(
    'message' => 'My own custom error message'
);

$blackListed = new BlackListed($config);

```

## Currently provided filters


###### 1. BlackListed Filter:

The black list detector flags a string as a spam if it contains
any of one or more words that has been added to the black list.
Strings could be formed from Regular Expressions or a Character Sequence.

###### 2. BlackListedFile Filter:

This one is similar to the above but it fetches regex'es and string from provided folder. 
Currently the folder is in ``src/Filter/blacklists``. If you want to provide your own files, upon init you must declare the folder path:

```php

$config = array(
    'dir' => '/path/to/your/folder'
);

$blackListedFile = new BlackListedFile($config);

```

Please DO NOT forget to add the ``index`` file which contains the filenames within the provided directory.

###### 3. TooManyEmails Filter:

The name says it all. It matches the number of links in string. It's configurable easy like:

```php

$config = array(
    'emailsAllowed' => 2
);

```

###### 4. TooManyLinks Filter:

How many links we can allow in the string

```php

$config = array(
    'linksAllowed' => 2,
    'linksRatio' => 20 // Percentage of links to words ratio within the text
);

```

###### 5. TooManyPhones Filter:

How many phone numbers we can allow.

```php

$config = array(
    'phonesAllowed' => 2
);

```

###### 6. TooManyUppercase Filter:

How many uppercase words are allowed in string. Useful when someones enter the ``FREE FREE BLAH BLAH DISCOUNT OFF``.
 
```php

$config = array(
    'wordsRatio' => 20 // Percentage of allowed uppercase words in a string
);

```


## Creating your own custom Filter

You create a detector simply by creating a class that implements the ``FilterInterface``
which defines the following contract.

```php

interface FilterInterface
    {
        public function filter($data);
    }

```
Your filter must return an array of:

```php

array(
    'founded' => true, // or false if spam not found
    'message' => 'message to return if spam found'
);

```

After creating your own filter, you add it using the ``registerFilter()`` method in the SpamMarker.

## License

The MIT License (MIT). Please see LICENSE for more information.

Voila!




