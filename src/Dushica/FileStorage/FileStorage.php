<?php namespace Dushica\FileStorage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorage{

    /**
     * Public path directory, started from '/public' folder
     */
    public static $publicStartPath = '/FileStorage';

    /**
     * The length of the key string associated to the image
     */
     public static $KeyLength = 32;

    /**
     * Put and save a file in the public directory
     *
     * @param string path of the file
     * @return string keypath of file
     */
    public static function put(UploadedFile $file)
    {
        //Remove all the slashes that doesn't serve
        FileStorage::clearPublicStartPath();

        //Retrive and save the file extension of the file uploaded
        $fileExtension = $file->getClientOriginalExtension();

        //Save the public path with the start path
        $absolutePath = public_path().'/'.FileStorage::$publicStartPath;

        //Generate a random name to use for the file uploaded
        $keyFile = FileStorage::generateKey(FileStorage::$KeyLength).'.'.$fileExtension;

        //Check if the file with the $keyFile name doesn't exist, else, regenerate it
        while(file_exists($absolutePath.'/'.ord($keyFile[0]).'/'.$keyFile))
            $keyFile = FileStorage::generateKey(FileStorage::$KeyLength).'.'.$fileExtension;

        //Move the uploaded file and save
        $file->move($absolutePath.'/'.ord($keyFile[0]), $keyFile);

        //Save the keypath (start path, sub path, file name)
        $keyPath = FileStorage::$publicStartPath.'/'.ord($keyFile[0]).'/'.$keyFile;

        //Return public path of the file
        return $keyPath;
    }

    /**
     * Delete a file in from the public directory with the keyPath associated
     *
     * @param string path of the file
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public static function delete($keyPath)
    {
        return unlink($keyPath);
    }

    /**
     * Generate a random string
     *
     * @param int length of the key
     * @return string random string
     */
    public static function generateKey($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Generate a random string
     *
     * @return void
     */
    public static function clearPublicStartPath()
    {
        if(FileStorage::$publicStartPath[0]=='/')
        {
            FileStorage::$publicStartPath = substr(FileStorage::$publicStartPath, 1);
            FileStorage::clearPublicStartPath();
        }
    }

}
