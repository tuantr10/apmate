<?php
require("connect.php");
class method {
  function print_header() {/*header*/
  ?>
  <table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
  <td background-repeat='no-repeat' height='55' background='image/mainhead.jpg'  valign='bottom'>
  <p align='right'>
  <form align='right' action='index.php' method='post'>
  <b>Login user: <font color='white'><b><?php echo $_SESSION['ses_username']; ?>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </b></font>
  <input type='image' value='Logout' name='logout' src='image/b_logout_e.gif'></form>
  </p>
  </td>
  </tr>
  </table>
  <?php
  }

  function get_credits() {
    include("settings.php");
    $conn = mysqli_connect(constant('HOST'), constant('USERNAME'), constant('PASSWORD'), constant('DBNAME'));
    /* check connection */
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }
    $sql_getting_credits = "SELECT user_credit 
                            FROM users
                            WHERE user_id='".$_SESSION['ses_userid']."'";
    $query_getting_credits = mysqli_query($conn, $sql_getting_credits);
    while($row_getting_credits = mysqli_fetch_assoc($query_getting_credits)) {
      $_SESSION['ses_user_credit'] = $row_getting_credits['user_credit'];
    }

    echo "<table width='70%' align='right' border='1' cellpadding='1' cellspacing='0'"; 
    echo "<tr>";
    echo "<td align='center' bgcolor='F5F5F5'>";
    if($_COOKIE['language']=='en') {
      echo "Registered / Maximum Credits";
    } elseif($_COOKIE['language']=='ja') {
      echo "登録済み / 最大単位";
    }
    echo "</td>";
    echo "<td>";
    echo $_SESSION['ses_user_credit']."/30";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align='center'bgcolor='F5F5F5'>";
    echo "Registered Opposite Language Credits";
    echo "</td>";
    echo "<td align='center'>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</p>";
    echo "<br /><br /><br />";
  }

  function logout() {
    $temp_credit=0;
    $sql_reset_delete=" UPDATE records
                        SET record_deleted='0' 
                        WHERE user_id='".$_SESSION['ses_userid']."'
                        AND record_deleted='1'";
    mysqli_query($conn, $sql_reset_delete);
    $sql_delete_unapplied=" DELETE FROM records
                            WHERE record_applied='0'
                            AND user_id='".$_SESSION['ses_userid']."'";
    $query_delete_unapplied=mysqli_query($conn, $sql_delete_unapplied);

    $sql_get_total_credit=" SELECT DISTINCT record_credit,subject_code
                            FROM records
                            WHERE user_id='".$_SESSION['ses_userid']."'
                            AND record_applied='1'
                            AND record_deleted='0'";
    $query_get_total_credit=mysqli_query($conn, $sql_get_total_credit);
    while($row_get_total_credit=mysql_fetch_assoc($query_get_total_credit)) {
      $temp_credit += $row_get_total_credit['record_credit'];
    }
    $set_user_credit="  UPDATE users
              SET user_credit='".$temp_credit."'
              WHERE user_id='".$_SESSION['ses_userid']."'";
    mysqli_query($conn, $set_user_credit);
    header("location:index.php");
  }
}
?>