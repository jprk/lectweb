<?php
function smarty_function_adminlink ( array $params, Smarty_Internal_Template $template )
{
    /* Get the text parameter. */
    $text = $params['text'];

    /* Check that it is not empty. */
    if (empty ($text)) {
        trigger_error("adminlink: missing 'text' parameter");
        return;
    }

    /* We have two possible groups of parameters: (1) href or (2) act+obj+id. */
    if (empty ($params['href'])) {
        if (empty ($params['act'])) {
            trigger_error("adminlink: 'href' is empty and 'act' is missing as well");
            return;
        }

        if (empty ($params['obj'])) {
            trigger_error("adminlink: 'href' is empty and 'obj' is missing as well");
            return;
        }

        if (empty ($params['id'])) {
            trigger_error("adminlink: 'href' is empty and 'id' is missing as well");
            return;
        }

        if ( $template->smarty->isAdmin() ) {
            $text =
                '<a href="?act=' .
                $params['act'] . ',' .
                $params['obj'] . ',' .
                $params['id'] . '">' . $text . '</a>';
        }
    } else {
        if ( $template->smarty->isAdmin() ) {
            $text = '<a href="' . $params['href'] . '">' . $text . '</a>';
        }
    }

    if ( ! $template->smarty->isAdmin() ) {
        $text = '<span class="inactive">' . $text . '</span>';
    }


    return $text;
}

?>
