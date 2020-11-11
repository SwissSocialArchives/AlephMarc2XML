# AlephMarc2XML
This php class converts an MARC output from Aleph to a XML file

## Usage

```php
$marc = new AlephMarc2XML();
$marc->setFileName('down.txt');
$xml = $marc->get();
```
or
```php
$marc = new AlephMarc2XML();
$marc->setContent($content); // String with Marc
$xml = $marc->get();
```

## License

MIT
