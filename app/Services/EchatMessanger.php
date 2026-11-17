<?php

namespace App\Services;

class EchatMessanger
{   
    private $base_api_url = 'https://e-chat.tech/api/viber/v2/';
    private $default_params = array();

    private $api_key_viber = '6af342fb2d852';
    private $api_viber_phone = '380633651228';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function doSomething(): string
    {
        return 'Привет из моего класса!';
    }
	
    /** RemoteAPI request performer
		* action - RemoteAPI command name
		* args - JSON string with request parameters
	*/
    public function call($action, $args){
        
        
        if (stripos($action, 'unit_group') === 0) {
				$svc = $action;
				$svc[mb_strlen('unit_group')] = '/';
			} else {
				$svc = preg_replace('\'_\'', '/', $action, 1);
			}
        $url = $this->base_api_url.$svc;
           
        $all_params = [];
        $params = array(
				'number'=> $this->api_viber_phone,

		);
        if ($args <> '') {
            $all_params = array_merge($params, $args);
        }
        else {
            $all_params = $params;
        }

        /* cUrl magic */
        $str = json_encode($all_params);
        
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'user-agent: Armatel CRM API Client',
                'api-key: '.$this->api_key_viber
			],
            CURLOPT_POSTFIELDS => $str
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if($result === FALSE)
				$result = '{"error":-1,"message":'.curl_error($ch).'}';
			
			curl_close($ch);
            return $result;
    }


        /** Unknonwn methods hadler */
    public function __call($name, $args) {
        return $this->call($name, count($args) === 0 ? '' : $args[0]);
    }
}
