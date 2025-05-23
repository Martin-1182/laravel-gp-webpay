<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use DOMDocument;
use Spatie\ArrayToXml\ArrayToXml;
use Websystem\Gpwebpay\Services\Exceptions\AddInfoException;

class AddInfo
{
    private const NAMESPACE = 'http://gpe.cz/gpwebpay/additionalInfo/request';

    public function __construct(
        private readonly string $schema,
        private readonly array $values
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $dom = new DOMDocument;
        $dom->loadXML($this->toXml());

        libxml_use_internal_errors(true);
        if (! $dom->schemaValidateSource($this->schema)) {
            $errors = array_map(fn ($error) => $error->message, libxml_get_errors());
            throw new AddInfoException('Validation errors: '.implode('|', $errors));
        }
        libxml_use_internal_errors(false);
    }

    public function toXml(): string
    {
        return trim(ArrayToXml::convert(
            $this->values, [
                'rootElementName' => 'additionalInfoRequest',
                '_attributes' => [
                    'xmlns' => self::NAMESPACE,
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
