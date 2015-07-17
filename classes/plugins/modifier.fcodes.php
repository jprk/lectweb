<?php
define("UPLOAD_ERR_EMPTY", 5);

function smarty_modifier_fcodes($file_upload_errno)
{
    $upload_errors = array(
        UPLOAD_ERR_OK => "No errors.",
        UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL => "Partial upload.",
        UPLOAD_ERR_NO_FILE => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION => "File upload stopped by extension.",
        UPLOAD_ERR_EMPTY => "File is empty." // add this to avoid an offset
    );

    if (array_key_exists($file_upload_errno, $upload_errors)) {
        /* Get the text parameter. */
        $text = $upload_errors[$file_upload_errno];
    } else {
        $text = "Unknown error code " . $file_upload_errno;
    }

    return $text;
}

?>
