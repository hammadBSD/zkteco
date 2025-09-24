<?php

namespace App\ZKTeco\Lib\Helper;

use Jmrashed\Zkteco\Lib\ZKTeco;

class Time
{
    /**
     * Set the time on the ZKTeco device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @param string $t The time to set in the format "Y-m-d H:i:s".
     * @return bool|mixed Returns true if the time is set successfully, false otherwise.
     */
    static public function set(\App\ZKTeco\Lib\ZKTeco $self, $t)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_SET_TIME;
        $command_string = pack('I', Util::encodeTime($t));

        return $self->_command($command, $command_string);
    }

    /**
     * Get the current time from the ZKTeco device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @return bool|mixed Returns the current time if successful, false otherwise.
     */
    static public function get(\App\ZKTeco\Lib\ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_GET_TIME;
        $command_string = '';

        $ret = $self->_command($command, $command_string);

        if ($ret) {
            return Util::decodeTime(hexdec(Util::reverseHex(bin2hex($ret))));
        } else {
            return false;
        }
    }
}
