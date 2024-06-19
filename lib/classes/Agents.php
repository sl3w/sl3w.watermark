<?php

namespace Sl3w\Watermark;

use CAgent;

class Agents
{
    public static function createAgentForPendingProcess($elementId, $iblockId, $operation)
    {
        CAgent::AddAgent(
            '\Sl3w\Watermark\Agents::pendingWatermarkProcess(' . $elementId . ', ' . $iblockId . ', \'' . $operation . '\');',
            Settings::MODULE_ID,
            'N',
            3600,
            '',
            'Y',
            date('d.m.Y H:i:s', time() + SL3W_WATERMARK_PENDING_1C_PROCESS_START_AFTER_SECONDS)
        );
    }

    public static function pendingWatermarkProcess($elementId, $iblockId, $operation)
    {
        Events::mainProcessToAddWatermark($elementId, $iblockId, $operation);

        return false;
    }
}