<?php

namespace App\Services;

class SmsService
{
    
    private $apiEndpoint;
    private $apiKey;
    private $senderNumber;

    public function __construct()
    {
        $this->apiEndpoint = getenv('SMS_API_ENDPOINT');
        $this->apiKey = getenv('SMS_API_KEY');
        $this->senderNumber = getenv('SMS_SENDER_NUMBER');
    }

    /**
     * Генерация случайного кода
     * @param int $length - длина кода (по умолчанию 6 цифр)
     * @return string
     */
    public function generateCode(int $length = 6): string
    {
        $characters = '0123456789';
        $code = '';
        
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $code;
    }

    /**
     * Отправка SMS
     * @param string $phone - номер телефона
     * @param string $message - текст сообщения
     * @return bool
     */
    public function sendSms(string $phone, string $message): bool
    {
        $data = [
            'api_key' => $this->apiKey,
            'from' => $this->senderNumber,
            'to' => $phone,
            'text' => $message
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        /* 
            Тут мы совершаем отправку сообщения, но так как это
            тестовый проект, то строку закоментируем и отправим true
        */
        // $result = file_get_contents($this->apiEndpoint, false, $context);
        $result = true;
        
        return $result !== false;
    }
}