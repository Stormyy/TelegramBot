<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


use App\Chat;

Route::get('/', function () {
    //dump(Telegram::removeWebhook());
    //Telegram::setWebhook(["url" => "https://telegram.bramvaneijk.nl/webhook"]);
    dump(Telegram::addCommand(new \Bot\Commands\JoinCommand()));
});

/**
 * @param \Telegram\Bot\Objects\Message $message
 * @return Chat
 */
function getChat(\Telegram\Bot\Objects\Message $message){
    $chat = $message->getChat();
    if(!($oChat = Chat::where('chat_id', $chat->getId())->first())){
        $oChat = new Chat();
        $oChat->chat_id = $chat->getId();
        $oChat->save();
    }

    return $oChat;
}
function getUser(\Telegram\Bot\Objects\Message $message){
    $oChat = getChat($message);

    $user = $message->getFrom();
    if(!($oUser = $oChat->user()->where('user.user_id', $user->getId())->first())){
        if(!($oUser = \App\User::where('user_id', $user->getId())->first())){
            $oUser = new \App\User();
            $oUser->username = $user->getUsername();
            $oUser->firstname = $user->getFirstName();
            $oUser->lastname = $user->getLastName();
            $oUser->user_id = $user->getId();
            $oUser->save();
        }

        $oUser->chat()->attach($oChat);
    }

    return $oUser;
}

Route::any('/webhook', function () {
    /** @var \Telegram\Bot\Objects\Update $update */
    $update = Telegram::commandsHandler(true);

    $message = $update->getMessage();
    $reply = $message->getReplyToMessage();

    $chat = $message->getChat();
    $user = $message->getFrom();

    $oChat = getChat($message);
    $oUser = getUser($message);

    if ($reply) {
        $game = \App\TicTacToeGame::where('chat', $chat->getId())->first();
        if ($game) {
            $player = 'player' . $game->turn;

            if ($game->$player != $user->getId()) {
                Telegram::sendMessage(['text' => $oUser->getName().' it\'s not your turn!', 'reply_markup' => getKeyboard($game), 'chat_id' => $chat->getId()]);
            } else {
                $field = str_replace('.', '_', $message->getText());
                $possibleValues = ['1_1', '1_2', '1_3', '2_1', '2_2', '2_3', '3_1', '3_2', '3_3'];

                if (in_array($field, $possibleValues)) {
                    $fieldname = 'field_' . $field;
                    if ($game->$fieldname) {
                        Telegram::sendMessage(['text' => 'This box is already filled', 'reply_markup' => getKeyboard($game), 'chat_id' => $chat->getId()]);
                    } else {

                        $game->$fieldname = $game->turn;
                        $game->turn = ($game->turn == 1 ? 2 : 1);
                        $game->save();




                        if (($winner = checkWinner($game))) {
                            $playinguser = $oChat->user()->where('user.user_id', $game->$player)->first();
                            Telegram::sendMessage(['text' => $playinguser->getName().' has won! Do you want to play another game? Type /start', 'chat_id' => $chat->getId(), 'reply_markup' => Telegram::replyKeyboardHide()]);
                            $game->delete();
                        }elseif(checkDraw($game)){
                            Telegram::sendMessage(['text' => 'It\'s a draw noobs. Do you want to play another game? Type /start', 'chat_id' => $chat->getId(), 'reply_markup' => Telegram::replyKeyboardHide()]);
                            $game->delete();
                        } else {
                            $playingId = 'player' . $game->turn;
                            $playinguser = $oChat->user()->where('user.user_id', $game->$playingId)->first();
                            Telegram::sendMessage(['text' => $playinguser->getName().' it\'s now your turn!', 'reply_markup' => getKeyboard($game), 'chat_id' => $chat->getId()]);
                        }
                    }
                }
            }
        }
    }


    // Commands handler method returns an Update object.
    // So you can further process $update object
    // to however you want.

    return 'ok';
});

function getKeyboard(\App\TicTacToeGame $game) {
    $k1_1 = getKeyValue($game->field_1_1, '1.1');
    $k1_2 = getKeyValue($game->field_1_2, '1.2');
    $k1_3 = getKeyValue($game->field_1_3, '1.3');
    $k2_1 = getKeyValue($game->field_2_1, '2.1');
    $k2_2 = getKeyValue($game->field_2_2, '2.2');
    $k2_3 = getKeyValue($game->field_2_3, '2.3');
    $k3_1 = getKeyValue($game->field_3_1, '3.1');
    $k3_2 = getKeyValue($game->field_3_2, '3.2');
    $k3_3 = getKeyValue($game->field_3_3, '3.3');

    $markup = [
        [$k1_1, $k1_2, $k1_3],
        [$k2_1, $k2_2, $k2_3],
        [$k3_1, $k3_2, $k3_3]
    ];

    $keyboard = Telegram::replyKeyboardMarkup([
        'keyboard' => $markup,
        'resize_keyboard' => true,
        'one_time_keyboard' => true
    ]);

    return $keyboard;
}

function getKeyValue($value, $default) {
    if ($value == 0 || is_null($value)) {
        return $default;
    } elseif ($value == 1) {
        return "\xE2\xAD\x95";
    } elseif ($value == 2) {
        return "\xE2\x9C\x96";
    }
}

function checkWinner(\App\TicTacToeGame $game) {
    $combinations = [
        [$game->field_1_1, $game->field_1_2, $game->field_1_3],
        [$game->field_2_1, $game->field_2_2, $game->field_2_3],
        [$game->field_3_1, $game->field_3_2, $game->field_3_3],

        [$game->field_1_1, $game->field_2_1, $game->field_3_1],
        [$game->field_1_2, $game->field_2_2, $game->field_3_2],
        [$game->field_1_3, $game->field_2_3, $game->field_3_3],

        [$game->field_1_1, $game->field_2_2, $game->field_3_3],
        [$game->field_1_3, $game->field_2_2, $game->field_3_1]
    ];

    foreach($combinations as $combination){
        if(($value = array_first(array_count_values($combination))) == 3){
            return array_first($combination);
        }
    }
}

function checkDraw(\App\TicTacToeGame $game){
    for($i = 1; $i<=3; $i++){
        $fieldname = 'field_'.$i;
        for($x = 1; $x<=3; $x++){
            $fieldnametemp = $fieldname.'_'.$x;
            if($game->$fieldnametemp == 0){
                return false;
            }
        }
    }


    return true;
}

function unichr($i) {
    return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
