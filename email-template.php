<html>
  <body>
    <h2>New lead from <?php echo htmlspecialchars($formName); ?></h2>
    <ul>
      <li><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></li>
      <li><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></li>
      <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
      <li><strong>Service:</strong> <?php echo htmlspecialchars($service); ?></li>
    </ul>
    <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
  </body>
</html>
