<?php 

require_once "connection.php";

require '../extensions/PHPMailer/src/Exception.php';
require '../extensions/PHPMailer/src/PHPMailer.php';
require '../extensions/PHPMailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ModelRegister {

	static public function mdlRegisterPublicAccount($data){

		
		$db = new Connection();
        $pdo = $db->connect();

		$verify_token = substr(md5(rand()), 0,5);
		$account_type= "public";


		$mail = new PHPMailer(true);
		try {

		    //Server settings
			// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = 'testclgf@gmail.com';                     //SMTP username
			$mail->Password   = 'hggcmqxkxorglsrr';                               //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
			$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			$mail->setFrom('jajajo@gmail.com', 'JAJAJo');
			$mail->addAddress('janryanadivinagracia25@gmail.com', 'Joe User');     //Add a recipient

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Levites Registration Confirmation';
			$email_template = '<b>Welcome to Levites</b>
			<h1>Accept this request if you have registered! Disregard this message if not.</h1>
			<a href="http://localhost/levites/publicregistration/verify-email.php?token=$verify_token" ';
			$mail->Body    = '<b>Welcome to Levites</b>
								<h1>Accept this request if you have registered! Disregard this message if not.</h1>
								<h1>'. $verify_token.'</h1> ';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
				$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}


        try{
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();
			
			$stmt = $pdo->prepare("INSERT INTO account (acc_username,acc_password,acc_email,verify_token, acc_type) 
            VALUES (:acc_username, :acc_password, :acc_email, :verify_token, :acc_type)");

			// $stmt = $pdo->prepare("INSERT INTO register (AccountID,acc_username,acc_password,acc_email,acc_type,fname,lname,designation,acc_contact,religion,verify_token,created_at) 
            // VALUES (:AccountID,:acc_username,:acc_password,:acc_email,:acc_type,:fname,:lname,:designation,:acc_contact,:religion,:verify_token,:created_at)");

			$stmt->bindParam(":acc_username", $data["user_username"], PDO::PARAM_STR);
			$stmt->bindParam(":acc_password", $data["user_password"], PDO::PARAM_STR);
			$stmt->bindParam(":acc_email", $data["user_email"], PDO::PARAM_STR);
			$stmt->bindParam(":verify_token", $verify_token, PDO::PARAM_STR);
			$stmt->bindParam(":acc_type", $account_type, PDO::PARAM_STR);
			
			setcookie("current_email", $data["user_email"], time() + (86400 * 30), "/"); // 86400 = 1 day
			
			$stmt->execute();			





		    $pdo->commit();


			
		    return "ok";
		}catch (Exception $e){
			$pdo->rollBack();
			return "error";
		}	
		$pdo = null;	
		$stmt = null;

	}

	static public function mdlRegisterChurchAccount($data){

		
		$db = new Connection();
        $pdo = $db->connect();

		$verify_token = substr(md5(rand()), 0,5);
		$account_type= "admin";


		// to church
		$mail2 = new PHPMailer(true);
		try {

		    //Server settings
			// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail2->isSMTP();                                            //Send using SMTP
			$mail2->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
			$mail2->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail2->Username   = 'testclgf@gmail.com';                     //SMTP username
			$mail2->Password   = 'hggcmqxkxorglsrr';                               //SMTP password
			$mail2->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
			$mail2->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			$mail2->setFrom('jajajo@gmail.com', 'JAJAJo');
			$mail2->addAddress('janryanadivinagracia25@gmail.com', 'Joe User');     //Add a recipient

			//Content
			$mail2->isHTML(true);                                  //Set email format to HTML
			$mail2->Subject = 'Levites Registration Confirmation';
			$mail2->Body    = '<b>Welcome to Levites</b>
								<h1>Accept this request if you have registered! Disregard this message if not.</h1>
								<h1>'. $verify_token.'</h1> ';
			$mail2->AltBody = 'This is the body in plain text for non-HTML mail clients';
				$mail2->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail2->ErrorInfo}";
		}

        try{	
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();

			$current_year = substr(date('Y'), -2 );
			$current_month = date('n');

			$account_id = (new Connection)->connect()->prepare("SELECT CONCAT('A', LPAD((count(id)+1),4,'0'), '$current_month','$current_year') as account_id  FROM account FOR UPDATE");
			$account_id->execute();
			$accountid = $account_id -> fetchAll(PDO::FETCH_ASSOC);
			

			//add to account database
			$stmt = $pdo->prepare("INSERT INTO account (AccountID, acc_username,acc_password,acc_email,verify_token, acc_type) 
            VALUES (:AccountID, :acc_username, :acc_password, :acc_email, :verify_token, :acc_type)");

			// $stmt = $pdo->prepare("INSERT INTO register (AccountID,acc_username,acc_password,acc_email,acc_type,fname,lname,designation,acc_contact,religion,verify_token,created_at) 
            // VALUES (:AccountID,:acc_username,:acc_password,:acc_email,:acc_type,:fname,:lname,:designation,:acc_contact,:religion,:verify_token,:created_at)");

			$stmt->bindParam(":AccountID", $accountid[0]['account_id'], PDO::PARAM_STR);
			$stmt->bindParam(":acc_username", $data["church_username"], PDO::PARAM_STR);
			$stmt->bindParam(":acc_password", $data["church_password"], PDO::PARAM_STR);
			$stmt->bindParam(":acc_email", $data["church_email"], PDO::PARAM_STR);
			$stmt->bindParam(":verify_token", $verify_token, PDO::PARAM_STR);
			$stmt->bindParam(":acc_type", $account_type, PDO::PARAM_STR);
			
			setcookie("current_email", $data["church_email"], time() + (86400 * 30), "/"); // 86400 = 1 day
			$stmt->execute();		

			
			$church_id = (new Connection)->connect()->prepare("SELECT CONCAT('C', LPAD((count(id)+1),4,'0'), '$current_month','$current_year') as church_id  FROM churches FOR UPDATE");
			$church_id->execute();
			$churchid = $church_id -> fetchAll(PDO::FETCH_ASSOC);
			
			
			// add to church database
			$stmt2 = $pdo->prepare("INSERT INTO churches (churchID, church_name,church_email) 
            VALUES (:churchID, :church_name, :church_email)");

			// $stmt = $pdo->prepare("INSERT INTO register (AccountID,acc_username,acc_password,acc_email,acc_type,fname,lname,designation,acc_contact,religion,verify_token,created_at) 
            // VALUES (:AccountID,:acc_username,:acc_password,:acc_email,:acc_type,:fname,:lname,:designation,:acc_contact,:religion,:verify_token,:created_at)");

			$stmt2->bindParam(":churchID", $churchid[0]['church_id'], PDO::PARAM_STR);
			$stmt2->bindParam(":church_name", $data["church_name"], PDO::PARAM_STR);
			$stmt2->bindParam(":church_email", $data["church_email"], PDO::PARAM_STR);
			$stmt2->execute();		


			

		    $pdo->commit();


			
		    return "ok";
		}catch (Exception $e){
			$pdo->rollBack();
			return "error";
		}	
		$pdo = null;	
		$stmt = null;

	}




    static public function mdlVerifyRegistration($data) {
        $db = new Connection();
        $pdo = $db->connect();
		$success_stat = 1;
        
        try{
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();

			$stmt = $pdo->prepare("SELECT verify_token FROM account WHERE acc_email = :acc_email LIMIT 1");

            $stmt->bindParam(":acc_email", $_COOKIE['current_email'], PDO::PARAM_STR);

			$stmt->execute();
			$result = $stmt->fetch();
			

			if($result[0] == $data['code']){
				$status = $pdo->prepare("UPDATE account SET verify_status = :verify_status WHERE acc_email = :acc_email ");
				$status->bindParam(":verify_status", $success_stat, PDO::PARAM_INT);
				$status->bindParam(":acc_email", $_COOKIE['current_email'], PDO::PARAM_STR);
				$status->execute();
				$pdo->commit();
				echo "success";	
			}else{
				echo "fail";
			}

	
		    return "ok";
		}catch (Exception $e){
			$pdo->rollBack();
			return "error";
		}	
		$pdo = null;	
		$stmt = null;


    }

	static public function mdlResendVerification(){
		
		$db = new Connection();
        $pdo = $db->connect();
		

		$verify_token = substr(md5(rand()), 0,5);
		
		$mail = new PHPMailer(true);
		try {

		    //Server settings
			// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = 'testclgf@gmail.com';                     //SMTP username
			$mail->Password   = 'hggcmqxkxorglsrr';                               //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
			$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			$mail->setFrom('jajajo@gmail.com', 'JAJAJo');
			$mail->addAddress('janryanadivinagracia25@gmail.com', 'Joe User');     //Add a recipient

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Levites Registration Confirmation';
			$email_template = '<b>Welcome to Levites</b>
			<h1>Accept this request if you have registered! Disregard this message if not.</h1>
			<a href="http://localhost/levites/publicregistration/verify-email.php?token=$verify_token" ';
			$mail->Body    = '<b>Welcome to Levites</b>
								<h1>Accept this request if you have registered! Disregard this message if not.</h1>
								<h1>'. $verify_token.'</h1> ';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
				$mail->send();
			echo 'success';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

		    
        try{
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->beginTransaction();
			$status = $pdo->prepare("UPDATE account SET verify_token = :verify_token WHERE acc_email = :acc_email ");
			$status->bindParam(":verify_token", $verify_token, PDO::PARAM_STR);
			$status->bindParam(":acc_email", $_COOKIE['current_email'], PDO::PARAM_STR);
			$status->execute();
			$pdo->commit();
			echo "success";	
		}catch (Exception $e){
			$pdo->rollBack();
			return "error";
		}	
		$pdo = null;	
		$stmt = null;



	}

	


    
}





?>
