<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Command\StartCommand;

class WebhookController extends AbstractController 
{
    /**
     * @Route("/api/pub/telegram/webhook")
     */
    public function botListenWebhook(
        Request $request,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        KernelInterface $kernel) : JsonResponse
    {
        $logger->debug((string) $request->getContent()) ?? '{ "telegram": }';
        try {
            /* @var array $updateRequest */
            $updateRequest = json_decode($request->getContent(), true);
            $application = new Application($kernel);
            $application->setAutoExit(false);
            switch ($updateRequest['message']['text']) {
                case '/start':
                    $logger->debug('Helloo', ['telegram' => 'start']);
                    $input = new ArrayInput([
                        'command' => 'app:telegram:command:start',
                        StartCommand::ARG_CHAT_ID => $updateRequest['message']['chat']['id']
                    ]);
                    $output = new BufferedOutput();
                    $application->run($input, $output);
                    $content = $output->fetch();
                    $logger->debug($content, ['telegram' => 'start']);
                    break;
                default:
                    // $input = new ArrayInput([
                    //     'command' => 'app:telegram:command:dialog'
                    //     // https://symfony.com/doc/current/console/command_in_controller.html
                    //     ,
                    //     DialogCommand::OPTION_USERNAME => $updateRequest['from']['username'],
                    //     DialogCommand::ARG_CHAT_ID => $updateRequest['message']['chat']['id'],
                    //     DialogCommand::ARG_CHAT_TEXT => $updateRequest['message']['text'],
                    // ]);
                    // $output = new BufferedOutput();
                    // $application->run($input, $output);
                    // $content = $output->fetch();
                    // $logger->debug(\$content, ['telegram', 'default']);
                    break;
            }
        } catch (\Throwable $e) {
            $logger->critical($e->getMessage() . '::' . $e->getFile() . ':' . $e->getLine(), ['telegram_bot_controller']);
        }
        return $this->json(['Hello']);
    }
}