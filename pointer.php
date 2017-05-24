<?php




class
Pointer
{
  private $str;
  private $pos;

  function __construct($str,$pos=0)
  {
    $this->str = $str;
    $this->pos = $pos;
  }

  function get_char($n=0)
  {
    $c = $this->str[$this->pos];

    $this->pos += $n;

    return $c;
  }

  function advance($n=1)
  {
    $this->pos += $n;
  }

  function test()
  {
    return isset($this->str[$this->pos]);
  }

  function match($src_str,$n=0)
  {
    $tmp_pos = $this->pos;
    $src_pos =          0;

      while($n--)
      {
          if(isset($this->str[$tmp_pos]) &&
             isset($src_str[  $src_pos]))
          {
            $tmp_c = $this->str[$tmp_pos];
            $src_c = $src_str[  $src_pos];

              if($tmp_c != $src_c)
              {
                return false;
              }


            ++$tmp_pos;
            ++$src_pos;
          }

        else
          {
            return false;
          }
      }


    return true;
  }


  function  skip_spaces()
  {
      while($this->test())
      {
        $c = $this->get_char();

          if(($c != "\n"[0]) &&
             ($c != "\t"[0]) &&
             ($c != " "[0]))
          {
            break;
          }


        $this->advance();
      }


    return $this->test();
  }


};




