<?php
session_start();
//header('Content-Type: text/html; charset=utf-8');
/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 1.01 of the Secret Technical Group' APU Course Registration Simulation.
 *
 * @category   Simulation
 * @package    PackageName
 * @author     TRAN Tuan Dung <mcafee92@gmail.com>
 * @copyright  2012 The Secret Technical Group
 * @license    Not available
 * @version    1.01
 * @url      http://apmate.net
 * @since      File available since Release 1.00
 */

/**
* Connecting to the database
*/
require("connect.php");
$_SESSION['ses_period']='';
$_SESSION['ses_quarter']='';
$_SESSION['ses_day']='';

/**
* Declaring variables
* @i,j,k  Using in 3 dimension matrix (timetable)
* @day    Transform day from number to string
* @quarter  Transform quarter from number to string
* @announce  Array that store all the error or announce which will display on the web
*/
$day = array(2 => 'Mon','Tue','Wed','Thu','Fri','Sat');
$quarter = array(1 => '1Q', '2Q','Quarter');
$i = $j = $k = 0;
$username = $_SESSION['ses_username'];

/**
* (1)Description:  Handling apply button
* Allow user to apply and re-direct to view.php if the registered is less than 18 credits
*(1.1)If the chosen subject's vacancy reach 0 before the user click this button, that subject is out of the list
*(1.2)Decrease all the vacancy in subjects table by 1 which registered by the user
*(1.3)Set record_applied to 1
*/
require("method.php");
$method = new method;

if (isset($_POST['apply'])) {
  if($_SESSION['ses_user_credit']<=30) {
/**
(1.1) If the chosen subject's vacancy reach 0 before 
* the user click this button, that subject is out of the list
@ deleted_subject  Subject that unsucessfully registered
*/

/**stolen code need to move to view.php*/
    $_SESSION['ses_missed_subject']=array();
    $mysql_check_stolen = " SELECT DISTINCT subjects.subject_code,subject_name,subject_vacancy,subject_credit 
                            FROM subjects,records
                            WHERE user_id='".$_SESSION['ses_userid']."'
                            AND records.subject_code = subjects.subject_code
                            AND records.subject_quarter = subjects.subject_quarter
                            AND records.subject_day = subjects.subject_day
                            AND records.subject_period = subjects.subject_period
                            AND records.record_applied='0'
                            AND subject_vacancy='0'";
    $query_check_stolen = mysqli_query($conn, $mysql_check_stolen);
    while($row_check_stolen = mysqli_fetch_assoc($query_check_stolen)) {
      if($row_check_stolen['subject_vacancy'] == 0) {  
        $sql_delete_stolen_subject = "DELETE FROM records
                                      WHERE user_id='".$_SESSION['ses_userid']."'
                                      AND subject_code='".$row_check_stolen['subject_code']."'";
        mysqli_query($conn, $sql_delete_stolen_subject);
        $_SESSION['ses_missed_subject'][] = $row_check_stolen['subject_code']." ".$row_check_stolen['subject_name'];
      }
    }
/** Check stolen course
 */


/*End(1.1)*/

/*BUG Pass deleted subject to view.php to warn the user that they havent successfully chose that subject since other students are faster then them*/

/** (1.2) Decrease all the vacancy in subjects table by 1
 * 
 * which registered by the user  
 */
    $sql_decrease_vacancy=" UPDATE subjects,records
                            SET subject_vacancy=subject_vacancy-1
                            WHERE subjects.subject_id=records.subject_id
                            AND records.user_id='".$_SESSION['ses_userid']."'
                            AND records.record_applied='0'";
    mysqli_query($conn, $sql_decrease_vacancy);
/* End(1.2)*/
    
/** (1.3) Set record_applied of chosen subject in records to 1 
 */
    $sql_set_applied="UPDATE records
                      SET record_applied='1'
                      WHERE record_applied='0'
                      AND user_id='".$_SESSION['ses_userid']."'";
    mysqli_query($conn, $sql_set_applied);
/**
 * record_deleted
 */ 
    $sql_increase_vacancy=" UPDATE subjects,records
                            SET subject_vacancy=subject_vacancy+1
                            WHERE subjects.subject_id=records.subject_id
                            AND records.user_id='".$_SESSION['ses_userid']."'
                            AND record_deleted='1'";
    mysqli_query($conn, $sql_increase_vacancy);  
    $sql_delete_deleted_record="DELETE FROM records
                                WHERE user_id='".$_SESSION['ses_userid']."'
                                AND record_deleted='1'";
    mysqli_query($conn, $sql_delete_deleted_record);
/* End(1.3)
 */
    header("location:view.php");
  } else {
    $_SESSION['ses_announce'] = 1;
    header("location:timetableadvance.php#error");
  }
}
/* End (1) handling apply button
 */

