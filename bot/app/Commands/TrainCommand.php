<?php
/**
 * Created by PhpStorm.
 * User: Bram
 * Date: 18-3-2016
 * Time: 22:22
 */

namespace Bot\Commands;


use Telegram\Bot\Commands\Command;

class TrainCommand extends Command{

    /**
     * @var string Command Name
     */
    protected $name = "train";

    /**
     * @var string Command Description
     */
    protected $description = "#TreinSpringen";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments) {
        $prefix = '';
        if(!empty($arguments)){
            $prefix = $arguments.', ';
        }

        $response = $this->replyWithPhoto(['photo' => 'http://www.refdag.nl/polopoly_fs/fleurbloemen_anp_1_711002!image/1951233233.jpg', 'caption' => $prefix.'Als je het even niet meer ziet zitten']);
    }
}