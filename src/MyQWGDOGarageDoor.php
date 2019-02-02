<?php

namespace MyQ;

final class MyQWGDOGarageDoor extends MyQDevice {
    private $is_online;
    private $gateway_id;
    private $description;
    private $added_time;
    private $is_unattended_open_allowed;
    private $is_unattended_close_allowed;
    private $name;
    private $device_name;
    private $is_gdo_lock_connected;
    private $attached_work_light_error_present;

    /** @var $door_state MyQWGDOGarageDoorState|null */
    private $door_state;

    public function __construct($myq, $device) {
        parent::__construct($myq, $device);

        foreach ($device['Attributes'] as $attribute) {
            switch($attribute['AttributeDisplayName']) {
                case 'online':
                    $this->is_online = ($attribute['Value'] === "True");
                    break;
                case 'gatewayID':
                    $this->gateway_id = $attribute['Value'];
                    break;
                case 'desc':
                    $this->description = $attribute['Value'];
                    break;
                case 'doorstate':
                    $this->door_state = new MyQWGDOGarageDoorState($attribute['Value'], $attribute['UpdatedTime']);
                    break;
                case 'addedtime':
                    $this->added_time = $attribute['Value'];
                    break;
                case 'isunattendedopenallowed':
                    $this->is_unattended_open_allowed = $attribute['Value'];
                    break;
                case 'isunattendedcloseallowed':
                    $this->is_unattended_close_allowed = $attribute['Value'];
                    break;
                case 'name':
                    $this->name = $attribute['Value'];
                    break;
                case 'deviceName':
                    $this->device_name = $attribute['Value'];
                    break;
                case 'is_gdo_lock_connected':
                    $this->is_gdo_lock_connected = ($attribute['Value'] === "True");
                    break;
                case 'attached_work_light_error_present':
                    $this->attached_work_light_error_present = ($attribute['Value'] === "True");
                    break;
            }
        }
    }

    public function getState() {
        return $this->door_state;
    }

    public function requestState($desired_state) {
        $data = [
            'AttributeName' => 'desireddoorstate',
            'AttributeValue' => $desired_state,
            'MyQDeviceId' => $this->device_id,
        ];

        $response = $this->myq->apiCall(MyQ::PUT_DEVICE_STATE_PATH, 'PUT', $data);
    }

    public function open() {
        $this->requestState(MyQWGDOGarageDoorState::MYQ_DOOR_STATE_OPEN);
    }

    public function close() {
        $this->requestState(MyQWGDOGarageDoorState::MYQ_DOOR_STATE_CLOSED);
    }
}
