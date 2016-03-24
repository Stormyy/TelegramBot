<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use Telegram\Bot\Commands\Command;

class ResponseTimeCommand extends Command{

    /**
     * @var string Command Name
     */
    protected $name = "response";

    /**
     * @var string Command Description
     */
    protected $description = "Return response time";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $user = $this->getUpdate()->getMessage()->getFrom()->getFirstName();
        if(!empty($arguments)){
            $user = $arguments;
        }

        $response = $this->replyWithMessage(['text' => 'This message was rendered in '.(microtime(true) - LARAVEL_START).' seconds']);
    }
}