<?php

namespace App\ZKTeco\Lib\Helper;

use Jmrashed\Zkteco\Lib\ZKTeco;

class Face
{
    /**
     * Turn on the face recognition feature of the device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @return bool|mixed Returns true if the face recognition feature is turned on successfully, false otherwise.
     */
    static public function on(\App\ZKTeco\Lib\ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_DEVICE;
        $command_string = 'FaceFunOn';

        return $self->_command($command, $command_string);
    }
}
