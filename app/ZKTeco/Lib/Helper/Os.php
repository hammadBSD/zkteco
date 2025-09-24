<?php

namespace App\ZKTeco\Lib\Helper;

use Jmrashed\Zkteco\Lib\ZKTeco;

class Os
{
    /**
     * Get the operating system information of the ZKTeco device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @return bool|mixed Returns the operating system information if successful, false otherwise.
     */
    static public function get(\App\ZKTeco\Lib\ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_DEVICE;
        $command_string = '~OS';

        return $self->_command($command, $command_string);
    }
}
