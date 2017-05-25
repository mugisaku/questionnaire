<?php
header('Content-Type: text/html; charset=UTF-8');
header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
header( 'Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', FALSE );


include_once"file.php";
include_once"mutex.php";
include_once"question.php";
include_once"ansman.php";

$answered = false;

  if(!lock())
  {
    echo "ロックできませんでした";

    die();
  }


  if(isset($_COOKIE["id"]))
  {
    $filepath = "./dir/".$_COOKIE["id"];

      if(file_exists($filepath))
      {
        $time_limit = stat($filepath)["mtime"]+(60*60*24*365);

          if($time_limit > time())
          {
            $answered = true;
          }

        else
          {
            unlink($filepath);

            setrawcookie("id");
          }
      }

    else
      {
        setrawcookie("id");
      }
  }
?>


<!DOCTYPE html>
<html>
<head>
</head>
<body>


<div>ソースファイル類は<a href="https://github.com/mugisaku/questionnaire.git">こちら</a></div>

<hr>

<div>

<?php

$question_list = load_question_file("./questions.txt");


$ans_table = load_dir("./dir/");


aggregate($question_list,$ans_table);


$number_of_entries = count($ans_table);


  if($answered)
  {
    echo "あなたは以前に回答しています<p>";
    echo "選択を変更して、再び送信すれば、回答を更新することができます<hr>";
 }


echo $number_of_entries," 人が回答しています<p>";

?>

</div>
<div>


<form action="submit.php" method="post">
<?php


  foreach($question_list as $q)
  {
    $q->to_html($number_of_entries);
  }


unlock();
?>
<hr>
<div align="center">
<input type="hidden" name="answers" value="1">
回答を<input type="submit">
</div>
<br><br><br><br>
<br><br><br><br>
</form>
</body>
</html>




