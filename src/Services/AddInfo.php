<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Codehub\Gpwebpay\Services\Exceptions\AddInfoException;
use DOMDocument;
use Spatie\ArrayToXml\ArrayToXml;

class AddInfo
{
    public function __construct(private string $schema, private array $values)
    {
        $this->validate();
    }

    private function validate(): void
    {
        $dom = new DOMDocument;
        $dom->loadXML($this->toXml());

        libxml_use_internal_errors(true);
        if (! $dom->schemaValidateSource($this->schema)) {
            $errors = [];
            foreach (libxml_get_errors() as $xmlError) {
                $errors[] = $xmlError->message;
            }

            throw new AddInfoException('Validation errors: '.implode(' | ', $errors));
        }
        libxml_use_internal_errors(false);
    }

    public function toXml(): string
    {
        return trim(ArrayToXml::convert(
            $this->values, [
                'rootElementName' => 'additionalInfoRequest',
                '_attributes' => [
                    'xmlns' => 'http://gpe.cz/gpwebpay/additionalInfo/request',
                ],
            ]
        ));
    }

    public static function createMinimalValues(string $version = '5.0'): array
    {
        return [
            '_attributes' => [
                'version' => $version,
            ],
        ];
    }
}
