<?php

namespace MyQ;

final class MyQ {
    public const BASE_URL = "https://myqexternal.myqdevice.com/api/v4";
    public const LOGIN_PATH = "/User/Validate";
    public const GET_DEVICES_PATH = "/UserDeviceDetails/Get";
    public const PUT_DEVICE_STATE_PATH = "/DeviceAttribute/PutDeviceAttribute";

    private const APPLICATION_ID = "Vj8pQggXLhLy0WHahglCD4N1nAkkXQtGYpq2HrHD7H1nvmbT55KqtN6RSF4ILB/i";
    private const CONTENT_TYPE = "application/json";
    private const CULTURE = "en";
    //private const USER_AGENT = "Chamberlain/3.4.1";

    private const DEVICE_TYPES = [
        1 => "Gateway",
        2 => "GDO",
        3 => "Light",
        5 => "Gate",
        7 => "VGDO Garage Door",
        9 => "Commercial Door Operator (CDO)",
        13 => "Camera",
        15 => "WGDO Gateway AC",
        16 => "WGDO Gateway DC",
        17 => "WGDO Garage Door"
    ];

    private $username;
    private $password;

    /** @var $security_token MyQSecurityToken */
    private $security_token;

    private $wgdo_gateway_ac_devices;

    /** @var $wgdo_garage_door_devices MyQWGDOGarageDoor[] */
    private $wgdo_garage_door_devices;

    private $headers;


    public function __construct($username, $password, $security_token = null) {
        $this->username = $username;
        $this->password = $password;
        $this->initHeaders();

        if ($security_token) {
            $this->security_token = new MyQSecurityToken($security_token, true);
        }
        else {
            $this->login();
        }

        $this->fetchDevices();
    }

    public function getSecurityToken() {
        return $this->security_token;
    }

    private function initHeaders() {
        $this->headers = [
            'MyQApplicationId' => self::APPLICATION_ID,
            'Content-Type' => self::CONTENT_TYPE,
            'Culture' => self::CULTURE
        ];
    }

    private function login() {
        $data = [
            'username' => $this->username,
            'password' => $this->password
        ];
        $response = $this->apiCall(self::LOGIN_PATH, 'POST', $data);

        if ($response !== false && isset($response['SecurityToken'])) {
            $this->security_token = new MyQSecurityToken($response['SecurityToken'], false);
        }
    }

    public function fetchDevices() {
        $this->wgdo_gateway_ac_devices = [];
        $this->wgdo_garage_door_devices = [];
        $response = $this->apiCall(self::GET_DEVICES_PATH, 'GET');

        foreach($response['Devices'] as $device) {
            switch ($device['MyQDeviceTypeId']) {
                case 17:
                    $this->wgdo_garage_door_devices[] = new MyQWGDOGarageDoor($this, $device);
                    break;
            }
        }
    }

    public function getGarageDoorDevices() {
        return $this->wgdo_garage_door_devices;
    }

    private function parseHeaders() {
        $headers = [];

        foreach ($this->headers as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }

        if (is_a($this->security_token, 'MyQ\MyQSecurityToken')) {
            $headers[] = "SecurityToken: {$this->security_token->getValue()}";
        }

        return $headers;
    }

    public function apiCall($path, $method = 'GET', $data = []) {
        $ch = curl_init(self::BASE_URL.$path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FRESH_CONNECT => true,
            //CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_HTTPHEADER => $this->parseHeaders()
        ]);

        if (in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response_raw = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response_raw, true);
        if (is_null($response)) {
            throw new MyQException("API call failed.");
        }

        if ($response['ReturnCode'] === "216") {
            throw new MyQException("Login: Username or password is incorrect.");
        }

        if ($response['ReturnCode'] === "-3333" && $this->security_token->getIsPreset()) {
            $this->login();
            return $this->apiCall($path, $method, $data);
        }

        return $response;
    }
}
