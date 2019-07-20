<?php 

    // starts session
	session_start();

	// conection to the database
	require_once('config/connect.inc.php');

    // for the RSA encryption
    include('Crypt/RSA.php');
    

	//print_r ($_POST);
 
	function createKeyPair() {

        $rsa = new Crypt_RSA();
        $keys=$rsa->createKey(1024);     
		$_SESSION['privateKey']=$keys['privatekey'];
		$_SESSION['publickey']=$keys['publickey'];
    }
 
    function encrypt($publicKey, $plaintext){

        $rsa = new Crypt_RSA();
        $rsa->loadKey($publicKey);
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $ciphertext = base64_encode($rsa->encrypt($plaintext));
        return $ciphertext;
    }

    function decrypt($privateKey, $encryptedText){

        $rsa = new Crypt_RSA();
        $rsa->loadKey($privateKey);
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $ciphertext = $rsa->decrypt(base64_decode($encryptedText));
        return $ciphertext;

    }

	if(isset($_POST['encrypt']) && !empty($_POST['encrypt'])){

		// only generate one key pair per user session, to speedup page
		if(!isset($_SESSION['privateKey'])){

			// create encryption keys
			createKeyPair();
		}
		
		$_SESSION['plaintext'] = $_POST['encrypt'];
	
        // encrypt the message
        $encryptedText = encrypt($_SESSION['publickey'], $_SESSION['plaintext']);
		$_SESSION['cyphertext']  = $encryptedText;
				
		$publicKey = $_SESSION['publickey'];
		
		//save to database
		$query = $con->prepare('INSERT INTO encryption (public_key, message) VALUES (?,?)');
		$query->bind_param('ss', $publicKey, $encryptedText);
		$query->execute();
		$query->close();


	} else if(isset($_POST['decrypt']) && !empty($_POST['decrypt'])){
        		
        $query = "SELECT public_key, message FROM encryption ORDER BY id DESC LIMIT 1";

        if($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_array($result);
            $_SESSION['publickey'] = $row['public_key'];
			$_SESSION['cyphertext'] = $row['message'];
            $_SESSION['decripted'] = decrypt($_SESSION['privateKey'], $_SESSION['cyphertext']);
        }
	
		// free result
		mysqli_free_result($result);
		
		// Close Connection
    	mysqli_close($con);
	}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Assignment 11</title> 			
</head>


<body>

	<div class="container">
		<div class="row border border-info m-5 p-2">
			<div class="col-sm-12 ">
				<div class="row ">
					<p class="ml-5">
						<h3>Enter Text to encrypt</h3>
					</p>         
				</div>
				<form method="post" action="index.php" class="container">
					<div class="form-group">        
						<textarea class="form-control" id="encrypt" name="encrypt" maxlength="250" rows="3"><?php
							if(isset($_SESSION['plaintext'])){
								echo $_SESSION['plaintext'];
							}?>
						</textarea>
					</div>
					<button type="submit" class="btn btn-primary">Encrypt Text</button>
				</form>		
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row m-5">
			<div class="col-sm-4 border border-info p-2">
				<div class="row ">
					<p class="ml-5">
						<h4>Encrypted Text</h3>
					</p>              
				</div>
				<form method="post" action="index.php" class="container">
					<div class="form-group ml-1">        
						<textarea class="form-control" placeholder="Enter text to encrypt" id="decrypt" name="decrypt" maxlength="255" rows="5"><?php 
							if(isset($_SESSION['cyphertext'])){
								echo $_SESSION['cyphertext'];
							}?>
						</textarea>
					</div>
					<button type="submit" class="btn btn-primary ml-2">Decrypt</button>
				</form>
			</div>

            <div class="col-sm-4 border border-info p-2">
				<div class="row ml-2 mr-2">
					<p class="ml-5">
						<h4>Public Key</h3>
					</p>
                    <textarea class="form-control" id="decrypt" name="decrypt" maxlength="255" rows="5"><?php 
						if(isset($_SESSION['publickey'])){
							echo $_SESSION['publickey'];
						}?>
					</textarea>         
				</div>
			</div>
 
            <div class="col-sm-4 border border-info p-2">
				<div class="row ml-2 mr-2">
					<p class="ml-5">
						<h4>Private Key</h3>
					</p>
                    <textarea class="form-control" id="decrypt" name="decrypt" maxlength="255" rows="5"><?php 
						if(isset($_SESSION['privateKey'])){
							echo $_SESSION['privateKey'];
						}?>
					</textarea>         
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row border border-info m-5 p-2">
			<div class="col-sm-12">
				<div class="row ">
					<p class="ml-5">
						<h3>Decrypted Text</h3>
					</p>          
				</div>
				<div class="form-group ml-3 mr-3">        
					<textarea class="form-control" id="decrypt" name="decrypt" maxlength="255" rows="3"><?php 
						if(isset($_POST['decrypt'])){
							echo $_SESSION['decripted'];
						}?>
					</textarea>
				</div>	
			</div>
		</div>
	</div>
	
</body>

</html>