<?php

require 'vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Exception\TelegramException;

$telegram = new Telegram('7915745509:AAGqf-HCSU1C5WC-uixOBG5Oj6E8ugAYGKY', 'mynewmxd');

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

// Command to handle /earnings
class EarningsCommand extends UserCommand {
    protected $name = 'earnings';
    protected $description = 'Check your earnings';
    protected $usage = '/earnings';
    protected $version = '1.0.0';

    public function execute() {
        $message = $this->getMessage();
        $chat_id = $message->getFrom()->getId();

        $user = get_user_earnings($chat_id);
        $text = $user ? "Your earnings: \nUsername: {$user['username']}\nPoints: {$user['points']}" : 'No earnings found.';

        return Request::sendMessage(['chat_id' => $chat_id, 'text' => $text]);
    }
}

// Command to handle /points
class PointsCommand extends UserCommand {
    protected $name = 'points';
    protected $description = 'Update mined points';
    protected $usage = '/points <number>';
    protected $version = '1.0.0';

    public function execute() {
        $message = $this->getMessage();
        $chat_id = $message->getFrom()->getId();
        $text = trim($message->getText(true));
        $points = (int) $text;

        if ($points > 0) {
            update_mined_points($chat_id, $points);
            $msg = "Points updated successfully!";
        } else {
            $msg = "Please provide a valid number of points.";
        }

        return Request::sendMessage(['chat_id' => $chat_id, 'text' => $msg]);
    }
}

// Command to handle /user
class OneUserCommand extends UserCommand {
    protected $name = 'user';
    protected $description = 'Get user info';
    protected $usage = '/user';
    protected $version = '1.0.0';

    public function execute() {
        $message = $this->getMessage();
        $chat_id = $message->getFrom()->getId();

        $user = get_user_info($chat_id);
        if ($user) {
            $referral_link = "https://t.me/MyteacherDevBot?start={$user['referral_code']}";
            $msg = "User Info:\nUsername: @{$user['username']}\nReferral Link: $referral_link\nPoints: {$user['points']}";
        } else {
            $msg = "User not found.";
        }

        return Request::sendMessage(['chat_id' => $chat_id, 'text' => $msg]);
    }
}

// Command to handle /referrals
class ReferralsCommand extends UserCommand {
    protected $name = 'referrals';
    protected $description = 'Get referral details';
    protected $usage = '/referrals';
    protected $version = '1.0.0';

    public function execute() {
        $message = $this->getMessage();
        $chat_id = $message->getFrom()->getId();

        $referrals = get_referral_details($chat_id);
        $msg = "You have referred {$referrals['referral_count']} users.";

        return Request::sendMessage(['chat_id' => $chat_id, 'text' => $msg]);
    }
}

$telegram->handle();

?>
