<?php
function smarty_function_cmslink ( array $params, Smarty_Internal_Template $template )
{
    /* Check that all the necessary input parameters have been defined. */
    if ( empty( $params['plain'] ) && empty ( $params['text'] ))
    {
        trigger_error("cmslink: missing 'text' parameter");
        return null;
    }

    if ( empty ( $params['act'] )) {
        trigger_error("cmslink: missing 'act' parameter");
        return null;
    }

    if ( empty ( $params['obj'] )) {
        trigger_error("cmslink: missing 'obj' parameter");
        return null;
    }

    if ( empty ( $params['id'] )) {
        trigger_error("cmslink: missing 'id' parameter");
        return null;
    }

    $res =  BASE_DIR . '/msp/node/' . $params['act'] . '/' . $params['obj'] . '/' . $params['id'];

    if ( ! empty($params['get']))
    {
        $res = '?' . $params['get'];
    }

    if ( ! empty($params['https']))
    {
        /* Construct an absolute URL with https:// */
        $res = 'https://' . $_SERVER['SERVER_NAME'] . $res;
    }

    if ( empty($params['plain']))
    {
        $res = '<a href="' . $res . '">' . $params['text'] . '</a>';
    }

    return $res;
}

?>
