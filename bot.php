<?php

require_once 'vendor/autoload.php';

use Longman\TelegramBot\Telegram;

$telegram = new Telegram(TELEGRAM_BOT_TOKEN, 'mynewmxd');

// Set webhook
$telegram->setWebhook('localhost/tapswap/hook.php');

// Start command
$telegram->addCommand('start', function ($update, $callbackQueryData) {
    $telegramId = $update->getMessage()->getFrom()->getId();
    $userName = $update->getMessage()->getFrom()->getFirstName();
    $referralCode = $update->getMessage()->getText() === '/start' ? null : explode(' ', $update->getMessage()->getText())[1];

    $keyboard = [
        [
            ['text' => 'Play Game', 'url' => 'localhost/tapswap/index.php'],
            ['text' => 'Join Channel', 'url' => 'YourChannelUrl']
        ],
        [
            ['text' => 'Claim Reward', 'callback_data' => 'claim']
        ]
    ];

    $telegram->sendMessage([
        'chat_id' => $update->getMessage()->getChat()->getId(),
        'text' => "Welcome to MyTeacher Bot, $userName!",
        'reply_markup' => [
            'inline_keyboard' => $keyboard
        ]
    ]);
});

// Earnings command
$telegram->addCommand('earnings', function ($update, $callbackQueryData) {
    $telegramId = $update->getMessage()->getFrom()->getId();
    $earnings = getUserEarnings($telegramId);

    $telegram->sendMessage([
        'chat_id' => $update->getMessage()->getChat()->getId(),
        'text' => "Your earnings:\nUsername: @{$update->getMessage()->getFrom()->getUsername()}\nPoints: $earnings"
    ]);
});

// Points command
    $telegram->addCommand('points', function ($update, $callbackQueryData) {
    $telegramId = $update->getMessage()->getFrom()->getId();
    $args = explode(' ', $update->getMessage()->getText());
    $minedPoints = intval($args[1]);

    if (!is_numeric($minedPoints)) {
        return $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Please provide a valid number of points.'
        ]);
    }

    updateMinedPoints($telegramId, $minedPoints);

    $telegram->sendMessage([
        'chat_id' => $update->getMessage()->getChat()->getId(),
        'text' => 'Points updated successfully!'
    ]);
});


// User command
$telegram->addCommand('user', function ($update, $callbackQueryData) {
    $telegramId = $update->getMessage()->getFrom()->getId();
    $user = getUser($telegramId);

    $referralLink = "YourReferralLink";
    $referredBy = $user['referred_by'] ?? 'N/A';
    $telegram->sendMessage([
        'chat_id' => $update->getMessage()->getChat()->getId(),
        'text' => "User Info:\nUsername: @{$update->getMessage()->getFrom()->getUsername()}\nReferral Link: $referralLink\nPoints: {$user['points']}\nReferred By: {$referredBy}"
    ]);
});

// Referrals command
$telegram->addCommand('referrals', function ($update, $callbackQueryData) {
    $telegramId = $update->getMessage()->getFrom()->getId();
    $referralDetails = getReferralDetails($telegramId);

    $telegram->sendMessage([
        'chat_id' => $update->getMessage()->getChat()->getId(),
        'text' => "You have referred {$referralDetails['referral_count']} users: {$referralDetails['usernames']}"
    ]);
});

// Claim reward callback
$telegram->addCallbackQuery('claim', function ($update, $callbackQueryData) {
    $telegramId = $update->getCallbackQuery()->getFrom()->getId();
    $result = claimReward($telegramId);

    if ($result['success']) {
        $telegram->answerCallbackQuery([
            'callback_query_id' => $update->getCallbackQuery()->getId(),
            'text' => $result['message']
        ]);
    } else {
        $telegram->answerCallbackQuery([
            'callback_query_id' => $update->getCallbackQuery()->getId(),
            'text' => $result['message']
        ]);
    }
});

// Run the bot
$telegram->run();

?>