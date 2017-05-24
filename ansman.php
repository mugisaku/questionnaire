<?php
include_once"file.php";
include_once"mutex.php";
include_once"question.php";


function
load_dir($path)
{
  $filename_list = array();

  $dir = opendir($path);

    if($dir)
    {
      $stack = array();

      $now = time();

        for(;;)
        {
          $filename = readdir($dir);

            if(!$filename)
            {
              break;
            }


            if(($filename != ".") &&
               ($filename != ".."))
            {
                if(strncmp($filename,"_TMP",4) == 0)
                {
                  $stack[] = $path.$filename;
                }

              else
                {
                  $time_limit = stat($path.$filename)["mtime"]+(60*60*24*365);

                    if($time_limit > $now)
                    {
                      $filename_list[] = $filename;
                    }

                  else
                    {
                      $stack[] = $filename;
                    }
                }
            }
        }


        foreach($stack as $value)
        {
          @unlink($path.$value);
        }
    }


  return $filename_list;
}




