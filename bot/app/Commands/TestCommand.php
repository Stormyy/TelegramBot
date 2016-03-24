<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use Telegram\Bot\Commands\Command;

class TestCommand extends Command{

    /**
     * @var string Command Name
     */
    protected $name = "test";

    /**
     * @var string Command Description
     */
    protected $description = "Testing";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $user = $this->getUpdate()->getMessage()->getFrom()->getFirstName();
        if(!empty($arguments)){
            $user = $arguments;
        }

        $response = $this->replyWithPhoto(['photo' => 'http://static1.fjcdn.com/thumbnails/comments/Punch+whoever+made+that+question+_749e191b250808c5d15355ddb36e4e8f.jpg', 'caption' => 'Rekt!!! '.$user.' got rekt!']);
    }
}