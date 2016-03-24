<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use App\TicTacToeGame;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command {

    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start a game!";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $arguments = strtolower($arguments);
        $message = $this->getUpdate()->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();

        switch ($arguments) {
            case str_contains($arguments, "tictactoe"):
                $chatgame = TicTacToeGame::where('chat', $chat->getId())->first();
                if ($chatgame) {
                    $this->replyWithMessage(['text' => 'There is already a tictactoe game running!']);
                    return;
                } else {
                    $chatgame = new TicTacToeGame();
                    $chatgame->chat = $chat->getId();
                    $chatgame->player1 = $user->getId();
                    $chatgame->turn = rand(1, 2);
                    $chatgame->save();

                    $this->replyWithMessage(['text' => 'Tictactoe game started! Player 1: '.$user->getFirstName().'. Type /join to join the game']);
                }
                break;
            default:
                $this->replyWithMessage(['text' => 'Test']);
                break;
        }
    }
}