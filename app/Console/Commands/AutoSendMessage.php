<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class AutoSendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Auto:SendMessage {game_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自動發送訊息給telegram機器人';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $game_type = $this->argument('game_type');
        $getNewOrderNumInfo = Order::getNewOrderNumInfo($game_type);
        $game_num = $getNewOrderNumInfo[0]['game_num'];
        $user_id = $getNewOrderNumInfo[0]['user_id'];
        $getNumUserWinGold = Order::getNumUserWinGold($user_id,$game_num);

        if ($getNumUserWinGold < -500) {
            $botToken="1721543109:AAGi2EZC1N9UTr9eoh1FQ2FNQNEtl-GRuro";
            $website="https://api.telegram.org/bot".$botToken;
            $chatId=-1001131746605;
            $params=[
                'chat_id'=>$chatId,
                'text'=>'使用者：'.$user_id.', 期數：'.$game_num.', 輸超過500'.', 金額：'.$getNumUserWinGold,
            ];
            $ch = curl_init($website . '/sendMessage');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
