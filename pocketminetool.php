<?php
/**
*@author:lovol(qq:1669439552/3445778610)
*有问题可以联系我
*\(  ͯω  ͯ)/
*/



function colorText($text, $colorCode) {
    return "\033[" . $colorCode . "m" . $text . "\033[0m";
}

function successMessage($message) {
    echo colorText($message . "\n", "32"); 
}

function errorMessage($message) {
    echo colorText("错误: " . $message . "\n", "31"); 
    exit(1);
}

function checkExistence($path, $isFile = true) {
    if ($isFile && !file_exists($path)) {
        errorMessage("文件不存在: $path");
    } elseif (!$isFile && !is_dir($path)) {
        errorMessage("目录不存在: $path");
    }
}
function displayMenu() {

    echo colorText("=============================\n", "36"); 
    echo colorText(" 请选择操作：\n", "36");
    echo " 1. 解包 \n";
    echo " 2. 打包 \n";
    echo " 3. 获取 Phar stub \n";
    echo " 4. 添加 Phar stub \n";
    echo colorText("=============================\n", "36");
}

function prompt($message) {
    echo colorText($message, "33"); 
    return trim(fgets(STDIN));
}

function unpackPhar() {
    $input = prompt("输入要解包的 phar 文件的名字（不带 .phar 后缀）：");
    $input2 = prompt("输入解压的目标文件夹名字：");
    $pharFile = "$input.phar";

    try {
        checkExistence($pharFile);

        if (!mkdir($input2, 0700) && !is_dir($input2)) {
            throw new Exception("无法创建目标文件夹: $input2");
        }

       
                     $phar = new Phar($pharFile);
        $phar->extractTo($input2);
        successMessage("解包完成！文件已解压至：$input2");
    } catch (Exception $e) {
        errorMessage($e->getMessage());
    }
}

function packPhar() {
    $inputFolder = prompt("输入要打包的文件夹名字：");
    $outputPhar = prompt("输入打包后的 phar 文件名（不带 .phar 后缀）：");
    $pharFile = "$outputPhar.phar";

    try {
        checkExistence($inputFolder, false);

        $phar = new Phar($pharFile);
        $phar->buildFromDirectory($inputFolder);
        $phar->setStub($phar->createDefaultStub('index.php')); 
        successMessage("打包完成！生成的 Phar 文件：$pharFile");
    } catch (Exception $e) {
        errorMessage($e->getMessage());
    }
}

function getPharStub() {
    $pharName = prompt("输入要获取 stub 的 phar 文件名（不带 .phar 后缀）：");
    $pharFile = "$pharName.phar";

    try {
        checkExistence($pharFile);
        
        $phar = new Phar($pharFile);
        $stub = $phar->getStub();

        echo colorText("当前 Phar 的 stub 内容:\n", "36"); // 青色
        echo colorText("-------------------------\n", "36");
        echo $stub . "\n";
        echo colorText("-------------------------\n", "36");
    } catch (Exception $e) {
        errorMessage($e->getMessage());
    }
}

function setPharStub() {
    $pharName = prompt("输入要添加 stub 的 phar 文件名（不带 .phar 后缀）：");
    $pharFile = "$pharName.phar";
    $newStub = prompt("输入新的 stub 内容（以一行输入）：");

    try {
        checkExistence($pharFile); 
        
        
        $phar = new Phar($pharFile);
        $phar->setStub($newStub);
        successMessage("Stub 已成功更新！");
    } catch (Exception $e) {
        errorMessage($e->getMessage());
    }
}


while (true) {
    displayMenu();
    $operation = trim(fgets(STDIN));

    switch ($operation) {
        case '1':
            unpackPhar();
            break;
        case '2':
            packPhar();
            break;
        case '3':
            getPharStub();
            break;
        case '4':
            setPharStub();
            break;
        default:
            errorMessage("无效的操作。");
    }

    $continue = prompt("是否继续操作？(y/n)：");
    if (strtolower($continue) !== 'y') {
        successMessage("操作结束，感谢使用！");
        break;
    }
}
?>
