<?php



class
Int64
{
  public $hi;
  public $lo;

  function __construct($hi=0,$lo=0){
    $this->hi = $hi;
    $this->lo = $lo;
  }

};




function
fget_8c($f)
{
  return unpack("Cvalue",fread($f,1))["value"];
}


function
fget_be16c($f)
{
  return unpack("nvalue",fread($f,2))["value"];
}


function
fget_be32c($f)
{
  return unpack("Nvalue",fread($f,4))["value"];
}


function
fget_be64c($f)
{
  $i64 = new Int64();

  $i64->hi = fget_be32c($f);
  $i64->lo = fget_be32c($f);

  return $i64;
}




function
fput_8c($c,$f)
{
  fwrite($f,pack("C",$c));
}


function
fput_be16c($c,$f)
{
  fwrite($f,pack("n",$c));
}


function
fput_be32c($c,$f)
{
  fwrite($f,pack("N",$c));
}


function
fput_be64c($i64,$f)
{
  fput_be32c($i64->hi,$f);
  fput_be32c($i64->lo,$f);
}




function
fgetpos($f,&$pos)
{
  $pos = ftell($f);
}

function
fsetpos($f,$pos)
{
  fseek($f,$pos);
}




