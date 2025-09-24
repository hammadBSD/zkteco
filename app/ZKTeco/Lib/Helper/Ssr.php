<?php

namespace App\ZKTeco\Lib\Helper;

use Jmrashed\Zkteco\Lib\ZKTeco;

class Ssr
{
    /**
     * Get information about SSR (Self-Service Recorder) on the ZKTeco device.
     *
     * @param \App\ZKTeco\Lib\ZKTeco $self The instance of the ZKTeco class.
     * @return bool|mixed Returns SSR information if successful, false otherwise.
     */
    static public function get(\App\ZKTeco\Lib\ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_DEVICE;
        $command_string = '~SSR';

        return $self->_command($command, $command_string);
    }
}
