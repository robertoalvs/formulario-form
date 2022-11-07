<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8" />
    <title>Emailing Form Data</title>
	<style type="text/css">
	/* The CSS is here to keep the demo simple. As always, I recommend you put your CSS in an external file. */
	body {
		font-size: 100%;
		font-family: Arial, sans-serif;
	}
	</style>
</head>
<body>

<?php
/* --------------------------------------------------
IMPORTANT NOTE:

The sample PHP code in this file is simple by design, but as a result, it doesn't include the security checks that a bulletproof script would include. Use caution before including it on your own site. If you do intend to use a script like this, I recommend consulting PHP books and other resources to learn how to check submitted form values for malicious data before you write the values to the screen or to a database.
---------------------------------------------------- */


/* This is a very simple PHP script that outputs the name of each bit of information (that corresponds to the name attribute for that form field) along with the value that was sent with it right in the browser window, and then sends it all to an email address (once you've added it to the script; see the comments near the end regarding yourmail@yourdomain.com). 
*/


if (empty($_POST)) {
	print "<p>No data was submitted.</p>";
	print "</body></html>";
	exit();
}

/* Creates function that removes magic escaping, if it's been applied, from values and then removes extra newlines and returns to foil spammers. Thanks Larry Ullman! */
function clear_user_input($value) {
	if (get_magic_quotes_gpc()) $value=stripslashes($value);
	$value= str_replace( "\n", '', trim($value));
	$value= str_replace( "\r", '', $value);
	return $value;
}


/* Create body of email message by cleaning each field and then appending each name and value to it. */

$body ="Here is the data that was submitted:\n";

// Get value for each form field
foreach ($_POST as $key => $value) {
	$key = clear_user_input($key);
	$value = clear_user_input($value);
	
	if ($key == 'email_signup') { // True if an Email checkbox chosen	
		if (is_array($_POST['email_signup'])) {
			$body .= "$key: ";
			$counter =1;
			
			foreach ($_POST['email_signup'] as $value) {
				//Add comma and space until last element
				if (sizeof($_POST['email_signup']) == $counter) {
					$body .= "$value\n";
					break;
				} else {
					$body .= "$value, ";
					$counter += 1;
				}
			} // end foreach
		} else {
			$body .= "$key: $value\n";
		}
	} else { // field is not email_signup
		$body .= "$key: $value\n";
	}
} // end foreach

extract($_POST);

/* Get file upload picture name */
if(isset($_FILES['picture'])) {
	/*
		This basic script presents the name of the uploaded file only. To check the file size, file type (like JPG, GIF, or PNG), and actually upload a file to a folder, see the video tutorial at http://net.tutsplus.com/articles/news/diving-into-php/. The code explained in the video is also available for download from that URL. That page also includes links to a series of videos about using PHP.
	*/

	$picture_name = $_FILES['picture']['name'];

	// make sure name isn't blank
	if ($picture_name != '') {
		// add the picture name to the email body message
		$body .= "picture: $picture_name\n";
	}
}


/* Removes newlines and returns from $email and $name so they can't smuggle extra email addresses for spammers */
$email = clear_user_input($email);
$first_name = clear_user_input($first_name);

/* Create header that puts email in From box along with name in parentheses and sends Bcc to alternate address. Change yourmail@yourdomain.com to the Bcc email address you want to include. */
$from='From: '. $email . "(" . $first_name . ")" . "\r\n" . 'Bcc: robertoalvs@hotmail.com' . "\r\n";

// Creates intelligible subject line that also shows me where it came from
$subject = 'New Profile from Web Site';

/* Sends mail to the address below with the form data submitted above. Replace yourmail@yourdomain.com with the email address to which you want the data sent. */
mail ('robertoalvs@hotmail.com', $subject, $body, $from);
?>

<p>Thanks for signing up!</p>

</body>
</html>