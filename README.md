# AlephMarc2XML
This php class converts an MARC output from Aleph to a XML file

## Usage

```php
$inputFileName = 'down.txt';
$marc = new Marc2XML($inputFileName);
$xml = $marc->get();
```

## License

MIT