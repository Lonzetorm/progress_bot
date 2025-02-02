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
    name: 'app:telegram:command:show_tracked_list',
    description: '/start',
)]
class TrackedListCommand extends Command
{
    const ARG_CHAT_ID = 'chatId';
    const MESSAGE_START = "Выберите, по чему сегодня хотите внести запись";
    const START_BUTTONS = [
        [
	        [
		        'text' => 'Подтягивания',
		        'callback_data' => 'startNew',
            ],
            [
		        'text' => 'Отжимания',
		        'callback_data' => 'track',
            ],
            [
		        'text' => 'Приседания',
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

    protected function getTrackedList($chatId): array
    {
        $trackedList = [];

        return $trackedList;
    }
}
