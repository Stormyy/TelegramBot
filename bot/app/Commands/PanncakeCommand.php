<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use Telegram\Bot\Commands\Command;

class PanncakeCommand extends Command{

    /**
     * @var string Command Name
     */
    protected $name = "pancake";

    /**
     * @var string Command Description
     */
    protected $description = "Pannekoek!";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $prefix = $this->getUpdate()->getMessage()->getFrom()->getFirstName().", ";
        if(!empty($arguments)){
            $prefix = $arguments. ", ";
        }

        $response = $this->replyWithPhoto(['photo' => 'http://www.mrbreakfast.com/images/656_smile_pancake.jpg', 'caption' => $prefix.'Gij bent echt zo\'n un grote pannekoek hé']);
    }
}