<?php

namespace App\Http\Traits;

use GuzzleHttp\Client as GuzzleClient;

trait Fcm
{

    public function NotifyAdmin($token, $body)
    {
        $body = "العنوان : " . $body;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'key=AAAAp9RICsU:APA91bE2DSy5KnT88XoURF3rx9eEhpFQaTusJxddwhF15AB7M86YbOyPB5tEM2b9Mf2utX_XJhpeDsCwgnqK3_0R18j1yACLk5bw9wshchRU9T8DrEgpXBMquCkscb-37iKkG0Qtbw9V',
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);


        $body = '{
                "to": "' . $token . '",
        
                "notification": {
                    "color" : "#83C0FE",
                    "title": "هناك مستند بأنتظار المعاينة",
                    "body": "' . $body . '",
                    "icon": "@drawable/timer",
                  
                    "click_action": "FLUTTER_NOTIFICATION_CLICK"
                },
                "data":{
                    
                },
                "priority": 100
            }';

        $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'body' => $body
        ]);
    }
    public function NotifySuper($token, $body)
    {
        $body = "العنوان : " . $body;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'key=AAAAp9RICsU:APA91bE2DSy5KnT88XoURF3rx9eEhpFQaTusJxddwhF15AB7M86YbOyPB5tEM2b9Mf2utX_XJhpeDsCwgnqK3_0R18j1yACLk5bw9wshchRU9T8DrEgpXBMquCkscb-37iKkG0Qtbw9V',
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);


        $body = '{
            "to": "' . $token . '",        
                "notification": {
        
                    "color":"#83C0FE",
                    "title": "هناك مستند مؤرشف بأنتظار المعاينة",
                    "body": "' . $body . '",
                    "icon": "@drawable/book",
                    "click_action": "FLUTTER_NOTIFICATION_CLICK"
                },
                "data":{
                   
                },
                "priority": 100
            }';

        $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'body' => $body
        ]);
    }
    public function NotifyState($token, $body, $state)
    {
        $title = "";
        $color = "";
        $icon = "";
        $body = "العنوان : " . $body;
        if ($state) {
            $title = "تم الموافقة على المستند";
            $color = "green";
            $icon = "@drawable/approved";
        } else {
            $title = "تم رفض المستند";
            $color = "red";
            $icon = "@drawable/rejected";
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'key=AAAAp9RICsU:APA91bE2DSy5KnT88XoURF3rx9eEhpFQaTusJxddwhF15AB7M86YbOyPB5tEM2b9Mf2utX_XJhpeDsCwgnqK3_0R18j1yACLk5bw9wshchRU9T8DrEgpXBMquCkscb-37iKkG0Qtbw9V',
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);


        $body = '{
            "to": "' . $token . '",        
        
                "notification": {
        
                    "color":"' . $color . '",
                    "title": "' . $title . '",
                    "body": "' . $body . '",
                    "icon": "' . $icon . '",
                    "click_action": "FLUTTER_NOTIFICATION_CLICK"
                },
                "data":{
                   
                },
                "priority": 100
            }';

        $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'body' => $body
        ]);
    }
}
