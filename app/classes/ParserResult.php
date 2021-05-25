<?php

namespace App\classes;

use App\Models\Game;


class ParserResult
{
    public static function TWBG() {
        $ch = curl_init(); 
        $options = array(
            CURLOPT_URL => 'https://www.taiwanlottery.com.tw/Lotto/BINGOBINGO/drawing.aspx',
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => "Google Bot",
            CURLOPT_FOLLOWLOCATION => true
        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        curl_close($ch);

        $pattern_num = '/[0-9]{9}/';
        $string_num = $output;
        preg_match_all($pattern_num, $string_num, $matches_num);
        $gameNum = $matches_num[0];
        unset($gameNum[0]);
        rsort($gameNum);
        $count = count($gameNum);

        $pattern_rets = '/[0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}  [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}/';
        $string_rets = $output;
        preg_match_all($pattern_rets, $string_rets, $matches_rets);
        $gameRets = $matches_rets[0];

        for ($number = 0; $number < $count; $number++) {
            $game_num = $gameNum[$number];
            $game_rets = $gameRets[$number];
            $allArray = explode("  ",$game_rets); //先分成兩個陣列,去掉[10]兩個空格-字串轉陣列
            $array1 = mb_split("\s",$allArray[0]); //第一組陣列[0]~[9]--去掉字串中的空格
            $array2 = mb_split("\s",$allArray[1]); //第二組陣列[10]~[19]--去掉字串中的空格
            $arrayMerge = array_merge($array1, $array2); //陣列合併

            for ($i=1; $i<=20; $i++) {
                if($i <= 9){
                    $i = '0'.$i;
                }
                $resp[$i] = $arrayMerge[$i-1];
            }

            $result = json_encode($resp);
            Game::updateGameResult($game_num, $result);
            Game::closeState();
            Game::addResultTime();
        }
    }

    // public static function HPE5() {
    //     $ch = curl_init(); 
    //     $options = array(
    //         CURLOPT_URL => 'https://gem.tech/api/get_one?lottery=hllly&page=1&token=',
    //         CURLOPT_HEADER => false,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_USERAGENT => "Google Bot",
    //         CURLOPT_FOLLOWLOCATION => true
    //     );
    //     curl_setopt_array($ch, $options);
    //     $output = curl_exec($ch);
    //     curl_close($ch);

    //     $array_data = json_decode($output,true);

    //     foreach ($array_data['data'] as $data) {
    //         $game_num =$data['issue'];
    //         $array_rets = $data['code'];
    //         $allArray = explode(" ",$array_rets);

    //         for ($i=1; $i<=5; $i++) {
    //                 $i = '0'.$i;
    //             $resp[$i] = $allArray[$i-1];
    //         }

    //         Game::updateGameResult($game_num, $resp);
    //         Game::closeState();
    //         Game::addResultTime();
    //     }
    // }
}