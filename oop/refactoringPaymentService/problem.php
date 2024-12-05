<?php

// Given the legacy code for the PaymentService below
// 1. What anti-patterns do you recognize?
// 2. How would you refactor it according to best practices?
// 3. Could you draw a diagram of the final architecture?

class PaymentService
{
    public $bankRequest;
    public $bankResponse;

    public function authorize($amount, $currency, array $cardData, $gatewayUrl, $orderId)
    {
        $this->bankRequest = [
            'transaction_type' => 'auth',
            'amount' => $amount,
            'currency' => $currency,
            'card' => $cardData['cardNumber'],
            'exp' => $cardData['expirationDate'],
            'cvv' => $cardData['cvv'],
        ];

        $this->makeRequest($gatewayUrl);

        $statusCode = $this->bankResponse['status']['code'];
        $statusMessage = $this->bankResponse['status']['message'];
        $bankReference = $this->bankResponse['auth_ref_code'];

        if ($statusCode == '000') {
            $db =  new DatabaseHandler();
            $query = "UPDATE orders SET `Status` = 'AUTHORIZED', `BankReference` = '{$bankReference}' WHERE OrderId = '{$orderId}'";
            $db->run($query);
        }

        $paymentResponse = [
            'code' => $statusCode,
            'message' => $statusMessage
        ];

        return $paymentResponse;
    }

    public function makeRequest($gatewayUrl)
    {
        $httpClient = new HttpClient();
        $response = $httpClient->post($gatewayUrl, http_build_query($this->bankRequest));
        $this->bankResponse = json_decode($response, true);
    }
}

class DatabaseHandler
{
    public function run(string $query): void
    {
        // suppose a query is executed
    }
}

class HttpClient
{
    public function post(string $url, string $requestBody): string
    {
        // suppose a HTTP request is made
        return '{"status": {"code": "000", "message": "Success"}, "auth_ref_code": 45876}';
    }
}

function main()
{
    $service = new PaymentService();
    $cardData = [
        'cardNumber' => '4111111111111111',
        'expirationDate' => '1030',
        'cvv' => '123',
    ];

    $response = $service->authorize(10.99, 'EUR', $cardData, 'https://www.gateway.com/auth', '998877');

    var_dump($response);
}
main();
