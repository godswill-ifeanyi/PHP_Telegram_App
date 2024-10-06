namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

// Command to handle /start
class StartCommand extends UserCommand {
    protected $name = 'start';
    protected $description = 'Start command';
    protected $usage = '/start [referral_code]';
    protected $version = '1.0.0';

    public function execute() {
        $message = $this->getMessage();
        $chat_id = $message->getFrom()->getId();
        $username = $message->getFrom()->getFirstName();
        $referral_code = $message->getText(true) ?: null;

        $keyboard = new InlineKeyboard([
            ['text' => '😎 Play Game 😎', 'url' => 'https://godswill-ifeanyi.github.io/PHP_Telegram_App/'],
            ['text' => '🔗 Join Channel 🔗', 'url' => 'https://t.me/armtechtonic'],
            ['text' => '🤑 Claim Reward 🤑', 'callback_data' => 'claim'],
        ]);

        $data = [
            'chat_id' => $chat_id,
            'text' => "Welcome to MyTeacher Bot, $username!",
            'reply_markup' => $keyboard,
        ];

        return Request::sendMessage($data);
    }
}
