<?php
 
    $dbconnect = mysqli_connect("localhost", "root", "", "ffd");
    $database_name = "ffd";
    $table_name = "devices";
    $apiValues = $_REQUEST['sensorValues'];
     
  if(mysqli_connect_errno()) {
    echo "Connection failed:".mysqli_connect_error();
    exit;
  }
  else{


    ///$db_selected = mysqli_select_db($dbconnect, $database_name);

    if(! mysqli_select_db($dbconnect, $database_name)){
      $sql = "CREATE DATABASE ".$database_name;

      if (mysqli_query($dbconnect, $sql)) {
        # code...
        // echo "Database ".$table_name." created Successfully!";
      }
      else {
        # code...
        echo "ERROR: creating database ".$table_name."! ".mysqli_error()."\n";
      }
    }
    else{
        $tbl_iot = "CREATE TABLE IF NOT EXISTS ".$table_name." (
        id int NOT NULL primary key AUTO_INCREMENT,
        device_id VARCHAR(50) NOT NULL,
        temperature int NOT NULL,
        humidity int NOT NULL,
        carbon int NOT NULL,
        gas int NOT NULL,
        smokeValue int NOT NULL
        )";


        if (!mysqli_query($dbconnect, $tbl_iot)) {
          # code...
          echo "<h3>ERROR: could not execute ".$tbl_iot." </h3>".mysqli_error($dbconnect);
        }
        else{
            try{
                $temperature = $apiValues['temp'];
                $humidity    = $apiValues['humid'];
                $carbon      = $apiValues['carbon']; 
                $smokeValue  = $apiValues['gas'];
                $device_id   = $apiValues['device_id'];
                if ($smokeValue === 1) {
                        $gas = rand(1,400);
                    }
                else {
                        $gas = 0;
                    }

                $sql = "INSERT INTO ".$table_name." (device_id, temperature, humidity, carbon, gas, smokeValue) VALUES (  '$device_id', '$temperature', '$humidity', '$carbon', '$gas', '$smokeValue')";

                if(mysqli_query($dbconnect, $sql)){
                    echo "<h3>Data Inserted Successfully! into ".$table_name." </h3>";  
                }
                else{
                    echo "Data Insertion Errror! ".mysqli_error($dbconnect);
                }
            }
            catch(Exception $e){
                echo $e -> getMessage();
            }
        }
    }

  }
  echo "</div>";
