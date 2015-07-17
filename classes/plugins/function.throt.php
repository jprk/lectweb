<?php
function smarty_function_throt($params, &$smarty)
{
    /* Get the text parameter. */
    $text = $params['text'];

    /* Check that it is not empty. */
    if (empty ($text)) {
        $smarty->trigger_error("throt: missing 'text' parameter");
        return;
    }

    return '<img src="throt.php?text=' . $text .
    '" title="' . $text . '" alt="' . $text . '">';
}

?>
