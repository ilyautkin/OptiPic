<?php
/** @var modX $modx */
if ($modx->event->name != 'OnPageNotFound') {return;}
$alias = $modx->context->getOption('request_param_alias', 'q');
if (!isset($_REQUEST[$alias])) {return;}

$request = $_REQUEST[$alias];

$dirs = explode('/', ltrim($request, '/'));
$root = array_shift($dirs);
if ($root != 'optipic.io') {return;}

$OptiPic = $modx->getService('OptiPic', 'OptiPic', MODX_CORE_PATH . 'components/optipic/model/', $scriptProperties);
if (!$OptiPic) {
    return 'Could not load OptiPic class!';
}
$optipicCfg = array(
    "secretkey" => $modx->getOption('optipic_secretkey'),
    "api_url" => "https://optipic.io/api/",
);

if(isset($_GET["cmd"]) && $_GET["cmd"]=="getversion") {
    echo serialize(array(
        "version" => "1.31",
    ));
    exit;
}
if(!isset($_REQUEST["secretkey"]) || $_REQUEST["secretkey"]!==$optipicCfg["secretkey"]) {
    echo serialize(array(
        "error" => "wrong_api_key",
    ));
    exit;
}
if (isset($_REQUEST["cmd"]) && $_REQUEST["cmd"]=="doreindex") {
    $statusIndexing = array("status"=>"indexing");
    
    // Задан ли последний проиндексированный файл?
    $byStep = (isset($_POST["by_step"]))? true: false;
    $lastProcessed = (isset($_POST["last_processed_file"])) ?trim($_POST["last_processed_file"]): "";
    
    $where = array('indexed' => 0);
    
    if ($byStep && $lastProcessed) {
        if ($lastProcessedIndexItem = $modx->newObject('OptiPicImage', array('file' => $lastProcessed))) {
            $where['id:>'] = $lastProcessedIndexItem;
        }
    }
    
    $start = time();
    $indexingFiles = array();
    
    $q = $modx->newQuery('OptiPicImage', $where);
    $q->limit(100);
    $q->select($modx->getSelectColumns('OptiPicImage'));
    $files = $modx->getCollection('OptiPicImage', $q);
    
    $i = 0;
    foreach ($files as $file) {
        // Начинаем формировать данные о файле для передачи на индексацию через API
        $dataForAPI = array(
            "path" => $file->file,
            "path_md5" => md5($file->file),
        );
        clearstatcache(true, MODX_BASE_PATH . $file->file);
        $dataForAPI["size_original"] = filesize(MODX_BASE_PATH . $file->file);
        $dataForAPI["mtime"] = date("Y-m-d H:i:s", filemtime(MODX_BASE_PATH . $file->file));

        $dataForAPI["size_compressed"] = 0;
        $dataForAPI["orig_exists"] = 0;
        if ($file->optimized && file_exists(MODX_BASE_PATH . $file->optimized)) {
            $dataForAPI["size_compressed"] = filesize(MODX_BASE_PATH . $file->optimized);
            $dataForAPI["orig_exists"] = 1;
        }

        // Передаем картинку на индексирование в API
        $indexingFiles[] = $dataForAPI;
        $file->set('indexed', true);
        $file->save();
        $timePass = time() - $start;
    }
    
    
    echo serialize(array(
        "status" => "indexing done",
        "files" => $indexingFiles
    ));
    exit;
}

if($_REQUEST["cmd"]=="docompress") {
    $listFiles = $_POST["files"];
    $postParams = array();
    $compressedFiles = array();
    
    foreach($listFiles as $file) {
        if (!$indexItem = $modx->newObject('OptiPicImage', array('file' => ltrim($file, '/'))) || !$indexItem->optimized) {
            continue;
        }
        $fullpath = MODX_BASE_PATH . $indexItem->optimized;
        
        // Файла не существует - говорим об этом сервису (чтобы он удалил файл из индекса)
        if(!file_exists($fullpath)) {
            $compressedFiles[] = array(
                "path" => $file,
                "compressed" => 0,
                "not_found" => 1,
            );
            continue;
        }
        
        clearstatcache(true, $fullpath);
        $newsize = filesize($fullpath);
        $newmtime = filemtime($fullpath);
        
        $compressedFiles[] = array(
            "path" => $file,
            "compressed" => 1,
            "size" => $newsize,
            "mtime" => $newmtime,
        );
    }
    
    echo serialize(array(
        "status" => "compressing done", 
        "files" => $compressedFiles
    ));
    exit;
}

if($_REQUEST["cmd"]=="doreturnorigs") {
    echo serialize(array(
        "status" => "done", 
    ));
    exit;
}

if($_REQUEST["cmd"]=="dodeleteorigs") {
    echo serialize(array(
        "status" => "done", 
    ));
    exit;
}
