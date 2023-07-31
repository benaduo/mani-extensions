# GiantSMS PHP API Library

PHP library for making API requests to GiantSMS bulk messaging platform

[GiantSMS.com](https://giantsms.com)

---

- [Installation](#installation)
- [Requirements](#requirements)
- [Documentation](https://developer.giantsms.com)
- [Contribute](#contribute)

---

### Installation

To install GiantSMS PHP Library, simply:

    $ composer require fingergiant/giantsms-api

### Requirements

GiantSMS PHP Library works with PHP >= 5.3.

This library requires API authentication credentials in order to function. Get your credentials from [GiantSMS.com](https://giantsms.com)

### Usage
- Send a message
    
```PHP
    use BulkSMS\GiantSMS;
    
    $sms = new GiantSMS('xxxxxxxxx', 'xxxxxx'); // API username & secret
    var_dump($sms->send('Hello there', '0XXXXXXXXX', 'Tester')); // message, recipient, sender
```

- Check balance
    
```PHP
    use BulkSMS\GiantSMS;
    
    $sms = new GiantSMS('xxxxxxxxx', 'xxxxxx'); // API username & secret
    var_dump($sms->balance());
```

### Contribute

1. Check for open issues or open a new issue to start a discussion around a bug or feature.
1. Fork the repository on GitHub to start making your changes.
1. Write one or more tests for the new feature or that expose the bug.
1. Make code changes to implement the feature or fix the bug.
1. Send a pull request to get your changes merged and published.
