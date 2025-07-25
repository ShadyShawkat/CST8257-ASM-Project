<?php
// addfriend.php
// Page handling friend management

?>
<h1 class="h1">Add Friend</h1>
<?php loggedInMsg('WeeWoo') ?>
<p>Enter the ID of the user you want to be friend with</p>
<form method="post" name="addfriend" id="addfriend">
    <label for="id">ID : </label>
    <input type="text" name="id" id="id">
    <input type="submit" value="Send Friend Request">
</form>