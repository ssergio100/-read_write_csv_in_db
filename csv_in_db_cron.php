<?php
    include_once "conn.inc.php";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
            $updated = 0;
            $inserts = 0;
            $id = '';
            $sqlInsertValues = '';
            $sqlUpdate = '';
            $sqlCreateFile = true;
            $csvName = 'base.csv';
            $csvPath = '/home/sergio/centesimo/read_write_csv_in_db/';
            $csvPathName = '/home/sergio/centesimo/read_write_csv_in_db/'.$csvName;
            $sqlUpdateFile = $csvPath.'sql/sqlUpdate.sql';
            $sqlInsertFile = $csvPath.'sql/sqlInsert.sql';
            $pathLog = $csvPath.'log.txt';
            $insertColumns = "INSERT INTO j34_users_2(name,username,email,password,activation,params,otpKey,otep,matriculaSuperior,produto,site,polo,eps,negocio)VALUES";
            $messageLog = '';
            $now = date('d-m-Y H:i:s',time());
            $time_start = microtime_float();

            if(file_exists($csvPathName)) {
                $handle = fopen($csvPathName, "r");
                if ($handle) {
                    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                        $name = $data[0];
                        $username = $data[0];
                        $email = $data[0] . '@pim.com.br';
                        $password = md5('mudar');
                        //$block = 0;
                        //$sendEmail = 0;
                        //$registerDate
                        //$lastVisitDate
                        $activation = 0;
                        $params = '';
                        //$lastResetTime
                        //$resetCount
                        $otpKey = '';
                        $otep = '';
                        //$requireReset
                        //$requireRePass
                        $matriculaSuperior = '';
                        $produto = $data[2];
                        $site = $data[3];//ok
                        $polo = $data[4];//ok
                        $eps = $data[5];
                        $negocio = $data[6];


                        /*  Verifico se o registro já existe */

                        $sqlExist = "SELECT id from j34_users_2 WHERE name = '$name'";
                        $result = $conn->query($sqlExist);
                        $res = $result->fetch();
                        $id = $res['id'];
                        if ($id) {
                            $sqlUpdate .= "UPDATE j34_users_2 SET activation = 1 WHERE id = '$id';";
                            $updated++;
                        } else {
                            $sqlInsertValues .= "('$name','$username','$email','$password','$activation','$params','$otpKey','$otep','$matriculaSuperior','$produto','$site','$polo','$eps','$negocio'),";
                            $inserts++;
                        }

                    }
                    // desativa todo mundo se houverem registros para atualizar
                    if ($sqlUpdate != '' || $sqlInsertValues != '') {
                        $sql = "UPDATE j34_users_2 SET activation = 0";
                        $conn->query($sql);
                    }
                    //UPDATE

                    if ($sqlUpdate != '') {
                        $sqlUpdate = rtrim($sqlUpdate, ';');
                        if ($sqlCreateFile) {
                            $handleUpdate = fopen($sqlUpdateFile, 'w+');
                            //$handleUpdate = file_get_contents($sqlUpdateFile);
                            fwrite($handleUpdate, $sqlUpdate);
                            fclose($handleUpdate);
                        }
                        $conn->query($sqlUpdate);
                    }
                    //INSERT
                    if ($sqlInsertValues != '') {
                        $sqlInsertValues = $insertColumns . rtrim($sqlInsertValues, ',');
                        if ($sqlCreateFile) {
                            $handleInsert = fopen($sqlInsertFile, 'w+');
                            //$handleUpdate = file_get_contents($sqlInsertFile);
                            fwrite($handleInsert, $sqlInsertValues);
                            fclose($handleInsert);
                        }
                        $conn->query($sqlInsertValues);
                    }
                } else {$messageLog .= "[Ocorreu um erro ao abrir o arquivo no servidor]";}
            }else{$messageLog .= "[O arquivo csv não foi encontrado]";}

    fclose($handle);

    $time_end = microtime_float();
    $time = $time_end - $time_start;

    $messageLog .= '['.$now.'] Updates ['.$updated.'] Inserts ['.$inserts.'] Tempo ['.$time.']'. "\n";
    $handle = fopen($pathLog,'a+');
    fwrite($handle,$messageLog);
    fclose($handle);

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
