<?php

namespace MyQ;

final class MyQSecurityToken {
    private $value;
    private $is_preset;

    public function __construct($value, $is_preset = false) {
        $this->value = $value;
        $this->is_preset = $is_preset;
    }

    public function getValue() {
        return $this->value;
    }

    public function getIsPreset() {
        return $this->is_preset;
    }
}
