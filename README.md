# rsa-encryption-form
Simple RSA encryption for in PHP for Net-centric Computing

<a href="https://ibb.co/VWY32KB"><img src="https://i.ibb.co/7Jtb29y/Assignment-11-Midiel-Rodriguez.png" alt="Assignment-11-Midiel-Rodriguez" border="0"></a>

<br>
<h5>1) Create database</h5>
-- create database --<br>
CREATE DATABASE assignment11;<br><br>

-- create encryption table --         <br>
CREATE TABLE encryption (             <br>
	id int AUTO_INCREMENT,              <br>
	public_key VARCHAR(250) NOT NULL,   <br>
	message VARCHAR(250),               <br>
	PRIMARY KEY (id));                  <br><br>
  
  <h5>2) Modify the connection to your database</h5><br>
  	- Open <b>config/connect.inc.php</b> and modify the connection sessting to match that of your database.<br><br>
  
  <h5>3) Host the application and local database using XAMPP or any other tool</h5>
  
  
  
  
