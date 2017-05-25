<?php


function
lock($number_of_count_for_trying=20,$time_for_waiting=1000000)
{
    for($n = 0;  $n < $number_of_count_for_trying;  ++$n)
    {
        if(rename("./_UNLOCKED","./_LOCKED.".getmypid()))
        {
          return true;
        }


      usleep($time_for_waiting);
    }


  return false;
}


function
unlock()
{
  return rename("./_LOCKED.".getmypid(),"./_UNLOCKED");
}