/** Handling edit button
 * if clicked Re-direct to select.php
 * Appear in every single period's cell
 */

for ($i=1;$i<=6;++$i) {
  for ($j=1;$j<=2;++$j) {
    for ($k=2;$k<=7;++$k) {
      if (isset($_POST[$i.$j.$k])) {
        $_SESSION['ses_period']=$i;
        $_SESSION['ses_quarter']=$j;
        $_SESSION['ses_day']=$k;
        $_SESSION['ses_announce']=0;
        header("location:select.php");
      }
    }
  }
}
/*
* End handling edit button
*/

/** Handling logout button
 * Delete all subject which hasn't been applied
 */
if(isset($_POST['logout'])) {
  $method->logout(); 
}
/** End handling logout button
* 
*/
?>
<?php include_once("analyticstracking.php") ?>
<?php
/**
* Display the content of the website when user logged in probably.
* If the page accessed with out the logging in process, 'Hacking?' will printed out.
* Using <div> tag to make the site centered.
* (1) Banner
* (2) Apply button
* (3) Printing and describing the 3 icons
* (4) Getting user credit(s) number
* (5) Printing status timetable
* (6) Printing timetable
*/
if ($username!= NULL) {
  ?>
  <div class="wrapper">
    <!--(1) Printing banner -->
    <div class="ttb_header">
    <?php $method->print_header()?>
    </div>
    <!-- End (1)-->

    <!-- (2) Printing Apply button -->
    <div class="ttb_top">
    <form align='right' action='timetableadvance.php' method='post'>
    <input type='image' value='apply' name='apply' src='image/apply.png'> 
    </form>
    </p>
    </div>
<!-- End (2) -->

    <div class="ttb_top_2">
     <!-- (3) Printing and describing the 3 icons -->
      <div class="ttb_top_2_left">
      <p align="center">
      <?php if($_COOKIE['language']=='en'): ?>
        <img border='0' src='image/ico_mitoroku.gif' />&nbspUnregistered&nbsp&nbsp
        <img border='0' src='image/ico_sumi.gif'  />&nbspRegistered&nbsp&nbsp
        <img border='0' src='image/ico_fuka.gif'  />&nbspUnmodifiable
      <?php elseif($_COOKIE['language']=='ja'):?>
        <img border='0' src='image/ico_mitoroku.gif' />&nbsp未登録&nbsp&nbsp
        <img border='0' src='image/ico_sumi.gif'  />&nbsp登録済み&nbsp&nbsp
        <img border='0' src='image/ico_fuka.gif'  />&nbsp編集不能
      <?php endif ?>
      </p>
      </div>
    <!-- End (3) -->
      <div class="ttb_top_2_right">

    <!--(4) Get user credit(s) number-->
      <?php $method->get_credits(); ?>
      </div>
    </div>


<!--
* (6) Printing timetable
* (6.1) First row of timetable
* (6.2) The rest of timetable
* 2 stable subject is located in Wednesday 1st and 2nd period


/**
* (6.1) Printing First row of timetable

-->
<?php if($_COOKIE['language']=='en'): ?>
<strong>* Timetable details may change in the future.<br />
* Note that session subjects are not included <br />
* Please use Google Chrome</strong>
<?php elseif($_COOKIE['language']=='ja'): ?>
<strong>* 時刻表は、将来変更される可能性があります。<br />
* セッションの講義は、こちらのサイトにはございません。 <br />
* Google Chromeをご利用ください</strong>
<?php endif ?>
    <link rel='stylesheet' href='style/style.css' type='text/css' /> 
    <table style="font-size:11px;" border='1' align='center' width='100%' cellpadding='3' cellspacing='0'>
    <tr>
    <th width='12.5%' bgcolor='F5F5F5' scope='col'><font color='#848484'>Period/Day</font></th>
    <th width='12.5%' bgcolor='F5F5F5' scope='col'></th>
    <?php if($_COOKIE['language']=='en'): ?>
    <th class="ttb_table" scope='col'>Monday</th>
    <th class="ttb_table" scope='col'>Tuesday</th>
    <th class="ttb_table" scope='col'>Wednesday</th>
    <th class="ttb_table" scope='col'>Thurday</th>
    <th class="ttb_table" scope='col'>Friday</th> 
    <th class="ttb_table" scope='col'>Saturday</th> 
    <?php elseif($_COOKIE['language']=='ja'): ?>
    <th class="ttb_table" scope='col'>月</th>
    <th class="ttb_table" scope='col'>火</th>
    <th class="ttb_table" scope='col'>水</th>
    <th class="ttb_table" scope='col'>木</th>
    <th class="ttb_table" scope='col'>金</th>
    <th class="ttb_table" scope='col'>土</th> 
    <?php endif ?>
    </tr>
<?php
/**
* End (6.1)
*/

/**
* (6.2) Printing the rest of timetable
*/
  for ($i=1; $i<=6; ++$i) {
    for ($j=1; $j<=2; ++$j) {
      echo "<tr height='120'>";
      if ($j==1) {
        echo "<th bgcolor='F5F5F5' rowspan='2' scope='row'><font color='7B6F81'>".$i."</font></th>";
      }
      echo "<th bgcolor='F5F5F5' scope='row'><font color='7B6F81'>Q".$j."</font></th>";
      for ($k=2;$k<=7;++$k) {
        echo "<td>";
        echo "<form action='timetableadvance.php' method='post'>"; 
          $sql_generate_edit_button="  SELECT DISTINCT record_id 
                        FROM records 
                        WHERE subject_quarter IN('".$quarter[$j]."','Quarter')
                        AND subject_day='".$day[$k]."' 
                        AND subject_period='".$i."'
                        AND user_id='".$_SESSION['ses_userid']."'
                        AND record_deleted='0'";
          $query_generate_edit_button=mysqli_query($conn, $sql_generate_edit_button);
          if(mysqli_num_rows($query_generate_edit_button)>0) {
            echo "<br />";
            echo "<br />";
            echo "<input  type='image' name='".$i.$j.$k."' value='Edit' src='image/ico_sumi.gif'>";
          } else {
            echo "<input  type='image' name='".$i.$j.$k."' value='Edit' src='image/ico_mitoroku.gif'>";
          }

          //OUTPUT SUBJECTS' NAME
          $sql_generate_subject="  SELECT DISTINCT records.subject_code,subject_instructor,subject_name,subjects.subject_id,subjects.subject_name_jap,subjects.subject_instructor_jap
                      FROM subjects,records
                      WHERE user_id='".$_SESSION['ses_userid']."'
                      AND records.subject_code=subjects.subject_code
                      AND records.subject_quarter IN('".$quarter[$j]."','Quarter')
                      AND records.subject_period='".$i."'
                      AND records.subject_day='".$day[$k]."'
                      AND records.subject_id=subjects.subject_id
                      AND record_deleted= '0'";
          $query_generate_subject = mysqli_query($conn, $sql_generate_subject);
          $counter = 0;
          while ($row_generate_subject = mysqli_fetch_assoc($query_generate_subject)) {
          if($_COOKIE['language'] == 'en') {
            $subject_name = $row_generate_subject['subject_name'];
          } else if($_COOKIE['language'] == 'ja') {
            $subject_name = $row_generate_subject['subject_name_jap'];
          }
          if ($counter == 0) {
            echo "<font color='#0000FF' size='2'>".$row_generate_subject['subject_code']."</font><br />";
            if (str_word_count($subject_name)>5) {
              echo "<font color='#0000FF'><b>";
              $new_string=str_word_count($subject_name,1);
              foreach($new_string as $key => $value) {
                if ($key==3) {
                  echo $value."<br/>";
                } else {
                  echo $value." ";
                }
              }
              echo "</b></font>";
            } else {
              echo "<font color='#0000FF'><b>".$subject_name."</b></font>";
            }
            echo "<br/>";
            if ($_COOKIE['language']=='en') {
              echo "<font color='#0000FF' size='2'>".$row_generate_subject['subject_instructor']."</font><br />";
            } else {
              echo "<font color='#0000FF' size='2'>".$row_generate_subject['subject_instructor_jap']."</font><br />";
            }
            ++$counter;
          }
          }
          echo "</form>";
          echo "</td>";
      }
      echo "</tr>";
    }
  }
echo "</table><br />";
echo "<br/>";

echo "<div id='error'>";
if ($_SESSION['ses_user_credit']>30) {
  if($_COOKIE['language']=='en') {
    echo "<div style='height:10%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
    echo "<font color='red'>You have exceeded the credit limit.";
    echo "<br />";
    echo "Please cancel some courses</font>";
    echo "</div>";
  } else if($_COOKIE['language']=='ja') {
    echo "<div style='height:10%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
    echo "<font color='red'>最大単位数を超えました.";
    echo "<br />";
    echo "Please cancel some courses</font>";
    echo "</div>";
  }
} else {
  echo "<div style='height:10%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
  echo "<font color='2c2082'>Please choose the day and period you would like to register<br/>Click the pencil or the purple note to continue~</font>";
  echo "</div>";
}
echo "</div>";
echo "<br/>";

echo "<form align='center' action='timetableadvance.php' method='post'>
    <input type='image' value='apply' name='apply' src='image/apply.png'>
    <input type='image'  value='Logout' name='logout' src='image/b_logout_e.gif'></form>";
echo "<br/>";
echo "<br/>";
echo "</div>";
/**
 * End (6.2)
 */
} else {
  echo "Hackin'?";
}
?>