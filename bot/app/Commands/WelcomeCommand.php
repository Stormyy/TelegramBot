<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use Telegram\Bot\Commands\Command;

class WelcomeCommand extends Command{

    /**
     * @var string Command Name
     */
    protected $name = "welcome";

    /**
     * @var string Command Description
     */
    protected $description = "#welcome";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $prefix = $this->getUpdate()->getMessage()->getFrom()->getFirstName();
        if(!empty($arguments)){
            $prefix = $arguments;
        }

        if($arguments == "Casper"){
            $prefix = "DIKKE HOMO CASPER";
        }

        $response = $this->replyWithPhoto(['photo' => 'http://i3.ytimg.com/vi/fyiQfuB-ABg/mqdefault.jpg', 'caption' => 'Welkom '.$prefix.'!']);
    }
}