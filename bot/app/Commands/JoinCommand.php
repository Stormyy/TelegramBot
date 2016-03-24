<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use App\TicTacToeGame;
use App\User;
use Telegram;
use Telegram\Bot\Commands\Command;

class JoinCommand extends Command {

    /**
     * @var string Command Name
     */
    protected $name = "join";

    /**
     * @var string Command Description
     */
    protected $description = "Join a game!";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $arguments = strtolower($arguments);
        $message = $this->getUpdate()->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();
        $oChat = getChat($message);
        $oUser = getUser($message);

        $game = TicTacToeGame::where('chat', $chat->getId())->first();

        if (!$game) {
            $this->replyWithMessage(['text' => 'There is no game running at this moment']);
            return;
        } else {
            if ($game->player2) {
                $this->replyWithMessage(['text' => 'The game is already full! Try next time']);
            } else {
                if ($user->getId() == $game->player1) {
                    $this->replyWithMessage(['text' => 'You are already in the game']);
                    return;
                }
                $game->player2 = $user->getId();
                $game->save();
                $this->replyWithMessage(['text' => $user->getFirstName() . ' joined the game']);

                $markup = [
                    ['1.1', '1.2', '1.3'],
                    ['2.1', '2.2', '2.3'],
                    ['3.1', '3.2', '3.3'],
                ];

                $keyboard = Telegram::replyKeyboardMarkup([
                    'keyboard' => $markup,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);


                $player = 'player' . $game->turn;
                $playinguser = $oChat->user()->where('user.user_id', $game->$player)->first();
                $response = ($this->replyWithMessage(['text' => 'Game started, '.$playinguser->getName().' it\'s your turn!', 'reply_markup' => $keyboard]));

            }
        }
    }
}