<?php

include_once"pointer.php";
include_once"file.php";


class
Answer
{
  private  $question;
  private  $index;
  private  $text;
  private  $count;
  private  $checked;

  function __construct($question,$index,$text,$checked=false)
  {
    $this->question = $question;
    $this->index    = $index   ;
    $this->text     = $text    ;
    $this->count    =         0;
    $this->checked  = $checked ;
  }

  function get_text()
  {
    return $this->text;
  }

  function get_count()
  {
    return $this->count;
  }

  function increase_count()
  {
    ++$this->count;
  }

  function check()
  {
    $this->checked = true;
  }

  function to_html($number_of_entries)
  {
    $type = $this->question->is_multi()? "checkbox":"radio";

    echo "<tr><td>";
    echo "<input type=\"",$type,"\" name=\"question",$this->question->get_index(),"[]\" ";
    echo        "value=\"answer",$this->index,"\"";

      if($this->checked)
      {
        echo  ' checked="checked"';
      }


    echo ">";
    echo $this->text;
    echo "</td>";

      if($number_of_entries)
      {
        echo "<td align=\"right\">",floor($this->count/$number_of_entries*100),
          "%(",$this->count,"/",$number_of_entries,")</td>";
      }


    echo "</tr>";
  }

};


class
Question
{
  private $index;
  private $text;
  private $multi;
  private $answer_table;

  function __construct($ptr)
  {
    $this->index = 0;
    $this->multi = false;

    $this->text = array();

      while($ptr->test())
      {
        $c = $ptr->get_char(1);

          if(($c >= "0"[0]) &&
             ($c <= "9"[0]))
          {
            $this->index *= 10;
            $this->index += $c;
          }

        else
          {
            break;
          }
      }


      while($ptr->test())
      {
          if($ptr->match("?",1))
          {	
            $ptr->advance(1);

            break;
          }

        else
          if($ptr->match("*?",2))
          {	
            $this->multi = true;

            $ptr->advance(2);

            break;
          }


        $this->text[] = $ptr->get_char(1);
      }


    $this->text = implode($this->text);
    $this->answer_table = array();

    $n = 0;

      while($ptr->test())
      {
        $text = array();

        $ptr->skip_spaces();

          while($ptr->test())
          {
            $c = $ptr->get_char(1);

              if($c == ":"[0])
              {
                $this->answer_table[] = new Answer($this,$n++,implode($text));

                break;
              }

            else
              if($c == ";"[0])
              {
                $this->answer_table[] = new Answer($this,$n++,implode($text));

                return;
              }


           $text[] = $c;
         }
      }
  }


  function get_text()
  {
    return $this->text;
  }

  function get_number_of_answers()
  {
    return count($this->answer_table);
  }

  function is_multi()
  {
    return $this->multi;
  }

  function get_index()
  {
    return $this->index;
  }

  function &get_answer($i)
  {
    return $this->answer_table[$i];
  }

  function to_html($number_of_entries)
  {
    echo "<hr><h3>","Q. ",$this->text,"?</h3>";

      if($this->multi)
      {
        echo "（複数回答）";
      }


    echo "<table border=\"1px\">";

      foreach($this->answer_table as $a)
      {
        $a->to_html($number_of_entries);
      }


    echo "</table>";
  }

};



function
load_question_file($path)
{
  $ptr = new Pointer(file_get_contents($path));

  $qlist = array();

    while($ptr->skip_spaces())
    {
      $q = new Question($ptr);

      $qlist[$q->get_index()] = $q;
    }


  return $qlist;
}



function
aggregate(&$qlist,$dir)
{
    foreach($dir as $value)
    {
      $f = fopen("./dir/".$value,"rb");

        if($f)
        {
          $owner = isset($_COOKIE["id"])? ($_COOKIE["id"] == $value):false;

          $n = fget_be16c($f);

            while($n--)
            {
              $q = $qlist[fget_be16c($f)];

              $an = fget_8c($f);

                while($an--)
                {
                  $ai = fget_8c($f);

                  $a = $q->get_answer($ai);

                  $a->increase_count();

                    if($owner)
                    {
                      $a->check();
                    }
                }
            }


          fclose($f);
        }
    }
}




