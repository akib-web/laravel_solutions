<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\AppDevice;

trait AppNotify
{
    private $auth_key = 'key=firebase_API_key';
    private $device_id_array = [];
    
    private $notification_data;
    
    public function __construct(){
        $AppDeviceArray = AppDevice::where('status',1)->pluck('device_id')->toArray();
        $this->device_id_array = $AppDeviceArray;
    }
    
    public function set_data($notify_data){    
        if(!isset($notify_data['title']) && !$notify_data['body'] && !isset($notify_data['data']) ){
            return false;
        }
        /* required data */
        $data['registration_ids'] = $this->device_id_array;
        $data['notification']['title'] = $notify_data['title']??null;
        $data['notification']['body'] = $notify_data['body']??null;
        $data['data']['data'] = $notify_data['data']??null;
        /* required data */
        
        /* if needed start */
        $data['data']['id'] = $notify_data['id']??null;
        $data['data']['icon'] = $notify_data['icon']??null;
        $data['data']['image'] = $notify_data['image']??null;
        $data['data']['content_title'] = $notify_data['content_title']??null;
        $data['data']['summary'] = $notify_data['summary']??null;
        $data['data']['details'] = $notify_data['details']??null;
         /* if needed end */
         
        $this->notification_data = json_encode($data);
    }
    
    public function push_notification_data($data){
        
        $this->set_data($data);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $this->notification_data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: $this->auth_key",
            "Content-Type: application/json"
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return $response;
    }
    
    // use or check this function to send notification from any controller
    public function push_app_notification(Request $request){

        // "data": "new_blog",
        // "data": "new_course",
        // "data": "notification",

        $notify_data['title'] = 'title_test';
        $notify_data['body'] = 'body_test';
        
        $notify_data['data'] = 'notification'; // three type of notification. this code is simplify the desired notification.
        $notify_data['id'] = 'enter course/blog id';
        $notify_data['icon'] = 'enter icon image url';
        $notify_data['image'] = 'image url';
        $notify_data['content_title'] = 'content_title';
        $notify_data['summary'] = 'summary';
        $notify_data['details'] = 'details';
        
        // return $notify_data;
        
        return $this->push_notification_data($notify_data);
        
    }
}
