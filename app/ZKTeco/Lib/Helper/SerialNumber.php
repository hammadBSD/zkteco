<?php

namespace App\ZKTeco\Lib\Helper;

use Jmrashed\Zkteco\Lib\ZKTeco;

class SerialNumber
{
    /**
     * Get the serial number of the ZKTeco device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @return bool|mixed Returns the serial number if successful, false otherwise.
     */
    static public function get(\App\ZKTeco\Lib\ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_DEVICE;
        $command_string = '~SerialNumber';

        return $self->_command($command, $command_string);
    }
}
