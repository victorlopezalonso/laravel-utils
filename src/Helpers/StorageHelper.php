<?php

namespace Victorlopezalonso\LaravelUtils\Helpers;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacade;

class StorageHelper
{
    /**
     * @param string $directory
     * @param string $url
     *
     * @return string
     */
    public static function saveUrlToLocalDisk(string $directory, $url)
    {
        return self::saveUrlToDisk('local', $directory, $url);
    }

    /**
     * @param string $directory
     * @param string $url
     *
     * @return string
     */
    public static function saveUrlToPublicDisk(string $directory, $url)
    {
        return self::saveUrlToDisk('public', $directory, $url);
    }

    /**
     * @param string $directory
     * @param string $requestFileName
     *
     * @return string
     */
    public static function saveUploadedFileToLocalDisk(string $directory, string $requestFileName)
    {
        return self::saveUploadedFileToDisk('local', $directory, $requestFileName);
    }

    /**
     * @param string $directory
     * @param string $requestFileName
     *
     * @return string
     */
    public static function saveUploadedFileToPublicDisk(string $directory, string $requestFileName)
    {
        return self::saveUploadedFileToDisk('public', $directory, $requestFileName);
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    public static function saveBinaryFileToLocalDisk(string $directory)
    {
        return self::saveBinaryFileToDisk('local', $directory);
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    public static function saveBinaryFileToPublicDisk(string $directory)
    {
        return self::saveBinaryFileToDisk('public', $directory);
    }

    /**
     * @param string                                   $directory
     * @param null|array|\Illuminate\Http\UploadedFile $file
     *
     * @return null|string
     */
    public static function saveEncryptedFile(string $directory, UploadedFile $file)
    {
        $encryptedFile = Crypt::encrypt(file_get_contents($file->getRealPath()));

        $filename = uniqid('', true).'.'.FileFacade::extension($file->getClientOriginalName());

        Storage::put($directory.$filename, $encryptedFile);

        return $filename;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getEncryptedFile(string $filename)
    {
        return Crypt::decrypt(Storage::get($filename));
    }

    /**
     * @param string $directory
     * @param string $file
     */
    public static function deleteFileFromLocalDisk(string $directory, string $file = null)
    {
        self::deleteFileFromDisk('local', $directory, $file);
    }

    /**
     * @param string $directory
     * @param string $file
     */
    public static function deleteFileFromPublicDisk(string $directory, string $file = null)
    {
        self::deleteFileFromDisk('public', $directory, $file);
    }

    /**
     * @param string $disk
     * @param string $directory
     * @param string $requestFileName
     *
     * @return string
     */
    private static function saveUploadedFileToDisk(string $disk, string $directory, string $requestFileName)
    {
        if ($uploadedFile = request()->file($requestFileName)) {
            return basename(Storage::disk($disk)->putFile($directory, $uploadedFile));
        }

        return null;
    }

    /**
     * @param string $disk
     * @param string $directory
     * @param string $url
     *
     * @return string
     */
    private static function saveUrlToDisk(string $disk, string $directory, string $url)
    {
        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($temp, file_get_contents($url));

        $uploadedFile = new UploadedFile(realpath($temp), basename($temp));

        return basename(Storage::disk($disk)->putFile($directory, $uploadedFile));
    }

    /**
     * @param string $disk
     * @param string $directory
     *
     * @return string
     */
    private static function saveBinaryFileToDisk(string $disk, string $directory)
    {
        $path = 'storage/'.uniqid('', true);

        FileFacade::put($path, request()->getContent());

        $basename = basename(Storage::disk($disk)->putFile($directory, new File($path)));

        FileFacade::delete($path);

        return $basename;
    }

    /**
     * @param string $disk
     * @param string $directory
     * @param string $file
     */
    private static function deleteFileFromDisk(string $disk, string $directory, string $file = null)
    {
        if ($file) {
            $filePath = $directory.basename($file);
            Storage::disk($disk)->delete($filePath);
        }
    }
}
