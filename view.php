<?php
session_start();
require("connect.php");
require("method.php");
$method = new method;
$announce='';
/*Printing output*/

$dictionary = array(
  'en'=> array(
      "quarter"=>"Quarter",
      "day"=>"Day",
      "period"=>"Period",
      "grade"=>"Grade",
      "apm_field"=>"APM Field",
      "aps_field"=>"APS Field",
      "lecture_code"=>"Lecture code",
      "course"=>"course",
      "instructor"=>"Course Instructor",
      "language"=>"Language",
      "credit"=>"Credit",
      "vacancy"=>"Vacancy",
      "syllabus"=>"Syllabus"
    )
  ,
  'ja'=> array(
      "quarter"=>"学期",
      "day"=>"日",
      "period"=>"Period",
      "grade"=>"グレード",
      "apm_field"=>"APM 分野",
      "aps_field"=>"APS 分野",
      "lecture_code"=>"講義コード",
      "course"=>"講義名",
      "instructor"=>"担当教員",
      "language"=>"言語",
      "credit"=>"単位",
      "vacancy"=>"空席",
      "syllabus"=>"シラバス"
    )
);

function check_existing($child,$mom) {
  foreach($mom as $key=>$value) {
    if($value==$child) {
      return 1;
    }
  }
  return 0;
}

function printing($array)
{
$output="";
  
  foreach($array as $array_key=>$array_value)
  {
    $output .= "<tr>";
    $output .= "<td>".$array_value."</td>";
    $output .= "</tr>"."\n";
  }
  
return $output;
}

if(isset($_POST['print'])) {
  echo "<script language='JavaScript'>";
  echo "window.print();";
  echo "</script>";
}

/*if logout button clicked*/
if(isset($_POST['logout'])) {
  $temp_credit=0;
  $sql_reset_delete="  UPDATE records
            SET record_deleted='0' 
            WHERE user_id='".$_SESSION['ses_userid']."'
            AND record_deleted='1'";
            
  mysqli_query($conn, $sql_reset_delete);
  $sql_delete_unapplied="  DELETE FROM records
              WHERE record_applied='0'
              AND user_id='".$_SESSION['ses_userid']."'";
  $query_delete_unapplied=mysqli_query($conn, $sql_delete_unapplied);
  $sql_get_total_credit=" SELECT DISTINCT record_credit,subject_code
              FROM records
              WHERE user_id='".$_SESSION['ses_userid']."'
              AND record_applied='1'
              AND record_deleted='0'";
  $query_get_total_credit=mysqli_query($conn, $sql_get_total_credit);
  while($row_get_total_credit=mysqli_fetch_assoc($query_get_total_credit)) {
    $temp_credit+=$row_get_total_credit['record_credit'];
  }
  $set_user_credit="  UPDATE users
            SET user_credit='".$temp_credit."'
            WHERE user_id='".$_SESSION['ses_userid']."'";
  mysqli_query($conn, $set_user_credit);
  header("location:index.php");
}
/*if back button clicked*/
if(isset($_POST['back'])) {
  header("location:timetableadvance.php");
}

?>
<html>
<style type="text/css">
.strong
{
font-weight:bold;
}
</style>

<title>
View
</title>
<body>
<?php include_once("analyticstracking.php") ?>
<?php
$user_record=array();
$subject_list_code=array();
$text = "";
if($_COOKIE['language']=='en') {
  $subject_name_query='subject_name';
  $subject_instructor_query='subject_instructor';
} elseif ($_COOKIE['language']=='ja') {
  $subject_name_query='subject_name_jap';
  $subject_instructor_query='subject_instructor_jap';
}
$sql_get_details="  SELECT DISTINCT records.subject_quarter, records.subject_day, records.subject_period, records.subject_code, $subject_name_query, subject_language, $subject_instructor_query, subject_credit
          FROM subjects, records
          WHERE user_id = '".$_SESSION['ses_userid']."'
          AND records.subject_code = subjects.subject_code
          AND records.subject_quarter = subjects.subject_quarter
          AND records.subject_day = subjects.subject_day
          AND records.subject_period = subjects.subject_period
          AND record_deleted = '0'
          ORDER BY subjects.subject_id";

