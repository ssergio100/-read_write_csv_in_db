
<?php

    if (count($_FILES) > 0) {
        if ($_FILES['file']['error']<1) {
            include_once 'conn.php';
            $updated = 0;
            $inserts = 0;
            $arrSqlInsert = array();
            $arrSqlUpdate = array();

            if ($_FILES['file']['type'] == "text/csv") {

//                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
//                    //readfile($_FILES['file']['tmp_name']);
//                }

                $handle = fopen($_FILES['file']['tmp_name'], "r");

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
                            $sql = "UPDATE j34_users_2 SET activation = 1 WHERE id = '$id'";
                            array_push($arrSqlUpdate, $sql);
                        } else {
                            $sql = "INSERT INTO j34_users_2(name,username,email,password,activation,params,otpKey,otep,matriculaSuperior,produto,site,polo,eps,negocio)values('$name','$username','$email','$password','$activation','$params','$otpKey','$otep','$matriculaSuperior','$produto','$site','$polo','$eps','$negocio')";
                            array_push($arrSqlInsert, $sql);
                        }


                    }
                    // desativa todo mundo se houverem registros para atualizar
                    if(count($arrSqlUpdate)>0 || count($arrSqlInsert)>0) {
                        $sql = "UPDATE j34_users_2 SET activation = 0";
                        $conn->query($sql);
                    }

                    // reativa no banco os usuarios que constam no arquivo csv

                    if (count($arrSqlUpdate)) {
                        foreach ($arrSqlUpdate as $sqlUpdate) {
                            $conn->query($sqlUpdate);
                            $updated++;
                        }
                    }
                    // insere no banco os usuários que constam apenas arquivo csv
                    if (count($arrSqlInsert)) {
                        foreach ($arrSqlInsert as $sqlInsert) {
                            $conn->query($sqlInsert);
                            $inserts++;
                        }
                    }
                } else { $essageLog = "Ocorreu um erro ao abrir o arquivo no servidor";}
            } else { $messageLog = "O formato do arquivo deve ser .csv";}
        }else{$messageLog = uploadError($_FILES['file']['error']);}
    }//$_FILES  >0


fclose($handle);

function uploadError($err){
    switch ($err){
        case 1 :
            $message = "O arquivo enviado excede o limite definido";
            break;
        case 2 :
            $message = "O arquivo excede o limite definido no formulário HTML.";
            break;
        case 3 :
            $message = "O upload do arquivo foi feito parcialmente.";
            break;
        case 4 :
            $message = "Nenhum arquivo foi enviado.";
            break;
        case 5 :
            $message = "Pasta temporária ausênte.";
            break;
        case 6 :
            $message = "Pasta temporária ausênte. ";
            break;
        case 7 :
            $message = "Falha em escrever o arquivo em disco";
            break;
        case 8 :
            $message = "Uma extensão do PHP interrompeu o upload do arquivo.";
            break;
    }
    return $message;
}

?>