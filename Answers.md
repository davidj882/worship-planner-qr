## How can I upload MP3 files? ##
Any file format (PDF, MS Word, MP3) can be associated with a song.  The only limitation is file size.  The file `editSongs.php` sets a `MAX_FILE_SIZE` attribute that can be changed.  However, your ISP may also set a limit in your `php.ini` file.

## Why do I get the error `uploaded file is zero length` when trying to upload sheet music or MP3's? ##
This is usually due to the uploaded file size being greater than the max file size as set in `editSongs.php` or `php.ini`.