$query_get_details=mysqli_query($conn, $sql_get_details);
while($row_get_details=mysqli_fetch_assoc($query_get_details)) {
  $text .= "<tr>";

  foreach($row_get_details as $key=>$value) {
    if(check_existing($row_get_details['subject_code'],$subject_list_code)==1) {
      if($key=="subject_credit") {
        $text .= "<td></td>"."\n";
      } else {
        $text .= "<td>".$value."</td>"."\n";
      }
    } else {
      $text .= "<td>".$value."</td>"."\n";
    }
  }
  $text .="</tr>";
  $user_record[]=$text;
  $text="";
  $subject_list_code[]=$row_get_details['subject_code'];
}

echo "<div style='position: absolute; left: 15%; width: 995px; height: 55px'>";
/** Site's banner
 */
$method->print_header();
/** End site's banner
 */
 
echo "<p align='center'><font color='656565' size='5'><b>COURSE REGISTRATION CONFIRMATION</b></font></p>";


/** Course's list
 *
 */
echo "<table align='center' width='100%' border='1' cellpadding='3' cellspacing='0'>";
echo "<tr class='strong' bgcolor='F5F5F5'>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['quarter'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['day'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['period'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['lecture_code'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['course'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['language'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['instructor'];
echo "</td>";
echo "<td>";
echo $dictionary[$_COOKIE['language']]['credit'];
echo "</td>";
echo "</tr>";

echo printing($user_record);

/** Get user total credits
 *
 */
  $current_credit=0;
  $sql_get_current_credit="  SELECT DISTINCT subject_code,record_credit
                FROM records
                WHERE user_id='".$_SESSION['ses_userid']."'
                AND record_applied='1'
                AND record_deleted='0'";
  $query_get_current_credit=mysqli_query($conn, $sql_get_current_credit);
  while($row_get_current_credit=mysqli_fetch_assoc($query_get_current_credit))
  {
    $current_credit+=$row_get_current_credit['record_credit'];
  }
  $query_return_credit="  UPDATE users,records
              SET user_credit='".$current_credit."'
              WHERE users.user_id='".$_SESSION['ses_userid']."'";
  mysqli_query($conn, $query_return_credit);
  
  $sql_get_current_credit="  SELECT user_credit
                FROM users
                WHERE user_id='".$_SESSION['ses_userid']."'";
  $query_get_current_credit=mysqli_query($conn, $sql_get_current_credit);
  while($row_get_current_credit = mysqli_fetch_assoc($query_get_current_credit)) {
    $current_credit=$row_get_current_credit['user_credit'];
  }

/** End get user total credits
 *
 */
echo "<tr>";
echo "<td align='center' bgcolor='F5F5F5' colspan='7'>";
echo "<b>Total Registered Credits&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</b>";
echo "</td>";
echo "<td>";
echo "<b>".$current_credit."</b>";
echo "</td>";
echo "</tr>";
echo "</table>";

/** End site's table
*
*/


echo "<br />";
if (empty($_SESSION['ses_missed_subject'])) {
  echo "<div style='height:48%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
  echo "<font color='2c2082'>Click 'Print' to print a copy of this screen for your records<br/>Miracale will happen :D</font>";
  echo "</div>";
} else {
  echo "<div style='height:48%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
  foreach ($_SESSION['ses_missed_subject'] as $_key=>$_value) {
    echo "<font color='red'>There are no opening left for the course[".$_value."]</font><br/>";
  }
  echo "</div>";
}
echo "<br/>";
echo "<form action='view.php' method='post'>";
echo "<input type='image' value='print' name='print' src='image/print.png'>";
echo "</form>";
echo "<br />";
echo "<br />";

echo "</div>";

?>
</body>
</header>
</html>