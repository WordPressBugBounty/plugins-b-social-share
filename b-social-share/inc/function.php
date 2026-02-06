<?php
function bssbIsPremium()
{
    return BSSB_HAS_PRO ? bss_fs()->can_use_premium_code() : false;
}

