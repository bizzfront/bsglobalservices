<table style='width: 400px; margin: 0 auto;'>
  <tr>
    <td style='background-color: #f2f2f2; text-align: center;'>
      <img src='https://bsglobalservices.com/img/bsglobalservicesemail2.jpg' alt='B&S Global Services'>
    </td>
  </tr>
  <tr>
    <td style='background-color: #ffffff; text-align: left; font-family: Arial, Helvetica, sans-serif;'>
      Form: <?php echo htmlspecialchars($formName); ?><br>
      Name: <?php echo htmlspecialchars($name); ?><br>
      Email: <?php echo htmlspecialchars($email); ?><br>
      Phone: <?php echo htmlspecialchars($phone); ?><br>
      ZIP: <?php echo htmlspecialchars($zip); ?><br>
      Address: <?php echo htmlspecialchars($address); ?><br>
      Service: <?php echo htmlspecialchars($service); ?><br>
      Service Subject: <?php echo htmlspecialchars($service_detail); ?><br>
      Detail/Message: <?php echo nl2br(htmlspecialchars($message)); ?><br><br>
    </td>
  </tr>
  <tr>
    <td style='background-color: #f2f2f2; text-align: center;'>
      Official Website: <a href='https://bsglobalservices.com'>www.bsglobalservices.com</a><br>
      <small>Do not reply this email</small>
    </td>
  </tr>
  <tr>
    <td style='background-color: #f2f2f2; text-align: center;'>
      <small><small>Session ID: <?php echo htmlspecialchars($sessionID); ?></small></small>
    </td>
  </tr>
</table>
