<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\TelegramSender;

#[AsCommand(
    name: 'app:telegram:command:start',
    description: '/start',
)]
class StartCommand extends Command
{
    const ARG_CHAT_ID = 'chatId';
    const MESSAGE_START = "Привет! Вот что может этот бот:<br>
    - Отслеживать прогресс по чему угодно (для этого нажмите Начать)
    - Продолжить отслеживать прогресс (если вы уже начали отслеживать, нажмите Продолжить)";
    const START_BUTTONS = [
        [
	        [
		        'text' => 'Начать',
		        'callback_data' => 'startNew',
            ],
            [
		        'text' => 'Продолжить',
		        'callback_data' => 'track',
            ],
        ]
    ];
    const DEFAULT_PARSE_MODE = 'HTML';

    public function __construct(private TelegramSender $telegramSender)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Про симуляцию части потом, возможно, тоже убрать?
        $this->addOption('simulate', null, InputOption::VALUE_OPTIONAL, "Не отправлять сообщение по-настоящему");
        $this->addArgument(self::ARG_CHAT_ID, InputArgument::REQUIRED, 'Telegram chatId');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $simulate = $input->getOption('simulate');
        $chatId = $input->getArgument(self::ARG_CHAT_ID);
        if ($simulate) {
            $this->telegramSender->setOutput($output);
        }
        $this->telegramSender->sendMessage(
            $chatId,
            self::MESSAGE_START,
            self::DEFAULT_PARSE_MODE,
            self::START_BUTTONS
        );

        return Command::SUCCESS;
    }
}
