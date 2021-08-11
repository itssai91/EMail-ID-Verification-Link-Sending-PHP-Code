<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!--Bootstrap Css-->
	<link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
	<!--fontAwesome-->
	<link rel="stylesheet" href="assets/fontawesome-free-5.11.2-web/css/all.min.css">
	<link rel="stylesheet" href="assets/css/custom.css">
	<title>Email Confirmation Link</title>
</head>

<body>

	<div class="container">

		<h1 class="text-danger text-center">Register Email ID</h1>

		<form method="post">
			<div class="form-group">
				<label>User Name</label> <input type="text" name="email" class="form-control" />
			</div>
			<div class="form-group">
				<label>Password</label> <input type="password" name="password" class="form-control" />
			</div>

			<input type="submit" name="register" class="btn btn-danger btn-lg" value="Register" />
		</form>
	</div>



	<!--JavaScripts-->
	<script src="assets/bootstrap/bootstrap.min.js"></script>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script type="text/javascript" src="assets/js/ckeditor/ckeditor.js"></script>
</body>

</html>

<?php
ob_start();

register_user();

function register_user()
{
	if (isset($_POST['register'])) {

		$email = $_POST['email'];
		$password = md5($_POST['password']);
		$token = md5($_POST['email'] . random_int(99, 99999));
		$link = "<a href='https://example.com/mail_varification/confirm_mail.php?key=" . $email . "&token=" . $token . "'>Click To Verify Your Email</a>";

		require_once 'db_connection.php';
		$query = "SELECT * FROM `users` WHERE `email` = '$email'";
		$result = $conn->query($query);
		$row = mysqli_fetch_array($result);
		if ($row['email_verified_at'] != null) {
			echo '<script>alert("Please check your mail and verify")</script>';
		} else if ($row['email_verified_at'] == null) {
			echo '<script>alert("Your email is not verified please check your inbox and verify the email")</script>';
		} else {
			$sql = "INSERT INTO `users`(`email`, `password`, `verify_link`) VALUES ('$email','$password','$token')";
			if ($conn->query($sql) === true) {
				verification_link_sending($email, $link);
			} else {
				echo '<script>alert("Something Wrong")</script>';
			}
		}

		$conn->close();
	}
}

function verification_link_sending($recipient_email, $confirmation_link)
{
	require 'phpmailer/PHPMailerAutoload.php';
	//Setup
	$mail = new PHPMailer;
	$mail->isSMTP(); // Send using SMTP
	$mail->Host = 'mail.example.com'; // Set the SMTP server to send through
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = 'hello@example.com'; // SMTP username
	$mail->Password = '1{7zOItnpata'; // SMTP password
	$mail->SMTPSecure = 'ssl'; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
	$mail->Port = 465; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

	//Recipients
	$mail->setFrom('mail@example.com', "Example"); //from address
	//$mail->addAddress('krishna.urn@gmail.com'); // to mail
	//$mail->addAddress('ellen@example.com'); // Name is optional
	//$mail->addReplyTo('hello@biologycraze.com');
	// $mail->addCC('itssai91@gmail.com');

	$mail->addBCC($recipient_email);


	// Attachments
	// $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

	// Content
	$mail->isHTML(true); // Set email format to HTML
	$mail->Subject = 'Verification Link';
	$mail->Body =  'Confirmation Link: <b>' . $confirmation_link . ' </b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	if ($mail->send()) {
		echo "<script> alert('Confirmation Link Sent to Your Mail Address Please Verify') </script>";
	} else {
		echo "<script> alert('Confirmation Link does not send') </script>";
	}
}

?>