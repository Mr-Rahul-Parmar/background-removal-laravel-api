<?php

use Illuminate\Http\UploadedFile;

function createFileObject($url)
{
    $path_parts = pathinfo($url);

    $newPath = $path_parts['dirname'];

    $newUrl = $newPath . '/' . $path_parts['basename'];
    copy($url, $newUrl);
    $imgInfo = getimagesize($newUrl);

    $file = new UploadedFile(
        $newUrl,
        $path_parts['basename'],
        $imgInfo['mime'],
        filesize($url),
        true,
        TRUE
    );

    return $file;
}
