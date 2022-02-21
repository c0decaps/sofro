<?php
header('Content-type: text/html; charset=utf-8');

// establish SQLite3 connection and get all station information
$db = new PDO('sqlite:data/rad10.db');
// get all stations
$statement = $db->query("SELECT * FROM stations");
$stations = $statement->fetchAll(PDO::FETCH_ASSOC);

$debug = true;
if(isset($_GET['cmd'])) {
  $cmd = $_GET['cmd'];
  if($cmd == "play") {        // PLAY
    if($debug === true) {
      echo "playing...<br>";
    }
    shell_exec("python3 ./controller.py play");
  } elseif($cmd == "edit") {  // EDIT

  } elseif($cmd == "del") {   // DELETE
      if(isset($_GET["id"])) {
        echo "executing query ".$query."...<br>";
        $query = "delete from stations where id=".$_GET["id"];
        $db->exec($query);
      }
  } elseif($cmd == "stop") {  // STOP
    if($debug === true) {
      echo "stopping...";
    }
    shell_exec("python3 ./controller.py stop");
  } elseif($cmd == "add") {   // ADD
    if(isset($_GET["name"]) && isset($_GET["url"])) {
      if($debug == true) {
        echo "adding new station with id ".count($stations).", name ".$_GET["name"]." and url ".$_GET["url"]."<br>";
      }
      if(filter_var($_GET["url"], FILTER_VALIDATE_URL)) {
        if($debug == true) {
          echo "valid url...<br>";
        }
        $station_count = count($stations);
        $name = $_GET["name"];
        $url = $_GET["url"];
        $query = "insert into stations values (".$station_count.",\"".$name."\",\"".$url."\")";
        if($debug == true) {
          echo "executing query: ".$query."...<br>";
        }
        $statement = $db->exec($query);
        if($debug == true) {
            echo "executed query<br>";
        }
      } else {
        echo "invalid url";
      }
    }
  } elseif($cmd == "stream") {  // PLAY FROM URL
    if(isset($_GET['link'])){
      $url = $_GET['link'];
      if($debug === true) {
        echo "received command to play ".$url."... ";
      }
      if(filter_var($url, FILTER_VALIDATE_URL)) {
        if($debug === true) {
          echo "valid url, trying to play ";
          echo shell_exec("python3 ./controller.py url ".$url);
        } else {
          shell_exec("python3 ./controller.py url ".$url);
        }
      } else {
        echo "invalid url";
      }
    } else {
      echo "no url";
    }
  }
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>radio</title>
  <link rel="icon" href="style/radio-icon.png">
  <link rel="stylesheet" href="style/grid.css">
  <link rel="stylesheet" href="style/audio-bar.css">
  <link rel="stylesheet" href="style/new-station-form.css">
  <script>
      function openForm(station_id) {
        document.getElementById("popupForm").style.display = "block";
      }
      function closeForm() {
        document.getElementById("popupForm").style.display = "none";
      }
      function showOpts(item) {
        console.log("showOpts");
        var child_elems = item.children;
        for(i = 0; i < child_elems.length; i++) {
          var classes = child_elems[i].className.split(" ");
          for(class_num = 0; class_num < classes.length; class_num++) {
            if(classes[class_num] == "edit-item" || classes[class_num] == "delete-item") {
              child_elems[i].style.display = "block";
            }
          }
        }
      }
      function hideOpts(item) {
        console.log("hideOpts");
        var child_elems = item.children;
        for(i = 0; i < child_elems.length; i++) {
          var classes = child_elems[i].className.split(" ");
          for(class_num = 0; class_num < classes.length; class_num++) {
            if(classes[class_num] == "edit-item" || classes[class_num] == "delete-item") {
              child_elems[i].style.display = "none";
            }
          }
        }
      }
      function getConfirmation() {
        return confirm("Delete Station?");
      }
  </script>
</head>
<body>
<div class="grid-container">
  <?php
    // dynamically create the station buttons, based upon the db entries
    for($station_id = 0; $station_id < count($stations); $station_id++) {
        echo "  <div class=\"grid-item\" onmouseenter=\"showOpts(this)\" onmouseleave=\"hideOpts(this)\">";
        echo "    <div class=\"delete-item item-options\">";
        echo "      <form action=\"\" onsubmit=\"getConfirmation()\">";
        echo "        <input type=\"hidden\" name=\"cmd\" value=\"del\" />";
        echo "        <input type=\"hidden\" name=\"id\" value=\"".$station_id."\" />";
        echo "        <input type=\"submit\" value=\"\" class=\"submit-option delete\" />";
        echo "      </form></div>";
        echo "        <a href=\"?cmd=stream&link=".$stations[$station_id]['url']."\"><p>";
        echo            $stations[$station_id]['name'];
        echo "        </p></a>";
        echo "  </div>";
    }
  ?>
  <div class="grid-item">
    <a href="?cmd=stop">
      <p>Stop</p>
    </a>
  </div>
  <div class="grid-item">
    <a href="?cmd=play">
      <p>Play</p>
    </a>
  </div>
  <div class="grid-item add" onclick="openForm()">
    <p>+</p>
  </div>
</div>

<!-- popup form to add new station -->
<div class="addPopup">
     <div class="formPopup" id="popupForm">
       <form action="" class="formContainer">
         <input type="hidden" name="cmd" value="add">
         <h2>Station Details</h2>
         <label for="name">
           <strong>Name</strong>
         </label>
         <input type="text" id="name" placeholder="Station Name" name="name" required>
         <label for="url">
           <strong>URL</strong>
         </label>
         <input type="url" id="url" placeholder="Stream URL" name="url" required>
         <button type="submit" class="btn">Submit</button>
         <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
       </form>
     </div>
</div>

<div class="audio-bar">
  <ul>
    <li class="stop"><a href="?cmd=stop"><div class="stop"></div></a></li>
    <li><a href="?cmd=play"><div class="play"></div></a></li>
  </ul>
  </div>

</body>
</html>
