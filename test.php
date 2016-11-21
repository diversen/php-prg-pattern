<?php


use diversen\prg;
include_once "../../autoload.php";

session_start();

prg::prg();

// a post form
function form_test (){ ?>
<form method ="post" action = "">
<input type ="text" name="test" value="<?=@$_POST['test']?>" /> <br />
<input type ="submit" name="submit" value="Send!" /> 
</form>
<?php }

// call post form
form_test();
