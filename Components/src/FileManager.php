<?php
namespace jikai\Components;

class FileManager
{
    protected $errorCode;
    protected $errorMsg;

    /**
     * @param $path
     * @return array|bool
     */
    public function ls($path)
    {
        if (!file_exists($path)) {
            $this->errorMsg = "the Path not exists";
            return false;
        }
        if (filetype($path) !== "dir") {
            $this->errorMsg = "it's not a dir";
            return false;
        }

        $files = scandir($path);//扫描目录获得文件名数组

        if (count($files) <= 2) {
            $this->errorMsg = "it's a empty dir";
            return false;
        }

        $fileNames = array_slice($files, 2);//去除./ ../

        $filesInfo = array(); //储存返回的数组

        foreach ($fileNames as $fileName) {
            $filePath = realpath($path . '/' . $fileName);
            $filesInfo[] = $this->fileInfo($filePath);
        }

        return $filesInfo;
    }

    public function fileInfo($filePath)
    {
        if (!file_exists($filePath)) {
            $this->errorMsg = "$filePath is not exist";
            return false;
        }
        $fileType = filetype($filePath);
        $fileSize = fileSize($filePath);
        return compact('filePath', 'fileSize', 'fileType');
    }


    /**
     * 如果是用户输入的文件名（从前端获得的）必须要限制，检查，否则有安全隐患
     * @param $file
     * @return string
     */
    public function rmFile($file)
    {

        if (!is_file($file)) {
            $this->errorMsg = "this is not a file";
            return false;
        }
        if (unlink($file)) {
            return true;
        } else {
            $this->errorMsg = "fail to remove the file!";
            return false;
        }
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

}