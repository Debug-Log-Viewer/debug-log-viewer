<?php

namespace Hhxsv5\SSE;

use DBG_LV_LogModel;

class SSE
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Start SSE Server
     * @param int $interval in seconds
     */
    public function start($interval = 3)
    {
        $iterations = 0;

        while (true) {
            try {
                echo $this->event->fill();
            } catch (StopSSEException $e) {
                return;
            }

            if (ob_get_level() > 0) {
                ob_flush();
            }

            flush();

            // if the connection has been closed by the client we better exit the loop
            if (connection_aborted()) {
                return;
            }
            
            sleep($interval);

            // Reconnect logic to avoid timeouts
            $iterations++;
            if ($iterations >= DBG_LV_ITERATIONS_PER_SESSION) {
                // reset carret position
                update_option(DBG_LV_LogModel::DBG_LV_LAST_POSITION_OPTION_NAME, 0);

                echo "retry: 3000\n\n"; // Instruct the client to reconnect after 3 seconds
                flush();
                return; // Gracefully exit the script
            }
        }
    }
}
