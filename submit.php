<?php
header('Content-Type: text/html; charset=UTF-8');


require_once"mutex.php";
require_once"file.php";
require_once"question.php";


function
write($f)
{
  $question_list = load_question_file("./questions.txt");

  $table = array();

    foreach($_POST as $key => $value)
    {
      $qd = 0;
      $ad = 0;

        if(sscanf($key,"question%d",$qd) == 1)
        {
          $q = $question_list[$qd];

          $table[$qd] = array();

            if(is_array($value))
            {
                foreach($value as $content)
                {
                    if(is_string($content) && (sscanf($content,"answer%d",$ad) == 1))
                    {
                      $table[$qd][] = $ad;

                      echo $q->get_text(),"... ",$q->get_answer($ad)->get_text(),"<br>";
                    }
                }
            }
        }
    }


  fput_be16c(count($table),$f);

    foreach($table as $key => $value)
    {
      fput_be16c($key,$f);

      fput_8c(count($value),$f);

        foreach($value as $element)
        {
          fput_8c($element,$f);
        }
    }
}



const RES_failure  = -1;
const RES_inserted =  0;
const RES_updated  =  1;


function
accept()
{
  $result = RES_inserted;

  $filename = sprintf("%016x",time());

  $f = fopen("./dir/_TMP".$filename,"wb");

    if(!$f)
    {
      throw new Exception("ファイルを作成できませんでした");
    }


  write($f);

  fclose($f);

    if(isset($_COOKIE["id"]))
    {
        if(@unlink("./dir/".$_COOKIE["id"]))
        {
          $result = RES_updated;
        }


      setrawcookie("id");
    }


    if(!rename("./dir/_TMP".$filename,"dir/".$filename))
    {
      $msg = "新ファイルを名前変更できませんでした";

      throw new Exception($msg);
    }


  setrawcookie("id",$filename,time()+(60*60*24*365));

  return $result;
}


?>


<!DOCTYPE html>
<html>
<head>
</head>
<body>


<div>
<?php
$result = 0;

  if(isset($_POST["answers"]))
  {
      if(!lock())
      {
        echo "ロックできませんでした";

        $result = RES_failure;
      }

    else
      {
          try
          {
            $result = accept();
          }


          catch(Exception  $e)
          {
            echo $e->getMessage(),"<p>";
          }


        unlock();
      }
  }


echo "<hr>";

  switch($result)
  {
case(RES_failure ): echo"時間を置いて、再試行してください";break;
case(RES_inserted): echo"投票しました";break;
case(RES_updated ): echo"回答を更新しました";break;
  }
?>
</div>

<a href="./index.php">戻る</a>
</body>
</html>




