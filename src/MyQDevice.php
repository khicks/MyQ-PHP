<?php

namespace MyQ;

class MyQDevice {
    /** @var $myq MyQ */
    protected $myq;

    protected $device_id;
    protected $device_type_id;
    protected $device_type_name;
    protected $registration_time;
    protected $serial_number;
    protected $user_name;
    protected $user_country_id;
    protected $children_device_ids;
    protected $updated_by;
    protected $updated_time;
    protected $connect_server_device_id;

    protected function __construct($myq, $device) {
        $this->myq = $myq;
        $this->device_id = $device['MyQDeviceId'];
        $this->device_type_id = $device['MyQDeviceTypeId'];
        $this->device_type_name = $device['MyQDeviceTypeName'];
        $this->registration_time = $device['RegistrationDateTime'];
        $this->serial_number = $device['SerialNumber'];
        $this->user_name = $device['UserName'];
        $this->user_country_id = $device['UserCountryId'];
        $this->children_device_ids = $device['ChildrenMyQDeviceIds'];
        $this->updated_by = $device['UpdatedBy'];
        $this->updated_time = $device['UpdatedDate'];
        $this->connect_server_device_id = $device['ConnectServerDeviceId'];
    }

    public function getDeviceID() {
        return $this->device_id;
    }
}
