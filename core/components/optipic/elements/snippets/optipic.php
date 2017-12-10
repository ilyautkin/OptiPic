<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var modExtra $OptiPic */
$OptiPic = $modx->getService('OptiPic', 'OptiPic', MODX_CORE_PATH . 'components/optipic/model/', $scriptProperties);
if (!$OptiPic) {
    return 'Could not load OptiPic class!';
}
$optipicCfg = array(
    "secretkey" => $modx->getOption('optipic_secretkey'),
    "api_url" => "https://optipic.io/api/",
);

$file = ltrim($input, '/');

// Если CURL не установлен - возвращаем соответствующую ошибку
if(!function_exists('curl_init') || !is_callable('curl_init'))
{
    $modx->log(MODX_LOG_LEVEL_ERROR, "[OptiPic] Could not run Curl");
    return $file;
}

$postParams = array();
$compressedFiles = array();
$postParams["quality"] = 70;
$postParams["secretkey"] = $optipicCfg['secretkey'];

$fullpath = MODX_BASE_PATH . $file;
$path_arr = explode('/', $fullpath);
$file_name = array_pop($path_arr);
$optimized_file_name = 'op-' . $file_name;
$optimized_file = implode('/', $path_arr) . '/' . $optimized_file_name;
$output = str_replace(MODX_BASE_PATH, '', $optimized_file);

if (!file_exists($optimized_file)) {
    
    if (!$indexItem = $modx->getObject('OptiPicImage', array('file' => $file))) {
        $indexItem = $modx->newObject('OptiPicImage', array('file' => $file));
    }
    
    // Файла не существует - говорим об этом сервису (чтобы он удалил файл из индекса)
    if(!file_exists($fullpath))
    {
        $modx->log(MODX_LOG_LEVEL_ERROR, "[OptiPic] File {$file} does not exist");
        return $file;
    }
    
    // Если директория файла или сам файл недоступен для записи - выдаем ошибку записи
    if(!is_writable(dirname($fullpath)) || !is_writable($fullpath))
    {
        $modx->log(MODX_LOG_LEVEL_ERROR, "[OptiPic] File {$file} or directory is not writable");
        return $file;
    }
    
    // Прикрепляем файл
    if (function_exists('curl_file_create')) { // php 5.5+
        $cFile = curl_file_create($fullpath);
    } else { // 
        $cFile = '@' . realpath($fullpath);
    }
    $postParams["file"] = $cFile; 
    
    // Указываем внутренний путь к файлу от корня DOCUMENT_ROOT (это нужно для сохранения оригиналов в облеке optipic.io)
    $postParams['filepath'] = $file;
    
    //$imgData = file_get_contents($fullpath);
    //var_dump($optipicCfg["api_url"]."compress?".http_build_query($getParams));        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $optipicCfg["api_url"]."compress");
    //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($ch, CURLOPT_USERPWD, $optipicCfg["api_login"] . ":" . $optipicCfg["api_pass"]);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if(!ini_get("open_basedir"))
    {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    }
    curl_setopt($ch, CURLOPT_POSTREDIR, 3); // чтобы POST-данные передавались и при редиректе
    
    $optiImgData = curl_exec($ch);
    $info = curl_getinfo($ch);
    
    if($info["http_code"]==200)
    {
        //var_dump($_SERVER['DOCUMENT_ROOT'].$file);
        
        // сохраняем результат сжатия во временный файл
        $tmpCompressedFile = $fullpath.".tmp"; // tempnam(sys_get_temp_dir(), 'optipic_');
        file_put_contents($tmpCompressedFile, $optiImgData);
        
        // сравниваем исходный размер картинки со сжатым
        $compressedSize = filesize($tmpCompressedFile); // сжатый размер
        $origSize = filesize($fullpath); // исходный размер
        
        $perms = fileperms($fullpath); // запоминаем исходный chmod
        
        // сжатая картинка меньше, чем исходная - сохраняем сжатую версию
        if($compressedSize>0 && $compressedSize<$origSize)
        {
            $saved = file_put_contents($optimized_file, $optiImgData);
        }
        // исходная картинка меньше сжатой - оставляем исходную
        else
        {
            $modx->log(MODX_LOG_LEVEL_ERROR, "[OptiPic] Original file size of {$file} less then optimized");
            $saved = file_put_contents($optimized_file, file_get_contents($fullpath));
        }
        @chmod($optimized_file, $perms); // ставим chmod у сжатого такой же как у исходного
        @chown($optimized_file, fileowner($fullpath)); // ставим владельца таким же, как у исходного файла
        @chgrp($optimized_file, filegroup($fullpath)); // ставим группу такой же, как у исходного файла
        
        @unlink($tmpCompressedFile); // удаляем временный файл
        
        // успешно сохранен сжатый файл
        if($saved!==false)
        {
            clearstatcache(true, $fullpath); // чистим кеш информации по файлу - иначе php выдает старый mtime и size
            $newsize = filesize($fullpath);
            $newmtime = filemtime($fullpath);
            $indexItem->set('optimized', str_replace(MODX_BASE_PATH, '', $optimized_file));
            $indexItem->save();
        }
        // сжатый файл не удалось сохранить - фиксируем ошибку записи файла
        else
        {
            $modx->log(MODX_LOG_LEVEL_ERROR, "[OptiPic] Could not save {$file}");
            return $file;
            
        }
    }
    @unlink($tmpCompressedFile); // удаляем временный файл
}
return $output;
