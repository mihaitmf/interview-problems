<?php

class PaymentService
{
    public function __construct(
        private readonly CommunicationService $communicationService,
        private readonly OrderService $orderService,
    ) {}

    public function authorize(PaymentRequest $paymentRequest): PaymentResponse
    {
        $bankRequest = new BankRequest(
            $paymentRequest->order->amount,
            $paymentRequest->order->currency,
            $paymentRequest->cardData->cardNumber,
            $paymentRequest->cardData->expirationDate,
            $paymentRequest->cardData->cvv,
            $paymentRequest->gatewayUrl,
        );

        $bankResponse = $this->communicationService->makeAuthorizationRequest($bankRequest);

        $this->orderService->authorizeOrder($paymentRequest->order, $bankResponse);

        return new PaymentResponse(
            $bankResponse->statusCode,
            $bankResponse->statusMessage,
        );
    }
}

class DatabaseHandler
{
    public function run(string $query): void // suppose a query is executed
    {}
}

class HttpClient
{
    public function post(string $url, string $requestBody): string // suppose a HTTP request is made
    {
        return '{"status": {"code": "000", "message": "Success"}, "auth_ref_code": 45876}';
    }
}

class CommunicationService
{
    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly RequestBuilder $requestBuilder,
        private readonly ResponseParser $responseParser,
    ) {}

    public function makeAuthorizationRequest(BankRequest $request): BankResponse
    {
        $requestUrl = $request->requestUrl;
        $requestBody = $this->requestBuilder->buildRequestBody($request);

        $response = $this->httpClient->post($requestUrl, $requestBody);

        return $this->responseParser->parse($response);
    }
}

class BankRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $cardNumber,
        public readonly string $cardExpirationDate,
        public readonly string $cardCvv,
        public readonly string $requestUrl,
    ) {}
}

class RequestBuilder
{
    public function buildRequestBody(BankRequest $request): string
    {
        return http_build_query([
            'transaction_type' => 'auth',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'card' => $request->cardNumber,
            'exp' => $request->cardExpirationDate,
            'cvv' => $request->cardCvv,
        ]);
    }
}

class BankResponse
{
    public function __construct(
        public readonly string $bankReference,
        public readonly string $statusCode,
        public readonly string $statusMessage,
    ) {}
}

class ResponseParser
{
    public function parse($response): BankResponse
    {
        $responseDecoded = json_decode($response, true);

        $statusCode = $responseDecoded['status']['code'];
        $statusMessage = $responseDecoded['status']['message'];
        $bankReference = $responseDecoded['auth_ref_code'];

        return new BankResponse($bankReference, $statusCode, $statusMessage);
    }
}

class OrderService
{
    private const STATUS_CODE_SUCCESS = '000';

    public function __construct(
        private  readonly OrderServiceDao $dao,
    ) {}

    public function authorizeOrder(Order $order, BankResponse $bankResponse): void
    {
        if ($bankResponse->statusCode === self::STATUS_CODE_SUCCESS) {
            $this->dao->updateAuthorized(
                $order->orderId,
                $bankResponse->bankReference,
            );
        }
    }
}

class OrderServiceDao
{
    public function __construct(
        private readonly DatabaseHandler $db,
    ) {}

    public function updateAuthorized(string $orderId, string $bankReference): void
    {
        $query = "UPDATE orders SET `Status` = 'AUTHORIZED', `BankReference` = '{$bankReference}' WHERE OrderId = {$orderId}";
        $this->db->run($query);
    }
}

class Order
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $amount,
        public readonly string $currency,
    ) {}
}

class CardData
{
    public function __construct(
        public readonly string $cardNumber,
        public readonly string $expirationDate,
        public readonly string $cvv,
    ) {}
}

class PaymentRequest
{
    public function __construct(
        public readonly Order $order,
        public readonly CardData $cardData,
        public readonly string $gatewayUrl,
    ) {}
}

class PaymentResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $message,
    ) {}
}

function main()
{
    $service = new PaymentService(
        new CommunicationService(
            new HttpClient(),
            new RequestBuilder(),
            new ResponseParser(),
        ),
        new OrderService(
            new OrderServiceDao(
                new DatabaseHandler(),
            ),
        ),
    );
    $cardData = new CardData('4111111111111111', '1030', '123');
    $order = new Order('998877', 10.99, 'EUR');
    $gatewayUrl = 'https://www.gateway.com/auth';
    $paymentRequest = new PaymentRequest($order, $cardData, $gatewayUrl);

    $response = $service->authorize($paymentRequest);

    var_dump($response);
}
main();
