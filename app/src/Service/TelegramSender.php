<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

final class TelegramSender
{
    private ?OutputInterface $output = null;
    public function __construct(private loggerInterface $logger, private ParameterBagInterface $parameterBag) {}

    // Вот это потом убрать?
    public function setOutput(?OutPutInterface $output): void
    {
        $this->output = $output;
    }

    public function sendMessage(
        int $chatId, 
        string $message, 
        string $parseMode = 'HTML', 
        array $buttons = []
    ): void
    {
        $tgApiToken = $this->parameterBag->get('telegram_http_api_token');
        if (empty($tgApiToken)) {
            $this->logger->critical("Не задан telegram_api_token. Сообщение не будет отправлено.", ['telegram']);
            return;
        }
        if (empty($chatId)) {
            $this->logger->critical("Пустой или равный [] chatId. Сообщение не будет отправлено.", ['telegram']);
            return;
        }
        // Вот это потом убрать
        if ($this->output) {
            $this->logger->warning("Сообщение не отправляется, а выводится в терминал");
            $this->output->writeLn("{$chatId}: {$message}");
            return;
        }
        // Отправляем HTTP-запрос. Можно использовать обычный CURL

        $jsonContent = [
            'chat_id' => $chatId,
            'parse_mode' => $parseMode,
            'text' => $parseMode == 'HTML' ? strip_tags($message, '<b><i><u><s><span><a><code><pre>'): $message
        ];
        if (! empty($buttons)) {
            $jsonContent['reply_markup'] = json_encode([
                'inline_keyboard' => $buttons,
            ]);
        }
        $response = HttpClient::create()->request(
            'POST', 'https://api.telegram.org/bot' . $tgApiToken . '/sendMessage', [
                'json' => $jsonContent
            ]
        );
        if ($response->getStatusCode() != 200) {
            $this->logger->critical("Не удалось отправить сообщение в телеграм chat_id={$chatId}", ['telegram_sender']);
            $respArr = $response->getContent();
            if (is_array($respArr)) {
                $this->logger->critical(json_encode($respArr), ['telegram_sender']);
            }
        }
    }
}