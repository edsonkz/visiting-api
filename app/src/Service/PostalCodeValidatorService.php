<?php

namespace App\Service;

use Normalizer;
use Cake\Http\Client;
use App\Error\Exception\ApiValidationException;

class PostalCodeValidatorService
{
    protected Client $http;
    public function __construct()
    {
        $this->http = new Client();
    }

    public function validateAndFetchAddress(array $postData): array
    {

        $postalCode = $postData['postal_code'];
        // Search postal_code
        $data = $this->fromRepublicaVirtual($postalCode);

        if (!$data) {
            $data = $this->fromViaCep($postalCode);
        }

        if (!$data) {
            throw new ApiValidationException('CEP não encontrado');
        }

        // Prioritze user inserted values
        foreach (['street', 'sublocality', 'complement'] as $field) {
            if (!empty($postData[$field])) {
                $data[$field] = $postData[$field];
            }
        }

        $data['street_number'] = $postData['street_number'];

        return $data;
    }

    // Normalize República Virtual texts
    protected function normalizeText(string $text): string
    {
        // Remove whitespaces (NBSP, etc.)
        $text = preg_replace('/[\x{00A0}\x{1680}\x{180E}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u', ' ', $text);

        // Normilize accentuation
        $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');

        // Remove double spaces and trim the string
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    // Search postal_code on Republica Virutal
    protected function fromRepublicaVirtual(string $postalCode): ?array
    {
        $url = 'http://cep.republicavirtual.com.br/web_cep.php';
        $response = $this->http->get($url, [
            'cep' => $postalCode,
            'formato' => 'json',
        ]);

        if (!$response->isOk()) {
            return null;
        }

        $json = $response->getJson();

        if (empty($json) || (int)($json['resultado'] ?? 0) <= 0) {
            return null;
        }

        return [
            'postal_code' => $postalCode,
            'street' => $this->normalizeText(($json['tipo_logradouro'] ?? '') . ' ' . ($json['logradouro'] ?? '')),
            'complement' => '',
            'sublocality' => $this->normalizeText($json['bairro'] ?? ''),
            'city' => $this->normalizeText($json['cidade'] ?? ''),
            'state' => $this->normalizeText($json['uf'] ?? ''),
        ];
    }


    // Search postal_code on Via Cep
    protected function fromViaCep(string $postalCode): ?array
    {
        $url = "https://viacep.com.br/ws/{$postalCode}/json/";
        $response = $this->http->get($url);

        if (!$response->isOk()) {
            return null;
        }

        $json = $response->getJson();

        if (!empty($json['erro'])) {
            return null;
        }

        return [
            'postal_code' => $this->normalizeText(preg_replace('/[^0-9]/', '', $json['cep'] ?? $postalCode)),
            'street' => $this->normalizeText($json['logradouro'] ?? ''),
            'complement' => $this->normalizeText($json['complemento'] ?? ''),
            'sublocality' => $this->normalizeText($json['bairro'] ?? ''),
            'city' => $this->normalizeText($json['localidade'] ?? ''),
            'state' => $this->normalizeText($json['uf'] ?? ''),
        ];
    }

}
